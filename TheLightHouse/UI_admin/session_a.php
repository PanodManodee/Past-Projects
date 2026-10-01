<?php
session_start();
require_once('dbconn.php');

$name = $_GET['name'];
$date = $_GET['date'];

if (isset($_GET['search_session'])) {
    $q = "select * from session where 1=1";
    if ($name && !empty($name)) {
        $q .= " and session_name like '%$name%'";
    }
    if ($date && !empty($date)) {
        $q .= " and session_date like '%$date%'";
    }
} else {
    $q = "select * from session ";
}
$result = mysqli_query($mysqli, $q);
if (!$result) {
    echo "Select failed. Error:" . $mysqli->error;
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sessions</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <?php include 'top_a.php'; ?>
    <div id="content">
        <br>
        <div id="back">
            <a href="home_a.php" class="back">Back</a>
        </div>
        <div id="main">
            <h2>Sessions</h2>
            <form action="session_a.php" method="GET">
                <label>Search by session name</label>
                <input type="text" name="name">
                <input type="submit" name="search_session" value="Search"><br><br><br>
                <label>Search by session date</label>
                <input type="date" name="date"><br><br><br>
            </form>
            <table>

                <tr>
                    <th style="column-width: 15em; ">Title</td>
                    <th style="column-width: 15em;">Date & Time</td>
                    <th style="column-width: 3em;"></th>
                </tr>

                <!-- %%%%%%%%%%%%%%% DISPLAY SESSION %%%%%%%%%%%%%%%-->


                <!-- Demo -->
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
                        <td style="<? if ($row['approval'] == 1) {
                            echo "background-color: rgb(128, 254, 141)";
                        } else {
                            echo "background-color: rgb(254, 115, 115)";
                        } ?>">
                            <? echo $row['session_name'] ?>
                        </td>
                        <td style="<? if ($row['approval'] == 1) {
                            echo "background-color: rgb(128, 254, 141)";
                        } else {
                            echo "background-color: rgb(254, 115, 115)";
                        } ?>">
                            <? echo "$date at $start_time - $end_time" ?>
                        </td>
                        <td style="<? if ($row['approval'] == 1) {
                            echo "background-color: rgb(128, 254, 141)";
                        } else {
                            echo "background-color: rgb(254, 115, 115)";
                        } ?>">
                            <a href='check_session.php?sid=<? echo $row['session_id'] ?>' id="edit">Detail</a>
                        </td>
                    </tr>
                <? } ?>

                <!-- End of Demo-->

            </table>
        </div>
    </div>
</body>

</html>