<?php
session_start();
require_once('../config/connect.php');

// ตรวจสอบว่ามีการส่งข้อมูลการลงทะเบียนหรือไม่
if (isset($_POST['register']) && $_POST['register'] == '1') {
    $email = $_POST['email'];
    $pass = md5($_POST['pass']);
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];

    // ตรวจสอบว่าอีเมลนี้มีอยู่แล้วในฐานข้อมูลหรือไม่
    $check_usr = "SELECT * FROM tb_user WHERE user_email = '$email'";
    $query = $conn->query($check_usr);
    if ($query->num_rows >= 1) {
        echo 'fail';
    } else {
        // ถ้าไม่มี, เพิ่มข้อมูลผู้ใช้ใหม่ลงในฐานข้อมูล
        $sql = "INSERT INTO tb_user(user_firstname,user_lastname,user_email,user_pass,user_type,created_at) VALUES('$firstname','$lastname','$email','$pass',0,NOW())";
        $conn->query($sql);

        // หลังจากเพิ่มข้อมูลเรียบร้อย, ตรวจสอบข้อมูลผู้ใช้และกำหนดเซสชั่น
        $check_pass = $conn->query("SELECT * FROM tb_user WHERE user_email = '$email' AND user_pass = '$pass'");
        $user = $check_pass->fetch_array();
        $_SESSION['login'] = true;
        $_SESSION['user_type'] = 'user';
        $_SESSION['user_img'] = $user['user_img'];
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_firstname'] = $user['user_firstname'];
        $_SESSION['user_lastname'] = $user['user_lastname'];
        $_SESSION['user_email'] = $user['user_email'];
    }
} else {
    // ถ้าไม่มีข้อมูลการลงทะเบียนส่งมา, อาจจะรีเดียเรกต์หรือแสดงข้อความข้อผิดพลาด
    echo 'Invalid request';
}
?>
