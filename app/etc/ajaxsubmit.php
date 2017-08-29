<?php
$connection = mysql_connect("localhost", "root", "CpEUQD3f"); // Establishing Connection with Server..
$db = mysql_select_db("trunited_mage", $connection); // Selecting Database
//Fetching Values from URL
$email2=$_POST['email1'];
//Insert query
$query = mysql_query("insert into popup_click(email) values ('$email2')");
echo "Form Submitted Succesfully";
mysql_close($connection); // Connection Closed
?>