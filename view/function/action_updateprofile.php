<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('../config/connect.php');

$response = array(); // Initialize response array

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Receive data from the form
    $user_firstname = $_POST['user_firstname'];
    $user_lastname = $_POST['user_lastname'];
    $user_email = $_POST['user_email'];
    $user_tel = $_POST['user_tel'];
    $user_id = $_SESSION['user_id'];
    
    // For debugging: Log received form data
    error_log("Received data: Firstname: $user_firstname, Lastname: $user_lastname, Email: $user_email, Tel: $user_tel");

    // Check if user_img uploaded
    if (isset($_FILES['user_img']) && $_FILES['user_img']['error'] == 0) {
        // Get the temporary location of the uploaded file
        $user_img_tmp_name = $_FILES['user_img']['tmp_name'];
        // Read the contents of the file
        $user_img_data = file_get_contents($user_img_tmp_name);

        // Prepare the SQL statement to update profile with user_img
        $sql = "UPDATE tb_user 
                SET user_firstname = ?, user_lastname = ?, user_email = ?, user_tel = ?, user_img = ? 
                WHERE user_id = ?";
        
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare($sql);
        // Bind parameters
        $stmt->bind_param("sssssi", $user_firstname, $user_lastname, $user_email, $user_tel, $user_img_data, $user_id);
    } else {
        // Prepare the SQL statement to update profile without user_img
        $sql = "UPDATE tb_user 
                SET user_firstname = ?, user_lastname = ?, user_email = ?, user_tel = ? 
                WHERE user_id = ?";
        
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare($sql);
        // Bind parameters
        $stmt->bind_param("ssssi", $user_firstname, $user_lastname, $user_email, $user_tel, $user_id);
    }

    // Execute the prepared statement
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Profile updated successfully';
    } else {
        $response['success'] = false;
        $response['message'] = 'Error updating profile: ' . $stmt->error;
        // For debugging: Log SQL error
        error_log("SQL Error: " . $stmt->error);
    }

    // Close statement
    $stmt->close();

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
