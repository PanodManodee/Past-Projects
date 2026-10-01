<!-- Delete User Then return to My Ticket -->
<?php
session_start();
require_once("dbconn.php");
$aid=$_SESSION["admin_id"];
$pid = $_GET["pid"];

$q="update performer set status = 1, admins = $aid where performer_id=$pid";
			if(!$mysqli->query($q)){
				echo "Ban failed. Error: ".$mysqli->error ;
		   }
		   $mysqli->close();
		   //redirect
		   header("Location: performer_a.php");
           exit();
?>