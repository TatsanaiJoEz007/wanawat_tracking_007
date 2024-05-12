<?php 
$host = "localhost";
$username = "root";
$pass = "123";
$db = "wanawat_tracking";
$conn = new mysqli($host,$username,$pass,$db);
$conn->set_charset("utf8");
date_default_timezone_set('Asia/bangkok');
if(!$conn){
    echo "error to connect database";
}
?>