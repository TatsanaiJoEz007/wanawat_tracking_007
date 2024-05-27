<?php
// connect to database
require_once('../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = $_POST['email']; // รับค่า email จาก POST
    $newPassword = password_hash($_POST['newPassword'], PASSWORD_BCRYPT); // เข้ารหัสรหัสผ่านใหม่

    // Update password in the database
    $query = $db->prepare('UPDATE tb_user SET user_password = ? WHERE user_email = ?');
    if ($query->execute([$newPassword, $user_email])) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
