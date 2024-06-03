<?php
session_start();
require_once('../config/connect.php');

// Read JSON input from the request body
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

$response = array(); // Initialize response array

if (isset($input['register']) && $input['register'] == '1') {
    $user_email = $input['user_email'];
    $user_pass = password_hash($input['user_pass'], PASSWORD_DEFAULT);
    $user_firstname = $input['user_firstname'];
    $user_lastname = $input['user_lastname'];
    $user_address = $input['user_address'];
    $province_id = $input['province_id'];
    $amphure_id = $input['amphure_id'];
    $district_id = $input['district_id'];
    $user_tel = $input['user_tel'];

    // Check if the user already exists
    $stmt = $conn->prepare("SELECT * FROM tb_user WHERE user_email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows >= 1) {
        $response['status'] = 'fail';
    } else {
        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO tb_user(user_firstname, user_lastname, user_email, user_pass, user_address, province_id, amphure_id, district_id, user_tel, user_type, user_create_at, user_status) 
        VALUES(?,?,?,?,?,?,?,?,?,0,NOW(), 1)"); // Assuming user_type = 0 for normal users and user_status = 1 for active users
        $stmt->bind_param("sssssssss", $user_firstname, $user_lastname, $user_email, $user_pass, $user_address, $province_id, $amphure_id, $district_id, $user_tel);
        $stmt->execute();

        // Fetch user information
        $stmt = $conn->prepare("SELECT * FROM tb_user WHERE user_email = ?");
        $stmt->bind_param("s", $user_email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Store user information in session
        $_SESSION['login'] = true;
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['user_img'] = $user['user_img'];
        $_SESSION['user_email'] = $user['user_email'];
        $_SESSION['user_firstname'] = $user['user_firstname'];
        $_SESSION['user_lastname'] = $user['user_lastname'];
        $_SESSION['user_address'] = $user['user_address'];
        $_SESSION['province_id'] = $user['province_id'];
        $_SESSION['amphure_id'] = $user['amphure_id'];
        $_SESSION['district_id'] = $user['district_id'];
        $_SESSION['user_tel'] = $user['user_tel'];
        $_SESSION['user_create_at'] = $user['user_create_at'];
        $_SESSION['user_status'] = $user['user_status'];

        $response['status'] = 'success'; // Registration successful
    }
}

// Log response data
error_log("Response Data: " . json_encode($response));

// Output JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
