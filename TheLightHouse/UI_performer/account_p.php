<?php	
    session_start();
    require_once('dbconn.php'); 
    $pid = $_SESSION['performer_id'];
    $q = "select * from performer where performer_id = $pid;";
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
    <?php include 'top_p.php'; ?>
    <div id="content">
        <br>
        <div id="back">
            <a href="home_p.php" class="back">Back</a>
        </div>

<!-- Display Alias This time -->

        <div id="main"><h2>Hello, <? echo $row['p_alias']?></h2></div>
        <div id="book_search">
            
            <label>Name: <? echo $row['fname'] . ' '. $row['lname'];?></label><br><br>
            <label>Username: <? echo $row['username']?></label><br><br>
            <label>Payment Method: <? echo $row['payment_method']?></label><br>

        </div>
        <div id="zone">
            
            <form action="../UI_1/login.php" method="post"> <!-- Delete Account and return to log in page -->
                <input type="submit" name="logout" value="Log Out" id="logout"><br><br><br>
            </form>
            <a href="edit_acc_p.php" class="back">Edit Account</a>
        </div>
    </div>
</body>
</html>