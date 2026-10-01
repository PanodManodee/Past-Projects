<?php
session_start();
$cid = $_SESSION["customer_id"];
date_default_timezone_set("Asia/Bangkok");
require_once("dbconn.php");
if (isset($_POST["topup"])) {
    $pay_meth = $_POST["pay_meth"];
    $amount = $_POST["amount"];
    $date = date('Y-m-d H:i:s');
    $stmt = $mysqli->prepare('call topup_balance( ?, ?, ?, ?)');
    if (!$stmt) {
        die("Prepare failed: " . $mysqli->error);
    }
    $stmt->bind_param("iiss", $cid, $amount, $pay_meth, $date);

    if (!$stmt->execute()) {
        die("Topup failed: " . $stmt->error);
    }
    $stmt->close();
    while ($mysqli->more_results()) {
        $mysqli->next_result();
    }
    header("Location: account.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Topup</title>
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
        <div id="book_search">
            <h2>Top-up</h2>

            <!-- UPDATE THE BALANCE -->

            <form action="topup.php" method="post">
                <label>payment method</label>
                <select name="pay_meth">
                    <option value="Prompt Pay">Prompt Pay</option>
                    <option value="True Money Wallet">True Money Wallet</option>
                    <option value="Credit card">Credit card</option>
                    <option value="Paypal">Paypal</option>
                </select>
                <br><br><br>
                <label>amount</label>
                <input type="number" name="amount" style="width: 400px;"><br><br><br>
                <input type="submit" name="topup" value="Confirm">
            </form>

        </div>
    </div>
</body>

</html>