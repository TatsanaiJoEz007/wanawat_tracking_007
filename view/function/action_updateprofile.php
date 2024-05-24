<?php
session_start();

require_once('../config/connect.php');

$response = array(); // Initialize response array

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receive data from the form
    $user_firstname = $_POST['user_firstname'];
    $user_lastname = $_POST['user_lastname'];
    $user_email = $_POST['user_email'];
    $user_tel = $_POST['user_tel'];

    // For debugging: Log received form data
    error_log("Received data: Firstname: $user_firstname, Lastname: $user_lastname, Email: $user_email, Tel: $user_tel");

    // Check if avatar uploaded
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        // Define target directory and file name to save the avatar
        $target_dir = "../uploads/avatars/";
        $user_img = $target_dir . basename($_FILES['avatar']['name']);

        // Check file type
        $imageFileType = strtolower(pathinfo($user_img, PATHINFO_EXTENSION));
        $valid_extensions = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($imageFileType, $valid_extensions)) {
            $response['success'] = false;
            $response['message'] = 'Only image files are allowed';
            echo json_encode($response);
            exit;
        }

        // Move uploaded file to the target directory
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $user_img)) {
            // For debugging: Log file upload success
            error_log("File uploaded successfully: $user_img");

            // Encode image to base64
            $user_img_base64 = base64_encode(file_get_contents($user_img));
            // For debugging: check if base64 encoding is successful
            error_log("Base64 Image: " . substr($user_img_base64, 0, 100)); // Log first 100 characters

            // Update profile information in the database
            $sql = "UPDATE tb_user SET user_firstname = '$user_firstname', user_lastname = '$user_lastname', user_email = '$user_email', user_tel = '$user_tel', user_img = '$user_img_base64' WHERE user_id = '$_SESSION[user_id]'";
            if ($conn->query($sql) === TRUE) {
                $response['success'] = true;
                $response['message'] = 'Profile updated successfully';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error updating profile: ' . $conn->error;
                // For debugging: Log SQL error
                error_log("SQL Error: " . $conn->error);
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'Error uploading image';
            // For debugging: Log file upload error
            error_log("Error uploading image: " . $_FILES['avatar']['error']);
        }
    } else {
        // If no image uploaded
        // Update profile information in the database without image
        $sql = "UPDATE tb_user SET user_firstname = '$user_firstname', user_lastname = '$user_lastname', user_email = '$user_email', user_tel = '$user_tel' WHERE user_id = '$_SESSION[user_id]'";
        if ($conn->query($sql) === TRUE) {
            $response['success'] = true;
            $response['message'] = 'Profile updated successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Error updating profile: ' . $conn->error;
            // For debugging: Log SQL error
            error_log("SQL Error: " . $conn->error);
        }
    }

    // Close database connection
    $conn->close();

    // Send JSON response
    header('Content-Type: application/json'); // Set content type as JSON
    echo json_encode($response);
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method';
    header('Content-Type: application/json'); // Set content type as JSON
    echo json_encode($response);
}
?>
