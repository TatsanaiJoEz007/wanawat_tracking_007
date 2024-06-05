<?php
header('Content-Type: application/json');
require_once('../../../config/connect.php');
require_once('../../function/action_activity_log/log_activity.php'); // Include log_activity.php

$response = [];

session_start(); // Make sure the session is started

// Debug: Output all received POST data
$response['post_data'] = $_POST;
$response['files_data'] = $_FILES;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $missing_fields = [];

    // Check each required field
    $required_fields = ['emp_firstname', 'emp_lastname', 'emp_email', 'emp_pass'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    // Set default value for admin_status if not provided
    if (!isset($_POST['admin_status'])) {
        $_POST['admin_status'] = 1;
    }

    // Check if any required fields are missing
    if (!empty($missing_fields)) {
        $response['success'] = false;
        $response['message'] = 'Required fields are missing: ' . implode(', ', $missing_fields);
    } else {
        // Sanitize and assign the input values
        $firstname = $conn->real_escape_string($_POST['emp_firstname']);
        $lastname = $conn->real_escape_string($_POST['emp_lastname']);
        $email = $conn->real_escape_string($_POST['emp_email']);
        $password = $_POST['emp_pass'];
        $status = (int)$_POST['emp_status'];

        // Check if email is already in use
        $email_check_stmt = $conn->prepare("SELECT user_id FROM tb_user WHERE user_email = ?");
        $email_check_stmt->bind_param("s", $email);
        $email_check_stmt->execute();
        $email_check_stmt->store_result();

        if ($email_check_stmt->num_rows > 0) {
            $response['success'] = false;
            $response['message'] = 'Email is already in use.';
        } else {
            $hashed_password = md5($password); // Consider using password_hash() for better security

            // Handle file upload if provided
            if (isset($_FILES['emp_img']) && $_FILES['emp_img']['error'] === UPLOAD_ERR_OK) {
                $file_content = file_get_contents($_FILES['emp_img']['tmp_name']);
                $user_img = $conn->real_escape_string($file_content);
            } else {
                $user_img = null; // Assuming no image
            }

            $stmt = $conn->prepare("INSERT INTO tb_user (user_firstname, user_lastname, user_email, user_pass, user_img, user_type, user_create_at, user_status) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");
            if (!$stmt) {
                $response['success'] = false;
                $response['message'] = 'Database error: ' . $conn->error;
                echo json_encode($response);
                exit;
            }

            $user_type = 1; // Default user type
            $stmt->bind_param("ssssbsi", $firstname, $lastname, $email, $hashed_password, $user_img, $user_type, $status);

            if ($stmt->execute()) {
                // Log the activity
                $admin_user_id = $_SESSION['user_id']; // Assuming admin user_id is stored in session
                $action = 'Add Employee';
                $entity = 'user';
                $entity_id = $conn->insert_id; // Get the last inserted ID
                $additional_info = "Added Employee with email: " . $email;
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
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
$conn->close();
?>
