<?php 
session_start();
require_once('../config/connect.php');

if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $pass = md5($_POST['pass']);
    $name = $_POST['name'];
    $check_usr = "SELECT * FROM tb_user WHERE user_username = '$email'";
    $query = $conn->query($check_usr);
    if ($query->num_rows >= 1) {
        echo 'fail';
    } else {
        $sql = "INSERT INTO tb_user(username,user_username,user_pass,user_type,created_at) VALUES('$name','$email','$pass',0,NOW())";
        $conn->query($sql);
        $check_pass = $conn->query("SELECT * FROM tb_user WHERE user_username = '$email' AND user_pass = '$pass'");
        $user = $check_pass->fetch_array();
        $_SESSION['login'] = true;
        $_SESSION['user_type'] = 'user';
        $_SESSION['user_img'] = $user['user_img'];
        $_SESSION['user_id'] = $user['User_ID'];
        $_SESSION['user_username'] = $user['user_username'];
        $_SESSION['user_password'] = $user['user_pass'];
        $_SESSION['user_create'] = $user['created_at'];
    }
}

?>