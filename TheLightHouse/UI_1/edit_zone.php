<?php
session_start();
require_once('dbconn.php');
$bid = $_GET['bid'];
if (isset($_POST['submit'])) {
    $zone = $_POST['zone'];
    $q = "update booking set zone = '$zone' where booking_id = $bid;";
    $result = mysqli_query($mysqli, $q);
    if (!$result) {
        echo "Insert failed. Error:" . $mysqli->error;
        exit;
    }
    header("Location: myticket.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Zone</title>
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
        <div id="book_search">
            <h2>Edit Zone</h2>

        </div>

        <!-- SELECT THE ZONE THEN UPDATE THE TICKET -->

        <div id="zone">
            <img src="stage_1.png" alt="Stand_in_img"><br>
            <form action="<? echo $_SERVER['PHP_SELF'] ?>?bid=<? echo $bid?>" method="post">
                <input type="radio" id="a" name="zone" value="A" required />
                <label for="a" id="lab_a"> Zone A </label>
                <input type="radio" id="b" name="zone" value="B" required />
                <label for="b" id="lab_b"> Zone B</label>
                <input type="radio" id="c" name="zone" value="C" required />
                <label for="c" id="lab_c"> Zone C</label>
                <input type="radio" id="d" name="zone" value="D" required />
                <label for="d" id="lab_d"> Zone D</label>
                <input type="radio" id="e" name="zone" value="E" required />
                <label for="e" id="lab_e"> Zone E</label>
                <input type="radio" id="f" name="zone" value="F" required />
                <label for="f" id="lab_f"> Zone F</label><br>
                <input type="submit" name="submit" value="Confirm"><br><br><br>
            </form>

        </div>
    </div>
</body>

</html>