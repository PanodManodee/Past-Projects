<?php
session_start();
require_once('dbconn.php');

$name = $_GET['name'];
$date = $_GET['date'];

if (isset($_GET['search'])) {
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
    <title>Booking</title>
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
            <h2>Book Your Ticket</h2>
        </div>
        <div id="book_search">
            <form action="booking.php" method="GET">
                <input type="submit" name="search" value="Search"><br><br><br>
                <label>Name</label>
                <input type="text" name="name"><br><br><br>
                <label>Date</label>
                <input type="date" name="date" style="margin-left: 10px;"><br><br><br>
            </form>


            <!-- Extract Session Name from the Search Filter -->

            <form action="zone.php" method="post">

                <!-- REAL ONE (ROUGH CODE) -->
                <label style="">Result : </label>
                <div id="select">
                    <?php
                    while ($row = $result->fetch_array()) {
                        ?>
                        <input type="radio" id="<? echo $row['session_id'] ?>" name="session"
                            value="<? echo $row['session_id'] ?>" required/>
                        <label for="<? echo $row['session_id'] ?>"><? echo $row['session_name'] ?> </label><br>
                        <!-- DEMO (It has to be selectable so they should be hidden radio buttons, not tables :( -->





                    <?php } ?>
                </div>

                <!-- End of Demo -->

                <input type="submit" name="submit" value="Confirm"
                    style="margin-top: 2em; margin-left: 12em;"><br><br><br>
            </form>
        </div>
    </div>|
</body>

</html>