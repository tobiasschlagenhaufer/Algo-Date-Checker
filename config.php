<?php
$host="localhost";
$username="root";
$password="root";
$db="algo_update_tool";

$pdo = new PDO("mysql:dbname=$db;host=$host", $username, $password);	

$updateTable = "updates";
?>