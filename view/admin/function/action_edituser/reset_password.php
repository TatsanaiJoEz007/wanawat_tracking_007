<?php
// connect to database
require_once('../../../config/connect.php');
require_once('../action_activity_log/log_activity.php'); // Include log_activity.php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['newPassword']) && isset($_POST['user_id'])) {
        $new_password = $_POST['newPassword'];
        $user_id = $_POST['user_id'];

        // Check if user_id is empty
        if (empty($user_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Missing user_id']);
            exit;
        }

        // Check if user exists in the database
        $query = $conn->prepare('SELECT user_email FROM tb_user WHERE user_id = ?');
        $query->bind_param('i', $user_id);
        $query->execute();
        $result = $query->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $user_email = $user['user_email'];

            // Update the password with MD5 hash
            $update_query = $conn->prepare('UPDATE tb_user SET user_pass = ? WHERE user_id = ?');
            $hashed_password = md5($new_password);
            $update_query->bind_param('si', $hashed_password, $user_id);

            if ($update_query->execute()) {
                // Log the password reset activity
                $admin_user_id = 1; // Assuming the admin's user_id is 1; adjust accordingly
                $action = 'reset password';
                $entity = 'user';
                $entity_id = $user_id;
                $additional_info = "Reset password for user with email: " . $user_email;

                logAdminActivity($admin_user_id, $action, $entity, $entity_id, $additional_info);

                echo json_encode(['status' => 'success', 'message' => 'Password reset successful']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to reset password']);
            }
            $update_query->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User does not exist']);
        }

        $query->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing parameters']);
    }
}
?>
