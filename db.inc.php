<?php
$hostname = "localhost:3306";
$username = "c00289426";
$password = "Claudiu2026?";
      
$dbname = "Myc00289426_";       
$con = mysqli_connect($hostname, $username, $password, $dbname);
if (!$con)
{
   die("Failed to connect to MYSQL: " . mysqli_connect_error());
} 
?>