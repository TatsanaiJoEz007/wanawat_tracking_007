<?php
include '../../config/connect.php';
require_once('../function/action_activity_log/log_activity.php'); // Include log_activity.php

session_start(); // Start the session to get the admin's user_id

if (isset($_GET['id'])) {
    $faq_id = $_GET['id'];
    
    // Get the admin's user_id from the session
    if (isset($_SESSION['user_id'])) {
        $admin_user_id = $_SESSION['user_id'];

        // Get the FAQ details for logging
        $faq_query = $conn->prepare("SELECT * FROM tb_freq WHERE freq_id = ?");
        $faq_query->bind_param('i', $faq_id);
        $faq_query->execute();
        $faq_result = $faq_query->get_result();

        if ($faq_result->num_rows > 0) {
            $faq = $faq_result->fetch_assoc();
            $faq_title = $faq['freq_header']; // Assuming there's a title field in tb_freq

            // Delete FAQ
            $delete_query = $conn->prepare("DELETE FROM tb_freq WHERE freq_id = ?");
            $delete_query->bind_param('i', $faq_id);
            if ($delete_query->execute()) {
                // Log the FAQ deletion
                $action = "delete faq";
                $entity = "faq";
                $entity_id = $faq_id;
                $additional_info = "Deleted FAQ with title: " . $faq_title;

                if (logAdminActivity($admin_user_id, $action, $entity, $entity_id, $additional_info)) {
                    echo "FAQ deleted successfully and logged.";
                } else {
                    echo "FAQ deleted successfully, but failed to log the activity.";
                }
            } else {
                echo "Failed to delete FAQ.";
            }
            $delete_query->close();
        } else {
            echo "FAQ not found.";
        }
        $faq_query->close();
    } else {
        echo "Unauthorized action.";
    }
} else {
    echo "Invalid request.";
}

mysqli_close($conn);
?>
