<?php
require_once('../../../config/connect.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the content type to JSON
header('Content-Type: application/json');

$response = [];

if (isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);
    $sql = "SELECT * FROM tb_user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            $response = $user;
        } else {
            $response['error'] = 'User not found';
        }
    } else {
        $response['error'] = 'Failed to prepare statement';
    }
} else {
    $response['error'] = 'User ID not provided';
}

echo json_encode($response);
?>
