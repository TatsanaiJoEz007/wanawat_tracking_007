<?php
header('Content-Type: application/json');
require_once('../config/connect.php');
require_once('../admin/function/action_activity_log/log_activity.php'); // Include log_activity.php

$response = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        isset($_POST['register-firstname'], 
              $_POST['register-lastname'], 
              $_POST['register-email'], 
              $_POST['register-password'], 
              $_POST['register-c-password'], 
              $_POST['register-address'], 
              $_POST['province_id'], 
              $_POST['amphure_id'], 
              $_POST['district_id'], 
              $_POST['register-tel'])
    ) {
        
        $firstname = $_POST['register-firstname'];
        $lastname = $_POST['register-lastname'];
        $email = $_POST['register-email'];
        $password = $_POST['register-password'];
        $confirm_password = $_POST['register-c-password'];
        $address = $_POST['register-address'];
        $province_id = $_POST['province_id'];
        $amphure_id = $_POST['amphure_id'];
        $district_id = $_POST['district_id'];
        $tel = $_POST['register-tel'];
        
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
                
                $stmt = $conn->prepare("INSERT INTO tb_user (user_firstname, user_lastname, user_email, user_pass, user_address, province_id, amphure_id, district_id, user_tel, user_img, user_type, user_create_at, user_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)");
                $user_img = ''; // Assuming a default image
                $user_type = 0; // Default user type
                $user_status = 1; // Default user status

                $stmt->bind_param("sssssiisssii", $firstname, $lastname, $email, $hashed_password, $address, $province_id, $amphure_id, $district_id, $tel, $user_img, $user_type, $user_status);

                if ($stmt->execute()) {
                    // Get the ID of the newly registered user
                    $new_user_id = $stmt->insert_id;
                    
                    // Log the registration activity
                    $admin_user_id = $new_user_id; // Assuming the new user logs themselves
                    $action = 'register';
                    $entity = 'user';
                    $entity_id = $new_user_id;
                    $additional_info = "New user registered with email: " . $email;

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
