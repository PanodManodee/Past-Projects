<?php
session_start();
require_once('dbconn.php');
$pid = $_SESSION['performer_id'];
$alias = $_SESSION['p_alias'];
$sid = $_GET['sid'];

$q = mysqli_query($mysqli, "select * from performer where performer_id = $pid");
$r = mysqli_fetch_array($q);
if (!$r) {
    echo "Fetch performer failed. Error: " . $mysqli->error;
    exit;
}

$q2 = mysqli_query($mysqli, "select * from session where session_id = $sid");
$r2 = mysqli_fetch_array($q2);
if (!$r2) {
    echo "Fetch session failed. Error: " . $mysqli->error;
    exit;
}
$datetime = new DateTime($r2['session_date']);
$saved_date = $datetime->format('Y-m-d');
$saved_time = $datetime->format('H:i:s');

$q3 = mysqli_query($mysqli, "select * from equipment where session = $sid");
$r3 = mysqli_fetch_array($q3);
$equipment_detail = $r3 ? $r3['req_detail'] : '';

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    $price = $_POST['price'];
    $datetime = $date . ' ' . $time;

    $stmt = $mysqli->prepare('call edit_concert_full( ?, ?, ?, ?, ?, ?);');
    $stmt->bind_param("ssissi", $r['username'], $r['password'], $sid, $title, $datetime, $price);
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }
    if (!$stmt->execute()) {
        die("Update failed: " . $stmt->error);
    }

    // Add this after calling stored procedure:
    while ($mysqli->more_results()) {
        $mysqli->next_result();
    }

    if (!empty($_POST["equip"])) {
        $equip = $_POST['equip'];
        $stmt2 = $mysqli->prepare('update equipment set req_detail = ? where session = ?;');
        $stmt2->bind_param('si', $equip, $sid);
        if (!$stmt2) {
            die('Prepare failed: ' . $mysqli->error);
        }
        if (!$stmt2->execute()) {
            die('Update Equipment failed: ' . $stmt2->error);
        }
        $stmt2->close();
    }

    $stmt->close();
    header('Location: mysession.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation</title>
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
            <h2>Edit Reservation</h2>
        </div>
        <div id="book_search">
            <form action="edit_session.php?sid=<?php echo $sid; ?>" method="post">

                <label>Title</label>
                <input type="text" name="title" value="<? echo $r2['session_name']; ?>"><br><br><br>

                <label>Date</label>
                <input type="date" name="date" value="<? echo $saved_date ?>"
                    style="margin-left: 10px; width: 200px; text-align: center;"><br><br><br>

                <label>Start Time (Each Session lasts 2 hours)</label>
                <select name="time" style="width: 200px; text-align: center;">
                    <option value="16:00:00" <?php echo ($saved_time == '16:00:00') ? 'selected' : ''; ?>>16:00</option>
                    <option value="18:00:00" <?php echo ($saved_time == '18:00:00') ? 'selected' : ''; ?>>18:00</option>
                    <option value="20:00:00" <?php echo ($saved_time == '20:00:00') ? 'selected' : ''; ?>>20:00</option>
                    <option value="22:00:00" <?php echo ($saved_time == '22:00:00') ? 'selected' : ''; ?>>22:00</option>
                </select> <br><br><br>
                <label>Price</label>
                <input type="number" name="price" value="<? echo $r2['price'] ?>" stype="margin-left: 10px;"><br><br><br>

                <label>Equipment/Special Equipment</label>
                <input type="text" name="equip" value="<? echo $equipment_detail ?>"
                    style="margin-left: 10px;"><br><br><br>

                <input type="submit" name="submit" value="Submit"><br><br><br>
            </form>


        </div>
    </div>|
</body>

</html>