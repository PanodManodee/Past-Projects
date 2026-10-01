<?php
session_start();
require_once("dbconn.php");
$cid = $_SESSION["customer_id"];
$sid = $_SESSION["session"];
$zone = $_POST["zone"];
$q = "select * from session where session_id = $sid;";
$result = mysqli_query($mysqli, $q);
if (!$result) {
    echo "Select failed. Error:" . $mysqli->error;
    exit;
}
$row = mysqli_fetch_array($result);
// Convert to DateTime object
$datetime = new DateTime($row["session_date"]);
// Format the date and times
$date = $datetime->format('d/m/Y');           // 22/11/2025
$start_time = $datetime->format('H:i');       // 18:00
// Add 2 hours for end time
$datetime->modify('+2 hours');
$end_time = $datetime->format('H:i');

if (isset($_POST['submit_payment'])) {
    try {
        $stmt = $mysqli->prepare('CALL process_payment(?, ?, ?)');

        if (!$stmt) {
            throw new Exception("Database error: " . $mysqli->error);
        }

        $stmt->bind_param('iis', $cid, $sid, $zone);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }

        $stmt->close();

        header("Location: myticket.php?success=1");
        exit();

    } catch (Exception $e) {
        // Catch the error and display it nicely
        $error_message = $e->getMessage();

        // Store error in session to display after redirect
        $_SESSION['payment_error'] = $error_message;
    }
}

// Check for error from session
if (isset($_SESSION['payment_error'])) {
    $error_message = $_SESSION['payment_error'];
    unset($_SESSION['payment_error']);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <?php include 'top.php'; ?>
    <div id="content">
        <br>
        <div id="back">
            <a href="zone.php" class="back">Back</a>
        </div>
        <div id="main">
            <h2>Payment</h2>
        </div>
        <div id="book_search">
            <?php
            /*
            $q1="select *  from session where session_id = $_GET['sid']";
            $q2="select payment_method from customer where customer_id = $_GET['uid']";     <---IDK HOW
            $result=$mysqli->query($q);
            if(!$result){
                echo "Select failed. Error: ".$mysqli->error ;
                return false;
            }

                *********Display  Payment Detail************

            <?php } */ ?>

            <!-- Demo -->

            <label>Title: <? echo $row['session_name'] ?></label><br><br><br>
            <label>Date: <? echo $date ?></label><br><br><br>
            <label>Price: <? echo $row['price'] ?></label><br><br><br>
            <label>Time: <? echo "$start_time - $end_time" ?></label><br><br><br>
            <label>Zone: <? echo $zone ?></label><br><br><br>

            <?php if (isset($error_message)): ?>
                <div class="error-box">
                    <?php
                    if (strpos($error_message, 'Insufficient balance') !== false) {
                        echo "⚠️ " . htmlspecialchars($error_message);
                        echo '<br><br><a href="topup.php">Top up your balance now</a>';
                    } else {
                        echo "⚠️ " . htmlspecialchars($error_message);
                    }
                    ?>
                </div>
            <?php endif; ?>

            <!-- End of Demo -->

            <form action="<? echo $_SERVER['PHP_SELF'] ?>" method="post">
                <input type="hidden" name="zone" value="<? echo $zone?>">
                <input type="submit" name="submit_payment" value="Confirm" style="margin-left: 12em;"><br><br><br>
            </form>
        </div>
    </div>
</body>

</html>