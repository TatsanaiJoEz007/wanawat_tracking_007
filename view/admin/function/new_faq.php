<?php
// Include database connection and log activity function
include '../../config/connect.php';
require_once('../function/action_activity_log/log_activity.php'); // Include log_activity.php

session_start(); // Start the session

header('Content-Type: application/json'); // Set the header to return JSON

$response = array(); // Initialize a response array

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input fields
    $freq_header = mysqli_real_escape_string($conn, $_POST['freq_header']);
    $freq_content = mysqli_real_escape_string($conn, $_POST['freq_content']);

    // Insert new FAQ into the database
    $query = "INSERT INTO tb_freq (freq_header, freq_content, freq_create_at) VALUES ('$freq_header', '$freq_content', NOW())";
    if (mysqli_query($conn, $query)) {
        // Get the ID of the newly created FAQ
        $faq_id = mysqli_insert_id($conn);

        // Log admin activity
        if (isset($_SESSION['user_id'])) {
            $userId = $_SESSION['user_id']; 
            $action = "create faq";
            $entity = "faq";
            $entityId = $faq_id;
            $additionalInfo = "Created new FAQ with header: $freq_header";

            logAdminActivity($userId, $action, $entity, $entityId, $additionalInfo);
        }

        // Prepare a successful response
        $response['status'] = 'success';
        $response['message'] = 'New FAQ added successfully';
        echo json_encode($response); // Return JSON response
        exit();
    } else {
        // If there's an error with the query, send an error response
        $response['status'] = 'error';
        $response['message'] = 'Failed to add new FAQ';
        echo json_encode($response); // Return error JSON
        exit();
    }
} else {
    // If request method is not POST, send an error response
    $response['status'] = 'error';
    $response['message'] = 'Invalid request';
    echo json_encode($response); // Return error JSON
    exit();
}

// Close database connection
mysqli_close($conn);
?>