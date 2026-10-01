<?php
session_start();
require_once('dbconn.php');
$cid = $_SESSION['customer_id'];
$q = "select * from customer where customer_id = $cid;";
$result = mysqli_query($mysqli, $q);
if (!$result) {
    echo "Insert failed. Error:" . $mysqli->error;
    exit;
}
$row = mysqli_fetch_array($result);
if (isset($_POST['edit'])) {

    $firstname = $_POST['fname'];
    $lastname = $_POST['lname'];
    $username = $_POST['username'];
    $password = $_POST['passwd'];
    $date = $_POST['date'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("
        update customer set fname = ?, lname = ?, username = ?, password = ?, dob = ? where customer_id = ?;
    ");
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("sssssi", $firstname, $lastname, $username, $hash, $date, $cid);
    if (!$stmt->execute()) {
        die("Update failed: " . $stmt->error);
    }
    $stmt->close();

    header("Location: account.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Edit</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <?php include 'top.php'; ?>
    <div id="content">
        <br>
        <div id="back">
            <a href="account.php" class="back">Back</a>
        </div>
        <div id="main">
            <h2>Account: <? echo $row['username'] ?></h2>
        </div>
        <div id="book_search">

            <!-- Same as register but make the info appear in text -->

            <form action="edit_acc.php" method="post">
                <label>First Name</label>
                <input type="text" name="fname" value="<? echo $row['fname'] ?>"><br><br><br>
                <label>Last Name</label>
                <input type="text" name="lname" value="<? echo $row['lname'] ?>"><br><br><br>
                <label>Username</label>
                <input type="text" name="username" value="<? echo $row['username'] ?>"><br><br><br>
                <label>Password</label>
                <input type="password" name="passwd" ><br><br><br>
                <label>Birth Date</label>
                <input type="date" name="date" value="<? echo $row['dob'] ?>" style="margin-left: 10px;"><br><br><br>


        </div>
        <div id="zone">

            <!-- Edit and Return to Account -->
            <input type="submit" name="edit" value="Edit" id="register"><br><br><br>
            </form>
        </div>
    </div>
</body>

</html>