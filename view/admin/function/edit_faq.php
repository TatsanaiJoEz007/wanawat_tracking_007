<?php
include '../../config/connect.php';
require_once('../function/action_activity_log/log_activity.php'); // Include log_activity.php

session_start(); // Ensure session is started

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if FAQ ID and new data are provided
    if (isset($_GET['id']) && isset($_POST['header']) && isset($_POST['content'])) {
        $faq_id = $_GET['id'];
        $header = $_POST['header'];
        $content = $_POST['content'];

        // Prepare and execute query to update FAQ
        $query = "UPDATE tb_freq SET freq_header = ?, freq_content = ? WHERE freq_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssi', $header, $content, $faq_id);

        if ($stmt->execute()) {
            // Log admin activity
            if (isset($_SESSION['user_id'])) {
                $user_id = $_SESSION['user_id'];
                $action = "edit faq";
                $entity = "faq";
                $entity_id = $faq_id;
                $additional_info = "Edited FAQ with header: $header";

                logAdminActivity($user_id, $action, $entity, $entity_id, $additional_info);
            }

            // FAQ updated successfully
            http_response_code(200); // OK
            echo json_encode(array("message" => "FAQ updated successfully."));
        } else {
            // Failed to update FAQ
            http_response_code(500); // Internal Server Error
            echo json_encode(array("message" => "Failed to update FAQ."));
        }
        $stmt->close();
    } else {
        // Missing parameters
        http_response_code(400); // Bad Request
        echo json_encode(array("message" => "FAQ ID, header, and content are required."));
    }
} else {
    // Invalid request method
    http_response_code(405); // Method Not Allowed
    echo json_encode(array("message" => "Only POST method is allowed."));
}

// Close database connection
$conn->close();
?>
