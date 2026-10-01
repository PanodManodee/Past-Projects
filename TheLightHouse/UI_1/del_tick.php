<?php
session_start();
require_once("dbconn.php");

if (!isset($_GET["bid"])) {
    $_SESSION['error'] = "Booking ID is required.";
    header("Location: myticket.php");
    exit();
}

$bid = $_GET["bid"];

try {
    $stmt = $mysqli->prepare("CALL cancel_booking(?)");
    $stmt->bind_param("i", $bid);

    if (!$stmt->execute()) {
        throw new Exception($stmt->error);
    }

    $stmt->close();

    while ($mysqli->more_results()) {
        $mysqli->next_result();
    }

    $mysqli->close();

    $_SESSION['success'] = "Booking cancelled successfully! Your balance has been refunded.";
    header("Location: myticket.php");
    exit();

} catch (Exception $e) {
    $error_message = $e->getMessage();
    
    // Extract user-friendly message
    if (strpos($error_message, 'cancellations must be made at least 7 days') !== false) {
        $_SESSION['error'] = "Cannot cancel booking. Cancellations must be made at least 7 days in advance.";
    } elseif (strpos($error_message, 'does not exist') !== false) {
        $_SESSION['error'] = "Booking not found.";
    } else {
        $_SESSION['error'] = "Cancel failed: " . $error_message;
    }
    
    header("Location: myticket.php");
    exit();
}
?>