<?php
header('Content-Type: application/json');
require_once('../config/connect.php');

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

        // Validate and process form data
        if ($password !== $confirm_password) {
            $response['success'] = false;
            $response['message'] = 'Passwords do not match.';
        } else {
            $hashed_password = md5($password); // Use MD5 to hash the password
            $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, address, province_id, amphure_id, district_id, tel) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssiiss", $firstname, $lastname, $email, $hashed_password, $address, $province_id, $amphure_id, $district_id, $tel);

            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Registration successful.';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error: ' . $stmt->error;
            }
            $stmt->close();
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
