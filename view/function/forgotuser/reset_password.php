<?php
// connect to database
require_once('../../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = $_POST['email']; // Ensure the name matches the input in the form
    $new_password = $_POST['newPassword'];

    // Check if email exists in the database
    $query = $conn->prepare('SELECT * FROM tb_user WHERE user_email = ?');
    $query->bind_param('s', $user_email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        // Update the password with MD5 hash
        $update_query = $conn->prepare('UPDATE tb_user SET user_pass = ? WHERE user_email = ?');
        $hashed_password = md5($new_password); // Using MD5 hash
        $update_query->bind_param('ss', $hashed_password, $user_email);
        if ($update_query->execute()) {
            echo 'success';
        } else {
            echo 'error';
        }
        $update_query->close();
    } else {
        echo 'notexists';
    }

    $query->close();
}
?>
