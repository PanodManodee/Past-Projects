<?php
require_once('dbconn.php');
session_start();

if (isset($_POST['register'])) {

    $firstname = $_POST['regis_fname'];
    $lastname = $_POST['regis_lname'];
    $username = $_POST['regis_username'];
    $password = $_POST['regis_passwd'];
    $date = $_POST['regis_date'];

    // Hash password safely
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $stmt = $mysqli->prepare("
        INSERT INTO customer (balance, username, password, fname, lname, dob)
        VALUES (0, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }

    // Bind parameters (all strings)
    $stmt->bind_param("sssss", $username, $hash, $firstname, $lastname, $date);

    // Execute
    if (!$stmt->execute()) {
        die("Register failed: " . $stmt->error);
    }

    // Get new customer ID
    $new_id = $stmt->insert_id;

    // Store ID in session
    $_SESSION['customer_id'] = $new_id;

    // Close statement
    $stmt->close();

    header("Location: home_c.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <div id="top">
        <ul id="menu">
            <li><a href="home_c_noacc.php">Home</a></li>
        </ul>
    </div>
    <div id="content">
        <br>
        <div id="back">
            <a href="login.php" class="back">Back</a>
        </div>
        <div id="main">
            <h2>Register new account</h2>
        </div>
        <form action='register.php' method='post'>
            <div id="book_search">

                <!--IDK IF THE ACTION IS CORRECT. (SO AS OTHER'S TBH) -->


                <label>First Name</label>
                <input type="text" name="regis_fname"><br><br><br>
                <label>Last Name</label>
                <input type="text" name="regis_lname"><br><br><br>
                <label>Username</label>
                <input type="text" name="regis_username"><br><br><br>
                <label>Password</label>
                <input type="password" name="regis_passwd"><br><br><br>
                <label>Birth Date</label>
                <input type="date" name="regis_date" style="margin-left: 10px;"><br><br><br>


            </div>
            <div id="zone">

                <!-- Register and return to home page -->
                <input type="submit" name="register" value="Register" id="register"><br><br><br>

            </div>
        </form>
    </div>
</body>

</html>