<?php
session_start();
require_once('dbconn.php');

if (isset($_GET['search_username'])) {
    if (isset($_GET['name']) && !empty($_GET['name'])) {
        $name = '%' . $_GET['name'] . '%';
        $q = "select * from customer where username like '$name'";
    } else {
        $q = "select * from customer";
    }
} else {
$q = "select * from customer";
}
$result = mysqli_query($mysqli, $q);
if (!$result) {
    echo "Select failed. Error:" . $mysqli->error;
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="style1.css">
</head>

<body>
    <!-- Header -->
    <?php include 'top_a.php'; ?>
    <div id="content">
        <br>
        <div id="back">
            <a href="home_a.php" class="back">Back</a>
        </div>
        <div id="main">
            <h2>Users</h2>
            <form action="user_a.php" method="GET">
                <label>Search by username</label>
                <input type="text" name="name">
                <input type="submit" name="search_username" value="Search"><br><br><br>
            </form>
            <table>

                <tr>
                    <th style="column-width: 15em; ">Username</td>
                    <th style="column-width: 15em;">Name</td>
                    <th style="column-width: 15em;">Balance</th>
                    <th style="column-width: 3em;"></th>
                    <th style="column-width: 3em;"></th>
                </tr>

                <!-- %%%%%%%%%%%%%%% DISPLAY USER %%%%%%%%%%%%%%%-->


                <!-- Demo -->

                <?php
                while ($row = $result->fetch_array()) { ?>
                    <tr>
                        <td style="<? if( $row['status'] == 1){ echo "background-color: rgb(128, 254, 141)";} else { echo "background-color: rgb(254, 115, 115)";}?>"><? echo $row['username'] ?></td>
                        <td style="<? if( $row['status'] == 1){ echo "background-color: rgb(128, 254, 141)";} else { echo "background-color: rgb(254, 115, 115)";}?>"><? echo $row['fname'] . ' ' . $row['lname'] ?></td>
                        <td style="<? if( $row['status'] == 1){ echo "background-color: rgb(128, 254, 141)";} else { echo "background-color: rgb(254, 115, 115)";}?>"><? echo "$" . $row['balance'] ?></td>
                        <td style="<? if( $row['status'] == 1){ echo "background-color: rgb(128, 254, 141)";} else { echo "background-color: rgb(254, 115, 115)";}?>"><a href="del_user.php?cid=<? echo $row['customer_id'] ?>" id="delete">Ban</a></td>
                        <td style="<? if( $row['status'] == 1){ echo "background-color: rgb(128, 254, 141)";} else { echo "background-color: rgb(254, 115, 115)";}?>"><a href="unban_user.php?cid=<? echo $row['customer_id'] ?>" id="delete">Unban</a></td>
                    </tr>
                <? } ?>


                <!-- End of Demo-->

            </table>
        </div>
    </div>
</body>

</html>