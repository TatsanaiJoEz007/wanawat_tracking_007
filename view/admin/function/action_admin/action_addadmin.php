<?php
header('Content-Type: application/json');
require_once('../../../config/connect.php');
require_once('../../function/action_activity_log/log_activity.php'); // Include log_activity.php

$response = [];

session_start(); // Make sure the session is started

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        isset($_POST['admin-firstname'], 
              $_POST['admin-lastname'], 
              $_POST['admin-email'], 
              $_POST['admin-pass'], 
              $_POST['admin-img'],
              $_POST['admin-status'])
    ) {
        
        $firstname = $_POST['admin-firstname'];
        $lastname = $_POST['admin-lastname'];
        $email = $_POST['admin-email'];
        $password = $_POST['admin-pass'];
        $img = $_POST['admin-img'];
        $status = $_POST['admin-status'];
        
        // Validate passwords match
        if ($password !== $confirm_password) {
            $response['success'] = false;
            $response['message'] = 'Passwords do not match.';
        } else {
            // Check if email is already in use
            $email_check_stmt = $conn->prepare("SELECT user_id FROM tb_user WHERE user_email = ?");
            $email_check_stmt->bind_param("s", $email);
            $email_check_stmt->execute();
            $email_check_stmt->store_result();
            
            if ($email_check_stmt->num_rows > 0) {
                $response['success'] = false;
                $response['message'] = 'Email is already in use.';
            } else {
                $hashed_password = md5($password); // Use password_hash for better security
                
                $stmt = $conn->prepare("INSERT INTO tb_user (user_firstname, user_lastname, user_email, user_pass, user_img, user_type, user_create_at, user_status) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
                $user_img = ''; // Assuming a default image
                $user_type = 999; // Default user type
                $user_status = 1; // Default user status

                $stmt->bind_param("ssssssii", $firstname, $lastname, $email, $hashed_password, $user_img, $user_type, $user_status);

                if ($stmt->execute()) {
                    // Log the activity
                    $admin_user_id = $_SESSION['user_id']; // Assuming admin user_id is stored in session
                    $action = 'add user';
                    $entity = 'user';
                    $entity_id = $conn->insert_id; // Get the last inserted ID
                    $additional_info = "Added user with email: " . $email;
                    logAdminActivity($admin_user_id, $action, $entity, $entity_id, $additional_info);

                    $response['success'] = true;
                    $response['message'] = 'Registration successful.';
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Error: ' . $stmt->error;
                }
                $stmt->close();
            }
            $email_check_stmt->close();
        }
    } else {
        $response['success'] = false;
        $response['message'] = 'Required fields are missing.';
    }
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
$conn->close();
?>
