<?php
session_start();
require_once("dbconn.php");

if (!isset($_GET["sid"])) {
    $_SESSION['error'] = "Session ID is required.";
    header("Location: myticket.php");
    exit();
}

$sid = $_GET["sid"];

try {
    $stmt = $mysqli->prepare("CALL cancel_session(?)");
    $stmt->bind_param("i", $sid);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $stmt->close();

    while ($mysqli->more_results()) {
        $mysqli->next_result();
    }

    $mysqli->close();

    $_SESSION['success'] = "session cancelled successfully! Customers balance has been refunded.";
    header("Location: mysession.php");
    exit();

} catch (Exception $e) {
    $error_message = $e->getMessage();
    
    // Extract user-friendly message
    if (strpos($error_message, 'cancellations must be made at least 7 days') !== false) {
        $_SESSION['error'] = "Cannot cancel session. Cancellations must be made at least 7 days in advance.";
    } elseif (strpos($error_message, 'does not exist') !== false) {
        $_SESSION['error'] = "session not found.";
    } else {
        $_SESSION['error'] = "Cancel failed: " . $error_message;
    }
    
    header("Location: mysession.php");
    exit();
}
?>