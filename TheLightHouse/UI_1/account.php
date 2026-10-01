<?php	
    session_start();
    require_once('dbconn.php'); 
    $cid = $_SESSION['customer_id'];
    $q = "select * from customer where customer_id = $cid;";
    $result = mysqli_query($mysqli, $q);
    if (!$result) {
        echo "Select failed. Error:" . $mysqli->error;
        exit;
    }
    $row = mysqli_fetch_array($result);
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
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
        <div id="main"><h2>Account: <? echo $row['username'];?></h2></div>
        <div id="book_search">
            
            <label>Name: <? echo $row['fname']. " ". $row['lname'];?></label><br><br>
            <label>Wallet Balance: <? echo "$". $row['balance'];?></label> 
            <a href="topup.php" class="back">Top up</a><br><br>
            <label>Birth Date: <? echo $row['dob'];?></label><br>

        </div>
        <div id="zone">
            
            <form action="login.php" method="post"> <!-- Delete Account and return to log in page -->
                <input type="submit" name="logout" value="Log Out" id="logout" ><br><br><br>
            </form>
            <a href="edit_acc.php" class="back">Edit Account</a>
        </div>
    </div>
</body>
</html>