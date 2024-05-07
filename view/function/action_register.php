<?php
session_start();
require_once('../config/connect.php');

// Check if registration data is sent
if (isset($_POST['register']) && $_POST['register'] == '1') {
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Note: Using md5 for demonstration, consider using more secure hashing algorithms
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $provinceId = $_POST['provinceId'];
    $amphureId = $_POST['amphureId'];
    $districtId = $_POST['districtId'];
    $tel = $_POST['tel'];

    // Check if the email already exists in the database
    $check_email_query = "SELECT * FROM tb_user WHERE user_email = '$email'";
    $email_result = $conn->query($check_email_query);
    if ($email_result->num_rows >= 1) {
        echo 'fail'; // Email already exists, return 'fail'
    } else {
        // If email does not exist, insert the new user data into the database
        $insert_query = "INSERT INTO tb_user (user_firstname, user_lastname, user_email, user_pass, user_address, province_id, amphure_id, district_id, user_tel, user_type, created_at) 
                        VALUES ('$firstname', '$lastname', '$email', '$password', '$address', '$provinceId', '$amphureId', '$districtId', '$tel', 0, NOW())";
        $insert_result = $conn->query($insert_query);

        if ($insert_result) {
            // After successful registration, retrieve user data and set session variables
            $user_query = "SELECT * FROM tb_user WHERE user_email = '$email' AND user_pass = '$password'";
            $user_result = $conn->query($user_query);
            if ($user_result->num_rows == 1) {
                $user = $user_result->fetch_assoc();
                $_SESSION['login'] = true;
                $_SESSION['user_type'] = 'user';
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_firstname'] = $user['user_firstname'];
                $_SESSION['user_lastname'] = $user['user_lastname'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['user_pass'] = $user['user_pass'];
                $_SESSION['user_address'] = $user['user_address'];
                $_SESSION['province_id'] = $user['province_id'];
                $_SESSION['amphure_id'] = $user['amphure_id'];
                $_SESSION['district_id'] = $user['district_id'];
                $_SESSION['user_tel'] = $user['user_tel'];

                // You can set more session variables as needed
            }
            echo 'success'; // Registration successful
        } else {
            echo 'error'; // Error inserting data into the database
        }
    }
} else {
    // If no registration data is sent, return 'Invalid request'
    echo 'Invalid request';
}
?>
