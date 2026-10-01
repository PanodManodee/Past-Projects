<?php
session_start();
require_once('dbconn.php');
$aid =$_SESSION['admin_id'];
$sid = $_GET['sid'];
$q = "select * from equipment e inner join session s on e.session = s.session_id where session = $sid";
$result = mysqli_query($mysqli, $q);
if (!$result) {
    echo "Select failed. Error:" . $mysqli->error;
    exit;
}
$row = mysqli_fetch_array($result);
$uid_update = $row["equipment_id"];

if (isset($_POST["confirm"])) {

    $q = "update equipment set req_approval = 1, admins = $aid where equipment_id = $uid_update;";
    $result = mysqli_query($mysqli, $q);
    if (!$result) {
        echo "Update failed. Error: " . $mysqli->error;
        exit;
    }
    header("Location: requests.php");
    exit;

} else if (isset($_POST["reject"])) {

    $q = "update equipment set req_approval = 0, admins = $aid where equipment_id = $uid_update";
    $result = mysqli_query($mysqli, $q);
    if (!$result) {
        echo "Update failed. Error: " . $mysqli->error;
        exit;
    }
    header("Location: requests.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Session</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <?php include 'top_a.php'; ?>
    <div id="content">
        <br>
        <div id="back">
            <a href="requests.php" class="back">Back</a>
        </div>
        <div id="main">
            <h2>From: <? echo $row['session_name']?></h2>
        </div>
        <div id="book_search">

            <!-- WHAT WILL HAPPNED WHEN APPROVE OR REJECT ? 
 Maybe add enable/disable???-->

            <form action="check_req.php?sid=<? echo $row['session_id']?>" method="post">

                <label>Detail: <? echo $row['req_detail']?></label><br><br><br>
                <label>Session Date: <? echo $row['req_date']?></label><br><br><br>

                <input type="submit" name="confirm" value="Confirm">
                <input type="submit" name="reject" value="Reject" id="reject"><br><br><br>
            </form>

        </div>
    </div>
</body>

</html>