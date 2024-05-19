<?php
header('Content-Type: application/json');
require_once('../config/connect.php');

$response = [];

// Retrieve the input from the request body
$input = json_decode(file_get_contents('php://input'), true);
error_log('Received input: ' . print_r($input, true));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        isset($input['register-firstname'], 
              $input['register-lastname'], 
              $input['register-email'], 
              $input['register-password'], 
              $input['register-c-password'], 
              $input['register-address'], 
              $input['province_id'], 
              $input['amphure_id'], 
              $input['district_id'], 
              $input['register-tel'])
    ) {
        error_log('All required fields are present.');
        
        $firstname = $input['register-firstname'];
        $lastname = $input['register-lastname'];
        $email = $input['register-email'];
        $password = $input['register-password'];
        $confirm_password = $input['register-c-password'];
        $address = $input['register-address'];
        $province_id = $input['province_id'];
        $amphure_id = $input['amphure_id'];
        $district_id = $input['district_id'];
        $tel = $input['register-tel'];
        $user_img = ''; // Default value for user_img
        $user_type = 0; // Default value for user_type
        $user_create_at = date('Y-m-d H:i:s'); // Default value for user_create_at
        $user_status = 1; // Default value for user_status

        // Validate and process form data
        if ($password !== $confirm_password) {
            $response['success'] = false;
            $response['message'] = 'Passwords do not match.';
        } else {
            error_log('Passwords match. Inserting user into database...');
            $hashed_password = md5($password); // Use MD5 to hash the password
            $stmt = $conn->prepare("INSERT INTO tb_user (user_firstname, user_lastname, user_email, user_pass, user_img, user_type, user_address, province_id, amphure_id, district_id, user_tel, user_create_at , user_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? , ?)");

            // Check if the statement was prepared successfully
            if ($stmt === false) {
                $response['success'] = false;
                $response['message'] = 'Prepare failed: ' . $conn->error;
            } else {
                $stmt->bind_param("sssssiisssiss", $firstname, $lastname, $email, $hashed_password, $address, $province_id, $amphure_id, $district_id, $tel, $user_img, $user_type, $user_create_at , $user_status);

                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Registration successful.';
                } else {
                    $response['success'] = false;
                    $response['message'] = 'Error: ' . $stmt->error;
                }
                $stmt->close();
            }
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
