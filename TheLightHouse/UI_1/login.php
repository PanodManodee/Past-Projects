<?php

session_unset();
require_once('dbconn.php');
if (isset($_POST['login'])) {
    $username = $_POST['name'];
    $password = $_POST['pass'];
    // Get the user from DB
    $stmt = $mysqli->prepare("SELECT customer_id, password, status FROM customer WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($customer_id, $stored_hash, $status);
        $stmt->fetch();

        // Verify input password using hash
        if (password_verify($password, $stored_hash)) {
            if ($status == 1) {
            //  Start a session, redirect, etc.
            session_start();
            $_SESSION["customer_id"] = $customer_id; // Store the actual customer ID, not the hash!
            $stmt->close();

            // FIX: Add "Location: " before the URL
            header("Location: home_c.php");
            exit(); // Always call exit() after header redirect
            } else  echo "Account suspended.";
        } else {
            echo "Wrong password.";
        }
    } else {
        echo "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
            <a href="home_c_noacc.php" class="back">Back</a>
        </div>
        <div id="book_search">
            <h2>Log in</h2>

            <!-- THE FIRST PAGE THE USER SEE, EITHER GO TO REGISTER OR LOG IN (REDIRECT TO HOME) -->

            <form action="login.php" method="post">
                <label>Username</label>
                <input type="text" name="name" style="width: 400px;"><br><br><br>
                <label>Password</label>
                <input type="password" name="pass" style="width: 400px;"><br><br><br>
                <input type="submit" name="login" value="Login">
                <a href="register.php">Register</a>
                <a href="../UI_performer/login_p.php">Log In (Performer)</a>
            </form>

        </div>
    </div>
</body>

</html>