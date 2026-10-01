<?php
session_start();
require_once('dbconn.php');
$aid =$_SESSION['admin_id'];
$sid = $_GET['sid'];
$q = "select * from session where session_id = $sid";
$result = mysqli_query($mysqli, $q);
if (!$result) {
    echo "Select failed. Error:" . $mysqli->error;
    exit;
}
$row = mysqli_fetch_array($result);
$sid_update = $row["session_id"];

if (isset($_POST["approve"])) {

    $q = "update session set approval = 1, admins = $aid where session_id = $sid_update";
    $result = mysqli_query($mysqli, $q);
    if (!$result) {
        echo "Update failed. Error: " . $mysqli->error;
        exit;
    }
    header("Location: session_a.php");
    exit;

} else if (isset($_POST["reject"])) {

    $q = "update session set approval = 0, admins = $aid where session_id = $sid_update";
    $result = mysqli_query($mysqli, $q);
    if (!$result) {
        echo "Update failed. Error: " . $mysqli->error;
        exit;
    }
    header("Location: session_a.php");
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
            <a href="session_a.php" class="back">Back</a>
        </div>
        <div id="main">
            <h2>Session Title: <? echo $row['session_name'] ?></h2>
        </div>
        <div id="book_search">

            <!-- SHOULD FORM BE HERE OR LOWER ? -->

            <form action="check_session.php?sid=<? echo $row['session_id'] ?>" method="post">
                <input type="hidden" name="sid" value="<?php echo $sid; ?>">
                <?php
                // Convert to DateTime object
                $datetime = new DateTime($row["session_date"]);
                // Format the date and times
                $date = $datetime->format('d/m/Y');           // 22/11/2025
                $start_time = $datetime->format('H:i');       // 18:00
                // Add 2 hours for end time
                $datetime->modify('+2 hours');
                $end_time = $datetime->format('H:i');
                ?>
                <label>Date: <? echo $date ?></label><br><br><br>

                <label>Time: <? echo "$start_time - $end_time" ?></label><br><br><br>

                <!-- Go to the check_req page of this Session. 
Haven't embedded anything to href apart from the web page itself-->

                <label>Equipment/Special Equipment: </label> <a href="check_req.php?sid=<? echo $row['session_id']; ?>" class="back">Check request</a><br><br><br>

                <!-- WHAT WILL HAPPNED WHEN APPROVE OR REJECT ? 
 Maybe add enable/disable???-->

                <input type="submit" name="approve" value="Approve">
                <input type="submit" name="reject" value="Reject" id="reject"><br><br><br>
            </form>

        </div>
    </div>
</body>

</html>