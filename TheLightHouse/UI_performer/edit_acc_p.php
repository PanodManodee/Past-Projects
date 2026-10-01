<?php
session_start();
require_once('dbconn.php');
$pid = $_SESSION['performer_id'];
$q = "select * from performer where performer_id = $pid;";
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
    $alias = $_POST['alias'];
    $pay_meth = $_POST['pay_meth'];
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $mysqli->prepare("
        update performer set fname = ?, lname = ?, username = ?, password = ?, p_alias = ?, payment_method = ? where performer_id = ?;
    ");
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("ssssssi", $firstname, $lastname, $username, $hash, $alias, $pay_meth, $pid);
    if (!$stmt->execute()) {
        die("Update failed: " . $stmt->error);
    }
    $stmt->close();

    header("Location: account_p.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <?php include 'top_p.php'; ?>
    <div id="content">
        <br>
        <div id="back">
            <a href="account_p.php" class="back">Back</a>
        </div>
        <div id="main">
            <h2>Account: <? echo $row['p_alias']?></h2>
        </div>
        <div id="book_search">

            <!-- Same as register but make the info appear in text -->

            <form action="edit_acc_p.php" method="post">
                <label>First Name</label>
                <input type="text" name="fname" value="<? echo $row['fname']?>"><br><br><br>
                <label>Last Name</label>
                <input type="text" name="lname" value="<? echo $row['lname']?>"><br><br><br>
                <label>Username</label>
                <input type="text" name="username" value="<? echo $row['username']?>"><br><br><br>
                <label>Password</label>
                <input type="password" name="passwd" ><br><br><br>
                <label>Alias</label>
                <input type="text" name="alias" value="<? echo $row['p_alias']?>" style="margin-left: 10px;"><br><br><br>
                <label>Payment Method</label>
                <select name="pay_meth">
                    <option value="Prompt Pay" <?php echo ($row['payment_method'] == 'Prompt Pay') ? 'selected' : ''; ?>>Prompt Pay</option>
                    <option value="True Money Wallet" <?php echo ($row['payment_method'] == 'True Money Wallet') ? 'selected' : ''; ?>>True Money Wallet</option>
                    <option value="Credit card" <?php echo ($row['payment_method'] == 'Credit') ? 'selected' : ''; ?>>Credit card</option>
                    <option value="Paypal" <?php echo ($row['payment_method'] == 'Paypal') ? 'selected' : ''; ?>>Paypal</option>
                </select>
                <br><br><br>

        </div>
        <div id="zone">

            <!-- Edit and Return to Account -->
            <input type="submit" name="edit" value="Edit" id="edit"><br><br><br>
            </form>
        </div>
    </div>
</body>

</html>