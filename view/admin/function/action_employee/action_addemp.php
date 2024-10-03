<?php
header('Content-Type: application/json');
require_once('../../../../view/config/connect.php');
require_once('../../function/action_activity_log/log_activity.php'); // Include log_activity.php

$response = [];

session_start(); // Make sure the session is started

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $missing_fields = [];

    // Check each required field
    $required_fields = ['emp_firstname', 'emp_lastname', 'emp_email', 'emp_pass'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }

    // Set default value for emp_status if not provided
    $status = isset($_POST['emp_status']) ? $_POST['emp_status'] : 1;

    // Check if any required fields are missing
    if (!empty($missing_fields)) {
        $response['success'] = false;
        $response['message'] = 'Required fields are missing: ' . implode(', ', $missing_fields);
    } else {
        // Sanitize and assign the input values
        $firstname = $_POST['emp_firstname'];
        $lastname = $_POST['emp_lastname'];
        $email = $_POST['emp_email'];
        $password = $_POST['emp_pass'];

        // Check if email is already in use
        $email_check_stmt = $conn->prepare("SELECT user_id FROM tb_user WHERE user_email = ?");
        $email_check_stmt->bind_param("s", $email);
        $email_check_stmt->execute();
        $email_check_stmt->store_result();

        if ($email_check_stmt->num_rows > 0) {
            $response['success'] = false;
            $response['message'] = 'Email is already in use.';
        } else {
            // แฮชรหัสผ่านด้วย md5
            $hashed_password = md5($password);

            // Handle file upload (image)
            $user_img = null; // Default null if no image is uploaded
            if (isset($_FILES['emp_img']) && $_FILES['emp_img']['error'] == 0) {
                // Get the temporary file path
                $img_tmp_name = $_FILES['emp_img']['tmp_name'];
                // Read the file contents as binary data
                $user_img = file_get_contents($img_tmp_name);
            }

            // Set user_type as 1 for employee
            $user_type = 1;

            // Prepare the SQL statement to insert the new employee, including the image as Blob
            $stmt = $conn->prepare("INSERT INTO tb_user (user_firstname, user_lastname, user_email, user_pass, user_img, user_type, user_create_at, user_status) VALUES (?, ?, ?, ?, ?, ?, NOW(), ?)");

            // Bind parameters, including user_type and image
            $stmt->bind_param("sssssis", $firstname, $lastname, $email, $hashed_password, $user_img, $user_type, $status);

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
