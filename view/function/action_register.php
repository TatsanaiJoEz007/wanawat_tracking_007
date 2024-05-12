<?php
header('Content-Type: application/json'); // Set header to indicate JSON response
require_once('../config/connect.php'); // Include your database connection

$response = array(); // Initialize response array

if(isset($_POST['register'])) {
    // Retrieve form data
    $firstname = mysqli_real_escape_string($conn, $_POST['register-firstname']);
    $lastname = mysqli_real_escape_string($conn, $_POST['register-lastname']);
    $email = mysqli_real_escape_string($conn, $_POST['register-email']);
    $password = mysqli_real_escape_string($conn, $_POST['register-password']);
    $c_password = mysqli_real_escape_string($conn, $_POST['register-c-password']);
    $address = mysqli_real_escape_string($conn, $_POST['register-address']);
    $province_id = mysqli_real_escape_string($conn, $_POST['province_id']);
    $amphure_id = mysqli_real_escape_string($conn, $_POST['amphure_id']);
    $district_id = mysqli_real_escape_string($conn, $_POST['district_id']);
    $tel = mysqli_real_escape_string($conn, $_POST['register-tel']);
    $created_at = date('Y-m-d H:i:s'); // Current timestamp

    // Check if passwords match
    if($password != $c_password) {
        $response['success'] = false;
        $response['message'] = "Passwords do not match";
    } else {
        // Hash the password with MD5
        $hashed_password = md5($password);

        // Insert user data into database
        $sql = "INSERT INTO tb_user (user_firstname, user_lastname, user_email, user_pass, user_address, province_id, amphure_id, district_id, user_tel, user_create_at, user_status)
                VALUES ('$firstname', '$lastname', '$email', '$hashed_password', '$address', '$province_id', '$amphure_id', '$district_id', '$tel', '$created_at', 1)";

        if(mysqli_query($conn, $sql)) {
            $response['success'] = true;
            $response['message'] = "Registration successful";
        } else {
            $response['success'] = false;
            $response['message'] = "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    }
} else {
    $response['success'] = false;
    $response['message'] = "No data received";
}

// Close the database connection
mysqli_close($conn);

// Return JSON response
echo json_encode($response);
?>
