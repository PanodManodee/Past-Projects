<?php

session_unset();
require_once('dbconn.php');
if (isset($_POST['login'])) {
    $username = $_POST['name'];
    $password = $_POST['pass'];
    // Get the user from DB
    $stmt = $mysqli->prepare("SELECT admin_id, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($admin_id, $stored_hash);
        $stmt->fetch();

        // Verify input password using hash
        if (password_verify($password, $stored_hash)) {
            //  Start a session, redirect, etc.
            session_start();
            $_SESSION["admin_id"] = $admin_id; // Store the actual customer ID, not the hash!
            $stmt->close();

            // FIX: Add "Location: " before the URL
            header("Location: home_a.php");
            exit(); // Always call exit() after header redirect
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
            <li ><a href="home_a_noacc.php">Home</a></li>
        </ul>
    </div>

    <div id="content">
        <br>
        <div id="back">
            <a href="home_a_noacc.php" class="back">Back</a>
        </div>
        <div id="book_search">
            <h2>Log in</h2>

<!-- THE FIRST PAGE THE USER SEE, EITHER GO TO REGISTER OR LOG IN (REDIRECT TO HOME) -->

            <form action="login_a.php" method="post">
                <label>Username</label>
                    <input type="text" name="name" style="width: 400px;"><br><br><br>
                <label>Password</label>
                    <input type="password" name="pass" style="width: 400px;"><br><br><br>
                <input type="submit" name="login" value="Submit">
            </form>
            
        </div>
    </div>
</body>
</html>