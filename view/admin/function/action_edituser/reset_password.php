<?php
// connect to database
require_once('../../../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['newPassword']) && isset($_POST['user_id'])) {
        $new_password = $_POST['newPassword'];
        $user_id = $_POST['user_id'];

        // Check if user_id is empty
        if (empty($user_id)) {
            echo 'error: missing user_id';
            exit;
        }

        // Check if user exists in the database
        $query = $conn->prepare('SELECT * FROM tb_user WHERE user_id = ?');
        $query->bind_param('s', $user_id);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            // Update the password with MD5 hash
            $update_query = $conn->prepare('UPDATE tb_user SET user_pass = ? WHERE user_id = ?');
            $hashed_password = md5($new_password);
            $update_query->bind_param('ss', $hashed_password, $user_id);
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
    } else {
        echo 'error: missing parameters';
    }
}
?>
