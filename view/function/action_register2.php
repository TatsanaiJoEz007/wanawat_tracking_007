<?php
session_start();
require_once('../config/connect.php');

if (isset($_POST['register'])) {
    $user_email = $_POST['user_email'];
    $user_pass = password_hash($_POST['user_pass'], PASSWORD_DEFAULT);
    $user_firstname = $_POST['user_firstname'];
    $user_lastname = $_POST['user_lastname'];
    $user_address = $_POST['user_address'];
    $province_id = $_POST['province_id'];
    $amphure_id = $_POST['amphure_id'];
    $district_id = $_POST['district_id'];
    $user_tel = $_POST['user_tel'];

    $stmt = $conn->prepare("SELECT * FROM tb_user WHERE user_email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows >= 1) {
        echo 'fail';
    } else {
        $stmt = $conn->prepare("INSERT INTO tb_user(user_firstname,user_lastname,user_email,user_pass,user_address,province_id,amphure_id,district_id,user_tel,user_type,created_at) 
        VALUES(?,?,?,?,?,?,?,?,?,0,NOW())");
        $stmt->bind_param("sssssssss", $user_firstname, $user_lastname, $user_email, $user_pass, $user_address, $province_id, $amphure_id, $district_id, $user_tel);
        $stmt->execute();

        $stmt = $conn->prepare("SELECT * FROM tb_user WHERE user_email = ?");
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_array();

        $_SESSION['login'] = true;
        $_SESSION['user_type'] = 'user';
        $_SESSION['user_img'] = $user['user_img'];
        $_SESSION['user_email'] = $user['user_email'];
        $_SESSION['user_password'] = $user['user_pass'];
        $_SESSION['user_firstname'] = $user['user_firstname'];
        $_SESSION['user_lastname'] = $user['user_lastname'];
        $_SESSION['user_address'] = $user['user_address'];
        $_SESSION['province_id'] = $user['province_id'];
        $_SESSION['amphure_id'] = $user['amphure_id'];
        $_SESSION['district_id'] = $user['district_id'];
        $_SESSION['user_tel'] = $user['user_tel'];
        $_SESSION['created_at'] = $user['created_at'];
    }
}
?>