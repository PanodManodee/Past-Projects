<?php
$mysqli = new mysqli('localhost','root','root','thelighthouse');
   if($mysqli->connect_errno){
      echo $mysqli->connect_errno.": ".$mysqli->connect_error;
   }
?>