<?php
require_once('../../../config/connect.php');  // Adjust the path if necessary
session_start();

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delUser']) && isset($_POST['id'])) {
    $userId = intval($_POST['id']); // Sanitize user input
    
    // Check if the user is logged in and has permission
    if (isset($_SESSION['user_id'])) {
        // Prepare and execute the delete query
        $stmt = $conn->prepare("DELETE FROM tb_user WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            // Check if any rows were affected
            if ($stmt->affected_rows > 0) {
                $response['status'] = 'success';
                $response['message'] = 'User deleted successfully.';
            } else {
                $response['status'] = 'error';
                $response['message'] = 'User not found or already deleted.';
            }
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Database error: ' . $stmt->error;
        }
        
        $stmt->close();
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Unauthorized action.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request.';
}

// Send the response as JSON
header('Content-Type: application/json');
echo json_encode($response);

$conn->close();
?>