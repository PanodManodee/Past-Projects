<?php
session_start();

require_once('dbconn.php');
$cid = $_SESSION['customer_id'];

$q = "select * from booking where customers = $cid";
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
    <title>My Ticket</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <?php include 'top.php'; ?>
    <div id="content">
        <br>
        <div id="back">
            <a href="home_c.php" class="back">Back</a>
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
            <h2>My Ticket</h2>
            <table>

                <tr>
                    <th style="column-width: 15em; ">Title</td>
                    <th style="column-width: 15em;">Zone</th>
                    <th style="column-width: 15em;">Date & Time</td>
                    <th style="column-width: 3em;"></th>
                    <th style="column-width: 3em;"></th>
                </tr>

                <!-- %%%%%%%%%%%%%%% DISPLAY ALL OF THE TICKET BELONGING TO THE USER %%%%%%%%%%%%%%%-->
                <?php
                while ($row = mysqli_fetch_array($result)) {
                    $datetime = new DateTime($row["datetime"]);
                    // Format the date and times
                    $date = $datetime->format('d/m/Y');           // 22/11/2025
                    $start_time = $datetime->format('H:i');       // 18:00
                    // Add 2 hours for end time
                    $datetime->modify('+2 hours');
                    $end_time = $datetime->format('H:i');
                    ?>
                    <tr>
                        <td><? echo $row['s_name'] ?></td>
                        <td><? echo $row['zone'] ?></td>
                        <td><? echo "$date at $start_time - $end_time" ?></td>
                        <td><a href='edit_zone.php?bid=<? echo $row['booking_id'] ?>' id="edit">Edit</a></td>
                        <td><a href='del_tick.php?bid=<? echo $row['booking_id'] ?>' id="delete">Delete</a></td>

                    </tr>
                <? } ?>
                <!-- Demo -->


                <!-- End of Demo-->

            </table>
        </div>
    </div>
</body>

</html>