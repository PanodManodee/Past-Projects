<?php
require_once("UI_1/dbconn.php");
$fname = 'admin1';
$lname = 'testadmin';
$username = 'root';
$password = password_hash('root', PASSWORD_DEFAULT);
$stmt = $mysqli->prepare("insert into admin (fname, lname, username, password) values ( ?, ?, ?, ?);");
    $stmt->bind_param("ssss", $fname, $lname, $username, $password);
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }
    if (!$stmt->execute()) {
        die("insert admin failed: " . $stmt->error);
    }
    $stmt->close();
?>