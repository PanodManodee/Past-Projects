<?php



session_start();
require_once('dbconn.php');

$name = $_GET['name'];
$date = $_GET['date'];

if (isset($_GET['search_name'])) {
    $q = "select * from session s inner join performer p on s.performers = p.performer_id inner join equipment e on s.session_id = e.session";
    if($name && !empty($name)){
        $q .= " and session_name like '%$name%' or username like '%$name%'";
    } if($date && !empty($date)){
        $q .= " and session_date like '%$date%'";
    }
} else {
    $q = "select * from session s inner join performer p on s.performers = p.performer_id inner join equipment e on s.session_id = e.session;";
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
    <title>Requests</title>
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
            <h2>Requests</h2>
            <form action="requests.php" method="GET">
                <label>Search by Name</label>
                <input type="text" name="name">
                <input type="submit" name="search_name" value="Search"><br><br><br>

                <!-- If you want to add date to equipment table -->

                <!-- ***** could do search date (from x to y) if it doesn't take to long, 
 other wise stick with this layout-->

                <label>Search by request date</label>
                <input type="date" name="date"><br><br><br>
            </form>
            <table>

                <tr>
                    <th style="column-width: 15em; ">Session</th>
                    <th style="column-width: 15em; ">From</td>
                    <th style="column-width: 3em;"></th>
                    <!-- <th style="column-width: 3em;"></th> -->
                </tr>

                <!-- %%%%%%%%%%%%%%% DISPLAY ALL OF THE TICKET BELONGING TO THE USER %%%%%%%%%%%%%%%-->


                <!-- Demo -->

                <?php
                    while ( $row = $result->fetch_array() ){ ?>
                <tr>
                    <td style="<? if( $row['req_approval'] == 1){ echo "background-color: rgb(128, 254, 141)";} else { echo "background-color: rgb(254, 115, 115)";}?>"><? echo $row['session_name']?></td>
                    <td style="<? if( $row['req_approval'] == 1){ echo "background-color: rgb(128, 254, 141)";} else { echo "background-color: rgb(254, 115, 115)";}?>"><? echo $row['p_alias']?></td>
                    <td style="<? if( $row['req_approval'] == 1){ echo "background-color: rgb(128, 254, 141)";} else { echo "background-color: rgb(254, 115, 115)";}?>"><a href="check_req.php?sid=<? echo $row['session_id']?>" id="edit">Detail</a></td>
                </tr>
                    
                   <?php } ?>
                


                <!-- End of Demo-->

            </table>
        </div>
    </div>
</body>

</html>