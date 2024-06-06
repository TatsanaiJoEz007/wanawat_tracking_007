<?php
session_start();
require_once('../config/connect.php');
require_once('../admin/function/action_activity_log/log_activity.php'); // Include log_activity.php

if (isset($_POST['logout'])) {
    if (isset($_SESSION['user_id'])) {
        $admin_user_id = $_SESSION['user_id'];
        $action = 'logout';
        $entity = 'user';
        $entity_id = $admin_user_id;
        $additional_info = "User logged out with email: " . $_SESSION['user_email'];

        // บันทึกการล็อกเอาท์ใน log_activity
        logAdminActivity($admin_user_id, $action, $entity, $entity_id, $additional_info);

        // ล้างค่า Session ทั้งหมด
        session_destroy();

        echo json_encode(['status' => 'success', 'message' => 'Logout successful.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No user is logged in.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
