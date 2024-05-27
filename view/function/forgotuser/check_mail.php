<?php
// connect to database
require_once('../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = $_POST['email']; // เปลี่ยนจาก user_email เป็น email
    // Check if email exists in the database
    $query = $db->prepare('SELECT * FROM tb_user WHERE user_email = ?');
    $query->execute([$user_email]);

    if ($query->rowCount() > 0) {
        echo 'exists';
    } else {
        echo 'notexists';
    }
}
?>
