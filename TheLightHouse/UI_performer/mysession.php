<?php
session_start();
require_once('dbconn.php');
$pid = $_SESSION['performer_id'];

$q = "select * from session where performers = $pid";
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
    <title>My Session</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <?php include 'top_p.php'; ?>
    <div id="content">
        <br>
        <div id="back">
            <a href="home_p.php" class="back">Back</a>
        </div>
        <div id="main">
            <?php
            // Display error message
            if (isset($_SESSION['error'])) {
                echo '<div style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #f5c6cb;">
            <strong>⚠️ Error:</strong> ' . htmlspecialchars($_SESSION['error']) . '
          </div>';
                unset($_SESSION['error']);
            }

            // Display success message
            if (isset($_SESSION['success'])) {
                echo '<div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #c3e6cb;">
            <strong>✓ Success:</strong> ' . htmlspecialchars($_SESSION['success']) . '
          </div>';
                unset($_SESSION['success']);
            }
            ?>
            <h2>My Session</h2>
            <table>

                <tr>
                    <th style="column-width: 15em; ">Title</td>
                    <th style="column-width: 15em;">Date & Time</td>
                    <th style="column-width: 3em;"></th>
                    <th style="column-width: 3em;"></th>
                </tr>

                <!-- %%%%%%%%%%%%%%% DISPLAY ALL OF THE TICKET BELONGING TO THE USER %%%%%%%%%%%%%%%-->


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
                        <td
                            style="<? if ($row['approval'] == 1) {
                                echo "background-color: rgb(128, 254, 141)";
                            } else {
                                echo "background-color: rgb(254, 115, 115)";
                            } ?>">
                            <? echo $row['session_name']; ?></td>
                        <td
                            style="<? if ($row['approval'] == 1) {
                                echo "background-color: rgb(128, 254, 141)";
                            } else {
                                echo "background-color: rgb(254, 115, 115)";
                            } ?>">
                            <? echo "$date at $start_time - $end_time"; ?></td>
                        <td
                            style="<? if ($row['approval'] == 1) {
                                echo "background-color: rgb(128, 254, 141)";
                            } else {
                                echo "background-color: rgb(254, 115, 115)";
                            } ?>">
                            <a href="edit_session.php?sid=<? echo $row['session_id'] ?>" id="edit">Edit</a></td>
                        <td
                            style="<? if ($row['approval'] == 1) {
                                echo "background-color: rgb(128, 254, 141)";
                            } else {
                                echo "background-color: rgb(254, 115, 115)";
                            } ?>">
                            <a href="del_session.php?sid=<? echo $row['session_id'] ?>" id="delete">Delete</a></td>
                    </tr>
                <? } ?>


                <!-- End of Demo-->

            </table>
        </div>
    </div>
</body>

</html>