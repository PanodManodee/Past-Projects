<!-- Delete User Then return to My Ticket -->
<?php
session_start();
require_once("dbconn.php");
$aid=$_SESSION["admin_id"];
$cid = $_GET["cid"];

$q="update customer set status = 1, admins = $aid where customer_id=$cid";
			if(!$mysqli->query($q)){
				echo "Ban failed. Error: ".$mysqli->error ;
		   }
		   $mysqli->close();
		   //redirect
		   header("Location: user_a.php");
           exit();
?>