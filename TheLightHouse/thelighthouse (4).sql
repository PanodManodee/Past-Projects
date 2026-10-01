-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 18, 2025 at 02:51 AM
-- Server version: 5.7.24
-- PHP Version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `thelighthouse`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `cancel_booking` (IN `p_booking_id` INT)   BEGIN
    DECLARE v_customer_id INT;
    DECLARE v_session_id INT;
    DECLARE v_price INT;
    DECLARE v_exists INT;

    -- Check if booking exists
    SELECT COUNT(*) INTO v_exists
    FROM booking
    WHERE booking_id = p_booking_id;

    IF v_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Booking ID does not exist.';
    END IF;

    -- Get customer_id and session_id from booking
    SELECT customers, sessions
    INTO v_customer_id, v_session_id
    FROM booking
    WHERE booking_id = p_booking_id;

    -- Get price from the session
    SELECT price INTO v_price
    FROM session
    WHERE session_id = v_session_id;

    IF v_price IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Could not determine session price.';
    END IF;

    START TRANSACTION;


        -- 2. Delete booking
        DELETE FROM booking
        WHERE booking_id = p_booking_id;

    COMMIT;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `cancel_session` (IN `p_session_id` INT)   BEGIN
    DECLARE v_exists INT DEFAULT 0;

    SELECT COUNT(*) INTO v_exists
    FROM session
    WHERE session_id = p_session_id;

    IF v_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Session ID does not exist.';
    END IF;

    START TRANSACTION;

    DELETE FROM booking
    WHERE sessions = p_session_id;

    DELETE FROM session
    WHERE session_id = p_session_id;

    COMMIT;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `edit_concert_full` (IN `p_username` VARCHAR(50), IN `p_password` VARBINARY(255), IN `p_session_id` INT, IN `p_new_name` VARCHAR(50), IN `p_new_date` DATETIME, IN `p_new_price` INT)   BEGIN
    DECLARE v_performer_id INT;
    DECLARE v_session_performer INT;
    DECLARE v_exists INT;

    -- 1. Authenticate performer
    SELECT performer_id INTO v_performer_id
    FROM performer
    WHERE username = p_username
      AND password = p_password
    LIMIT 1;

    IF v_performer_id IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid performer username or password.';
    END IF;


    -- 2. Check if session exists
    SELECT COUNT(*) INTO v_exists
    FROM session
    WHERE session_id = p_session_id;

    IF v_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Session does not exist.';
    END IF;


    -- 3. Check if this performer is allowed to edit this session
    SELECT performers INTO v_session_performer
    FROM session
    WHERE session_id = p_session_id;

    IF v_session_performer != v_performer_id THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'You are not authorized to edit this concert.';
    END IF;


    -- 4. Update session details
    UPDATE session
    SET 
        session_name = p_new_name,
        session_date = p_new_date,
        price = p_new_price
    WHERE session_id = p_session_id;




END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `process_payment` (IN `p_customer_id` INT, IN `p_session_id` INT, IN `p_zone` VARCHAR(50))   BEGIN
    DECLARE v_balance INT;
    DECLARE v_price INT;
    DECLARE v_performer INT;
    DECLARE v_session_name VARCHAR(50);
    DECLARE v_session_date DATETIME;
    DECLARE v_zone_bookings INT;
    DECLARE v_session_bookings INT;
    DECLARE v_zone_limit INT DEFAULT 200;
    DECLARE v_session_limit INT DEFAULT 1200;
    DECLARE err_msg VARCHAR(255);
    
    -- Start transaction to ensure atomicity
    START TRANSACTION;
    
    -- Get customer balance
    SELECT balance INTO v_balance
    FROM customer
    WHERE customer_id = p_customer_id
    LIMIT 1;
    
    IF v_balance IS NULL THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid customer ID.';
    END IF;
    
    -- Get session details (ADDED session_date)
    SELECT price, performers, session_name, session_date 
    INTO v_price, v_performer, v_session_name, v_session_date
    FROM session
    WHERE session_id = p_session_id
    LIMIT 1;
    
    IF v_price IS NULL THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Invalid session ID.';
    END IF;
    
    -- Check if customer has enough balance
    IF v_balance < v_price THEN
        ROLLBACK;
        SET err_msg = CONCAT('Insufficient balance: have ', v_balance, ', need ', v_price);
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = err_msg;
    END IF;
    
    -- Check zone capacity
    SELECT COUNT(*) INTO v_zone_bookings
    FROM booking
    WHERE sessions = p_session_id
      AND zone = p_zone;
    
    IF v_zone_bookings >= v_zone_limit THEN
        ROLLBACK;
        SET err_msg = CONCAT('Zone "', p_zone, '" is full (max ', v_zone_limit, ').');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = err_msg;
    END IF;
    
    -- Check total session capacity
    SELECT COUNT(*) INTO v_session_bookings
    FROM booking
    WHERE sessions = p_session_id;
    
    IF v_session_bookings >= v_session_limit THEN
        ROLLBACK;
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Session fully booked (max 1200).';
    END IF;
    
    -- Deduct balance
    UPDATE customer
    SET balance = balance - v_price
    WHERE customer_id = p_customer_id;
    
    -- Insert payment record (FIXED: Added payment_method and date)
    INSERT INTO payment (fname, lname, amount, customers, payment_method, date)
    SELECT 
        fname, 
        lname, 
        v_price, 
        p_customer_id,
        'Ticket Payment',
        v_session_date
    FROM customer
    WHERE customer_id = p_customer_id;
    
    -- Insert booking record
    INSERT INTO booking (fname, lname, customers, sessions, performers, s_name, zone, `datetime`)
    SELECT 
        fname, 
        lname, 
        p_customer_id, 
        p_session_id, 
        v_performer, 
        v_session_name, 
        p_zone,
        v_session_date
    FROM customer
    WHERE customer_id = p_customer_id;
    
    -- Commit everything
    COMMIT;
    
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `topup_balance` (IN `p_customer_id` INT, IN `p_amount` INT, IN `p_pay_meth` ENUM('Prompt Pay','True Money Wallet','Credit card','Paypal'), IN `p_date` DATETIME)  DETERMINISTIC BEGIN
    DECLARE v_balance INT;
    DECLARE v_fname VARCHAR(50);
    DECLARE v_lname VARCHAR(50);
    DECLARE err_msg VARCHAR(255);
    
    -- Validate top-up amount
    IF p_amount <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Top-up amount must be greater than zero.';
    END IF;
    
    -- Check customer exists and get details
    SELECT balance, fname, lname 
    INTO v_balance, v_fname, v_lname
    FROM customer
    WHERE customer_id = p_customer_id
    LIMIT 1;
    
    IF v_balance IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid customer ID.';
    END IF;
    
    -- Update balance
    UPDATE customer
    SET balance = balance + p_amount
    WHERE customer_id = p_customer_id;
    
    -- Insert payment record (FIXED: Added fname and lname)
    INSERT INTO payment (fname, lname, customers, amount, payment_method, `date`)
    VALUES (v_fname, v_lname, p_customer_id, p_amount, p_pay_meth, p_date);
    
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varbinary(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `fname`, `lname`, `username`, `password`) VALUES
(1, 'admin1', 'testadmin', 'root', 0x243279243130246757436a5558694b4772574b52534a6c5742366d666569493254544a37412f4a63536566396474626957634157786a3370474e6e57);

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `booking_id` int(11) NOT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `customers` int(11) NOT NULL,
  `sessions` int(11) NOT NULL,
  `performers` int(11) NOT NULL,
  `s_name` varchar(50) DEFAULT NULL,
  `zone` varchar(50) DEFAULT NULL,
  `datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`booking_id`, `fname`, `lname`, `customers`, `sessions`, `performers`, `s_name`, `zone`, `datetime`) VALUES
(6, 'qwe', 'qwe', 6, 4, 7, 'asd', 'C', '2025-11-17 16:00:00'),
(7, 'qwe', 'qwe', 6, 4, 7, 'asd', 'A', '2025-11-17 16:00:00'),
(8, 'qwe', 'qwe', 6, 4, 7, 'asd', 'B', '2025-11-17 16:00:00'),
(9, 'qwe', 'qwe', 6, 4, 7, 'asd', 'C', '2025-11-17 16:00:00'),
(10, 'qwe', 'qwe', 6, 4, 7, 'asd', 'C', '2025-11-17 16:00:00'),
(11, 'qwe', 'qwe', 6, 4, 7, 'asd', 'C', '2025-11-17 16:00:00'),
(12, 'qwe', 'qwe', 6, 4, 7, 'asd', 'F', '2025-11-17 16:00:00'),
(13, 'qwe', 'qwe', 6, 4, 7, 'asd', 'E', '2025-11-17 16:00:00'),
(14, 'qwe', 'qwe', 6, 4, 7, 'asd', 'B', '2025-11-17 16:00:00'),
(15, 'qwe', 'qwe', 6, 4, 7, 'asd', 'D', '2025-11-17 16:00:00'),
(16, 'qwe', 'qwe', 6, 4, 7, 'asd', 'C', '2025-11-17 16:00:00');

--
-- Triggers `booking`
--
DELIMITER $$
CREATE TRIGGER `check_cancellation_period` BEFORE DELETE ON `booking` FOR EACH ROW BEGIN
    DECLARE session_datetime DATETIME;
    DECLARE now_datetime DATETIME;
    DECLARE diff_days INT;
    DECLARE err_msg VARCHAR(255);

    SELECT session_date INTO session_datetime
    FROM session
    WHERE session_id = OLD.sessions;

    SET now_datetime = NOW();

    SET diff_days = DATEDIFF(session_datetime, now_datetime);

    IF diff_days < 7 THEN
        SET err_msg = CONCAT('Cannot cancel booking. Session starts in ', diff_days, ' day(s); cancellations must be made at least 7 days in advance.');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = err_msg;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_zone_and_session_availability` BEFORE INSERT ON `booking` FOR EACH ROW BEGIN
    DECLARE zone_bookings INT DEFAULT 0;
    DECLARE session_bookings INT DEFAULT 0;
    DECLARE zone_limit INT DEFAULT 200;
    DECLARE session_limit INT DEFAULT 1200;
    DECLARE err_msg VARCHAR(255);

    SELECT COUNT(*) INTO zone_bookings
    FROM booking
    WHERE sessions = NEW.sessions
      AND zone = NEW.zone;

    SELECT COUNT(*) INTO session_bookings
    FROM booking
    WHERE sessions = NEW.sessions;

    IF zone_bookings >= zone_limit THEN
        SET err_msg = CONCAT('Zone "', NEW.zone, '" is fully booked.');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = err_msg;
    END IF;

    IF session_bookings >= session_limit THEN
        SET err_msg = CONCAT('This session has been fully booked');
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = err_msg;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `refund_after_cancellation` AFTER DELETE ON `booking` FOR EACH ROW BEGIN
    DECLARE v_price INT;

    SELECT price INTO v_price
    FROM session
    WHERE session_id = OLD.sessions;

    UPDATE customer
    SET balance = balance + v_price
    WHERE customer_id = OLD.customers;

END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `balance` int(11) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varbinary(255) DEFAULT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `admins` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `balance`, `username`, `password`, `fname`, `lname`, `dob`, `admins`, `status`) VALUES
(6, 0, 'qwe', 0x24327924313024775936306f76617246414f4b7a756631665a34456375736453644e6b62614e725859683952496b454c4a4d644a694d463747494147, 'qwe', 'qwe', '2025-11-17', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `equipment_id` int(11) NOT NULL,
  `req_detail` varchar(255) DEFAULT NULL,
  `session` int(11) NOT NULL,
  `admins` int(11) DEFAULT NULL,
  `req_date` datetime NOT NULL,
  `req_approval` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`equipment_id`, `req_detail`, `session`, `admins`, `req_date`, `req_approval`) VALUES
(3, 'asd', 4, NULL, '2025-11-17 16:00:00', 0),
(4, 'qwe', 5, NULL, '2025-11-18 16:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `payment_id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `amount` int(11) DEFAULT NULL,
  `customers` int(11) DEFAULT NULL,
  `payment_method` enum('Prompt Pay','True Money Wallet','Credit card','Paypal','Ticket Payment') NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`payment_id`, `fname`, `lname`, `amount`, `customers`, `payment_method`, `date`) VALUES
(6, 'qwe', 'qwe', 500, 6, 'Prompt Pay', '2025-11-17 15:17:45'),
(7, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-25 16:00:00'),
(8, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-17 16:00:00'),
(9, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-17 16:00:00'),
(10, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-17 16:00:00'),
(11, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-17 16:00:00'),
(14, 'qwe', 'qwe', 500, 6, 'Prompt Pay', '2025-11-17 16:35:57'),
(15, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-17 16:00:00'),
(16, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-17 16:00:00'),
(17, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-17 16:00:00'),
(18, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-17 16:00:00'),
(19, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-17 16:00:00'),
(22, 'qwe', 'qwe', 100, 6, 'Prompt Pay', '2025-11-17 16:42:19'),
(24, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-17 16:00:00'),
(26, 'qwe', 'qwe', 100, 6, 'Ticket Payment', '2025-11-17 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `performer`
--

CREATE TABLE `performer` (
  `performer_id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varbinary(255) DEFAULT NULL,
  `fname` varchar(50) DEFAULT NULL,
  `lname` varchar(50) DEFAULT NULL,
  `p_alias` varchar(50) DEFAULT NULL,
  `payment_method` enum('Prompt Pay','Credit Card','True Money Wallet','Paypal') DEFAULT NULL,
  `admins` int(11) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `performer`
--

INSERT INTO `performer` (`performer_id`, `username`, `password`, `fname`, `lname`, `p_alias`, `payment_method`, `admins`, `status`) VALUES
(7, 'qwe', 0x2432792431302469506b4867356743616d347a5569724a42585136734f4f6b596f7231634a4e4752594a4b7a4d4f52316f5547744e6e30593255504b, 'qwe', 'qwe', 'qwe', 'Prompt Pay', 1, 1),
(8, 'qwe', 0x2432792431302470677162697747395766495a7a3735696b4a7057452e6d6643666541352e507677686753395a634c4133597a666b48484a642f2f75, 'qwe', 'qwe', 'qwe', 'Prompt Pay', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `session_id` int(11) NOT NULL,
  `session_name` varchar(50) DEFAULT NULL,
  `session_date` datetime DEFAULT NULL,
  `p_alias` varchar(50) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `performers` int(11) DEFAULT NULL,
  `admins` int(11) DEFAULT NULL,
  `approval` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`session_id`, `session_name`, `session_date`, `p_alias`, `price`, `performers`, `admins`, `approval`) VALUES
(4, 'asd', '2025-11-17 16:00:00', 'qwe', 100, 7, 1, 1),
(5, 'qwe', '2025-11-18 16:00:00', 'qwe', 100, 8, NULL, 0);

--
-- Triggers `session`
--
DELIMITER $$
CREATE TRIGGER `validate_time_slot` BEFORE INSERT ON `session` FOR EACH ROW BEGIN
    DECLARE conflict_count INT;
    
    SELECT COUNT(*) INTO conflict_count
    FROM session
    WHERE 
        NEW.session_date < DATE_ADD(session_date, INTERVAL 2 HOUR)
        AND 
        DATE_ADD(NEW.session_date, INTERVAL 2 HOUR) > session_date;
    
    IF (conflict_count > 0) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Time slot overlaps with an existing session';
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `fk_customers3` (`customers`),
  ADD KEY `fk_session3` (`sessions`),
  ADD KEY `fk_performer3` (`performers`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `admins` (`admins`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`equipment_id`),
  ADD KEY `fk_admins2` (`admins`),
  ADD KEY `fk_session2` (`session`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_customers` (`customers`);

--
-- Indexes for table `performer`
--
ALTER TABLE `performer`
  ADD PRIMARY KEY (`performer_id`),
  ADD KEY `fk_admin` (`admins`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `fk_performers` (`performers`),
  ADD KEY `fk_admins` (`admins`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `equipment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `performer`
--
ALTER TABLE `performer`
  MODIFY `performer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_customers3` FOREIGN KEY (`customers`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_performer3` FOREIGN KEY (`performers`) REFERENCES `performer` (`performer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_session3` FOREIGN KEY (`sessions`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`admins`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `fk_admins2` FOREIGN KEY (`admins`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_session2` FOREIGN KEY (`session`) REFERENCES `session` (`session_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `fk_customers` FOREIGN KEY (`customers`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `performer`
--
ALTER TABLE `performer`
  ADD CONSTRAINT `fk_admin` FOREIGN KEY (`admins`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `session`
--
ALTER TABLE `session`
  ADD CONSTRAINT `fk_admins` FOREIGN KEY (`admins`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_performers` FOREIGN KEY (`performers`) REFERENCES `performer` (`performer_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
