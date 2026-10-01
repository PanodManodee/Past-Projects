<?php
session_start();
require_once('dbconn.php');
$pid = $_SESSION['performer_id'];

$q = "select * from session where approval = 1 and date(session_date) = CURRENT_DATE and performers = $pid";
$result = mysqli_query($mysqli, $q);
if (!$result) {
    echo "Fetch sessions failed. Error:" . $mysqli->error;
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <?php include 'top_p.php'; ?>

    <!-- Main Section -->
    <div id="content">

        <div id="head">
            <!-- Add something -->
            <div id="banner">
                <h1>THE LIGHT HOUSE</h1>
                <h3>Music is the beacon to our lives</h3>
            </div>
            <div id="logo">
                <img src="Logo1.png" alt="Logo">
            </div>
        </div>
        <div id="main">
            <a href="reservation.php"><input type="button" value="Make Reservation"></a>
            <h2>Today's Schedule</h2>
            <!-- Table goes here -->
            <table>
                <tr>
                    <th style="column-width: 10em;">Time</th>
                    <th style="column-width: 30em;">Session</th>
                </tr>

                <!-- Stand in -->
                <?php
                while ($row = $result->fetch_array()) {
                    // Convert to DateTime object
                    $datetime = new DateTime($row["session_date"]);
                    // Format the date and times
                    $date = $datetime->format('d/m/Y');           // 22/11/2025
                    $start_time = $datetime->format('H:i');       // 18:00
                    // Add 2 hours for end time
                    $datetime->modify('+2 hours');
                    $end_time = $datetime->format('H:i'); ?>
                    <tr>
                        <td><? echo "$start_time - $end_time";?></td>
                        <td><? echo $row['session_name']?></td>

                    </tr>
                <? } ?>

        </div>
    </div>

</body>

</html>