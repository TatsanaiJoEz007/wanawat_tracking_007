<?php
require_once('../config/connect.php');

function registerUser($data)
{
    global $conn;

    // Extract data from JSON
    $firstname = mysqli_real_escape_string($conn, $data['register-firstname']);
    $lastname = mysqli_real_escape_string($conn, $data['register-lastname']);
    $email = mysqli_real_escape_string($conn, $data['register-email']);
    $password = mysqli_real_escape_string($conn, $data['register-password']);
    $cpassword = mysqli_real_escape_string($conn, $data['register-c-password']);
    $address = mysqli_real_escape_string($conn, $data['register-address']);
    $province_id = mysqli_real_escape_string($conn, $data['province_id']);
    $amphure_id = mysqli_real_escape_string($conn, $data['amphure_id']);
    $district_id = mysqli_real_escape_string($conn, $data['district_id']);
    $tel = mysqli_real_escape_string($conn, $data['register-tel']);

    // Check if password and confirmation password match
    if ($password !== $cpassword) {
        $response = array("success" => false, "message" => "Password and confirmation password do not match");
        return json_encode($response);
    }

    // Hash the password before storing it in the database
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $sql = "INSERT INTO tb_user (user_firstname, user_lastname, user_email, user_pass, user_img , user_type , user_address, province_id, amphure_id, district_id, user_tel , user_create_at) 
        VALUES ('$firstname', '$lastname', '$email', '$hashedPassword', '' , 0 , '$address', '$province_id', '$amphure_id', '$district_id', '$tel' , NOW())";

    if (mysqli_query($conn, $sql)) {
        $response = array("success" => true, "message" => "Registration successful");
    } else {
        $response = array("success" => false, "message" => "Error: " . mysqli_error($conn));
    }

    // Convert response to JSON
    return json_encode($response);
}

// Check if the request is POST and contains JSON data
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && !empty($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    // Get JSON data from the request body
    $jsonData = file_get_contents('php://input');

    // Decode JSON data
    $data = json_decode($jsonData, true);

    // Call the register function and echo the JSON response
    echo registerUser($data);
} else {
    // Invalid request
    echo json_encode(array("success" => false, "message" => "Invalid request"));
}

mysqli_close($conn);
