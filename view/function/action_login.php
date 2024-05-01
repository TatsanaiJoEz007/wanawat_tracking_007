<?php 
session_start();
require_once('../config/connect.php');

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = md5($_POST['password']);

    // Using prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM tb_user WHERE user_username = ? AND user_pass = ?");
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if ($user['status'] != 0) {
            $_SESSION['login'] = true;
            $_SESSION['user_id'] = $user['User_ID'];
            $_SESSION['user_img'] = $user['user_img'];
            $_SESSION['user_username'] = $user['user_username'];
            $_SESSION['user_create'] = $user['created_at'];
            if ($user['user_type'] == 999) {
                // Check if the user type is 999 directly
                $_SESSION['user_type'] = 'admin';
                echo 'admin'; // This response will be handled in JavaScript
            } else {
                $_SESSION['user_type'] = 'user';
            }
        } else {
            echo 'close';
        }
    } else {
        echo 'fail';
    }
    $stmt->close();
}
?>
