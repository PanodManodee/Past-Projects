<?php
session_start();
require_once('dbconn.php');
$pid = $_SESSION['performer_id'];
$alias = $_SESSION['p_alias'];

if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    $price = $_POST['price'];
    $datetime = $date . ' ' . $time;
    try {
        $stmt = $mysqli->prepare('insert into session( session_name, session_date, p_alias, price, performers, approval) values ( ?, ?, ?, ?, ?, 0);');
        if (!$stmt) {
            throw new Exception("Database error: " . $mysqli->error);
        }
        $stmt->bind_param('sssii', $title, $datetime, $alias, $price, $pid);

        if (!$stmt->execute()) {
            throw new Exception($stmt->error);
        }
        $sid = $stmt->insert_id;
        if (!empty($_POST["equip"])) {
            $equip = $_POST['equip'];
            $stmt2 = $mysqli->prepare('insert into equipment( req_detail, session, req_date) values ( ?, ?, ?);');
            $stmt2->bind_param('sis', $equip, $sid, $datetime);
            if (!$stmt2) {
                die('Prepare failed: ' . $mysqli->error);
            }
            if (!$stmt2->execute()) {
                die('Reserve Equipment failed: ' . $stmt2->error);
            }
            $stmt2->close();
        }

        $stmt->close();
        header('Location: mysession.php');
        exit();
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }

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
            <?php if (isset($error_message)): ?>
                <div class="error-box">
                    <?php if (isset($error_message)): ?>
                        <div class="error-box"
                            style="background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; border: 1px solid #f5c6cb;">
                            <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <h2>Make Reservation</h2>
        </div>
        <div id="book_search">
            <form action="reservation.php" method="post">

                <label>Title</label>
                <input type="text" name="title" required><br><br><br>

                <label>Date</label>
                <input type="date" name="date" style="margin-left: 10px; width: 200px; text-align: center;"
                    required><br><br><br>

                <label>Start Time (Each Session lasts 2 hours)</label>
                <select name="time" style="width: 200px; text-align: center;" required>
                    <option value="16:00:00">16:00</option>
                    <option value="18:00:00">18:00</option>
                    <option value="20:00:00">20:00</option>
                    <option value="22:00:00">22:00</option>
                </select> <br><br><br>
                <label>Price</label>
                <input type="number" name="price" stype="margin-left: 10px;" required><br><br><br>

                <label>Equipment/Special Equipment</label>
                <input type="text" name="equip" style="margin-left: 10px;"><br><br><br>

                <input type="submit" name="submit" value="Submit"><br><br><br>
            </form>


        </div>
    </div>|
</body>

</html>