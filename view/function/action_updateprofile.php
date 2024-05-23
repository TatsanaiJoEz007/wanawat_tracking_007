<?php
session_start();

require_once('config/connect.php');

$response = array(); // Initialize response array

if (!isset($_SESSION['login'])) {
    // If not logged in, redirect to login page
    $response['success'] = false;
    $response['message'] = 'User not logged in';
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receive data from the form
    $user_firstname = $_POST['user_firstname'];
    $user_lastname = $_POST['user_lastname'];
    $user_email = $_POST['user_email'];
    $user_tel = $_POST['user_tel'];

    // Check if avatar uploaded
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        // Define target directory and file name to save the avatar
        $target_dir = "uploads/avatars/";
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
            // Update profile information in the database
            $sql = "UPDATE tb_user SET user_firstname = '$user_firstname', user_lastname = '$user_lastname', user_email = '$user_email', user_tel = '$user_tel', user_img = '$user_img' WHERE user_id = '$_SESSION[user_id]'";
            if ($conn->query($sql) === TRUE) {
                $response['success'] = true;
                $response['message'] = 'Profile updated successfully';
            } else {
                $response['success'] = false;
                $response['message'] = 'Error updating profile: ' . $conn->error;
            }
        } else {
            $response['success'] = false;
            $response['message'] = 'Error uploading image';
        }
    } else {
        // If no image uploaded
        // Update profile information in the database
        $sql = "UPDATE tb_user SET user_firstname = '$user_firstname', user_lastname = '$user_lastname', user_email = '$user_email', user_tel = '$user_tel' WHERE user_id = '$_SESSION[user_id]'";
        if ($conn->query($sql) === TRUE) {
            $response['success'] = true;
            $response['message'] = 'Profile updated successfully';
        } else {
            $response['success'] = false;
            $response['message'] = 'Error updating profile: ' . $conn->error;
        }
    }

    // Close database connection
    $conn->close();

    // Send JSON response
    echo json_encode($response);
} else {
    $response['success'] = false;
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
}
?>
