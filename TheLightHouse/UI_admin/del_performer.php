<?php
session_start();
require_once("dbconn.php");
$aid=$_SESSION["admin_id"];
$pid = $_GET["pid"];

$q = "update performer set status = 0, admins = $aid where performer_id=$pid";
if (!$mysqli->query($q)) {
	echo "DELETE failed. Error: " . $mysqli->error;
}

while ($mysqli->more_results()) {
	$mysqli->next_result();
	if ($result = $mysqli->store_result()) {
		$result->free();
	}
}
$mysqli->close();
//redirect
header("Location: performer_a.php");
exit();
?>