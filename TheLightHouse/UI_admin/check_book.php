<?php
session_start();
require_once('dbconn.php');

$name = $_GET['name'];
$date = $_GET['date'];

if (isset($_GET['search_book'])) {
    $q = "select * from booking where 1=1";
    if($name && !empty($name)){
        $q .= " and concat(fname,' ',lname) like '%$name%'";
    } if($date && !empty($date)){
        $q .= " and datetime like '%$date%'";
    }
} else {
    $q = "select * from booking ";
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
    <title>Check_Book</title>
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
            <h2>Bookings</h2>
            <form action="check_book.php" method="GET">
                <label>Search by session name</label>
                <input type="text" name="name">
                <input type="submit" name="search_book" value="Search"><br><br><br>

<!-- If you want to add date to equipment table -->

<!-- ***** could do search date (from x to y) if it doesn't take to long, 
 other wise stick with this layout-->

                <label>Search by booking date</label>
                <input type="date" name="date"><br><br><br>
            </form>

            <table>

                <tr>
                    <th style="column-width: 15em; ">Book by:</td> 
                    <th style="column-width: 15em;">Session</td>
                    <th style="column-width: 15em;">Time</td>
                </tr>

<!-- %%%%%%%%%%%%%%% DISPLAY ALL OF THE TICKET BELONGING TO THE USER %%%%%%%%%%%%%%%-->


<!-- Demo -->

                
                <?php

                while ($row = $result->fetch_array()) { ?>
                    <tr>
                        <td><? echo $row['fname'] . ' ' . $row['lname'] ?></td>
                        <td><? echo $row['s_name'] ?></td>
                        <td><? echo $row['datetime'] ?></td>
                    </tr>
                <? } ?>


<!-- End of Demo-->

            </table>
        </div>
    </div>
</body>
</html>