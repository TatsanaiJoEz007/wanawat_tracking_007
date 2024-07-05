<?php 
$host = "localhost";
$username = "root";
$pass = "root";
$db = "wanawat_tracking";

$conn = new mysqli($host, $username, $pass, $db);
$conn->set_charset("utf8");
date_default_timezone_set('Asia/Bangkok');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
