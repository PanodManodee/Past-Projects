<?php
session_start();
$cid = $_SESSION["customer_id"];
require_once('dbconn.php');
$session = $_POST['session'];
$_SESSION['session'] = $session;
$sid = $_SESSION['session'];


$q = "select * from session where session_id = $sid;";
$result = mysqli_query($mysqli, $q);
if (!$result) {
    echo "Select failed. Error:" . $mysqli->error;
    exit;
}
$row = mysqli_fetch_array($result);

// Convert to DateTime object
$datetime = new DateTime($row["session_date"]);
// Format the date and times
$date = $datetime->format('d/m/Y');           // 22/11/2025
$start_time = $datetime->format('H:i');       // 18:00
// Add 2 hours for end time
$datetime->modify('+2 hours');
$end_time = $datetime->format('H:i'); 
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zone Select</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <?php include 'top.php'; ?>
    <div id="content">
        <br>
        <div id="back">
            <a href="booking.php" class="back">Back</a>
        </div>
        <div id="main">
            <h2>Concert Detail</h2>
        </div>
        <div id="book_search">

            <!-- *********Display  Session Detail************ -->

            <!-- Demo -->

            <label>Title: <? echo $row['session_name'] ?></label><br><br><br>
            <label>Performer: <? echo $row['p_alias'] ?></label><br><br><br>
            <label>Date: <? echo $date?></label><br><br><br>
            <label>Time: <? echo "$start_time - $end_time"?></label><br><br><br>

            <!-- THE SELECTED RADIO WILL OUTPUT IN THE NEXT PAGE (payment.php) -->

        </div>
        <div id="zone">
            <img src="stage_1.png" alt="Stand_in_img"><br>
            <form action="payment.php" method="post">
                <input type="hidden" name="session" value="<? echo $row['session_id']?>">
                <input type="radio" id="a" name="zone" value="A" required/>
                <label for="a" id="lab_a"> Zone A </label>
                <input type="radio" id="b" name="zone" value="B" required/>
                <label for="b" id="lab_b"> Zone B</label>
                <input type="radio" id="c" name="zone" value="C" required/>
                <label for="c" id="lab_c"> Zone C</label>
                <input type="radio" id="d" name="zone" value="D" required/>
                <label for="d" id="lab_d"> Zone D</label>
                <input type="radio" id="e" name="zone" value="E" required/>
                <label for="e" id="lab_e"> Zone E</label>
                <input type="radio" id="f" name="zone" value="F" required/>
                <label for="f" id="lab_f"> Zone F</label><br>
                <input type="submit" name="submit" value="Confirm"><br><br><br>
            </form>

        </div>
    </div>
</body>

</html>