<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$pass = "";
$db = "wanawat_tracking";

$conn = new mysqli($host, $username, $pass, $db);
$conn->set_charset("utf8");
date_default_timezone_set('Asia/Bangkok');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$pdo = new PDO("mysql:host=$host;dbname=$db", $username, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>