<?php
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('../config/connect.php');
require_once('../admin/function/action_activity_log/log_activity.php'); // Include log_activity.php

// รับข้อมูล JSON หรือ POST data
$input = json_decode(file_get_contents('php://input'), true);
$is_logout_request = false;

// ตรวจสอบ request ทั้ง JSON และ POST
if ($input && isset($input['csrf_token'])) {
    // JSON request from JavaScript
    $is_logout_request = true;
    $csrf_token = $input['csrf_token'] ?? '';
} else if (isset($_POST['logout'])) {
    // Form POST request
    $is_logout_request = true;
    $csrf_token = $_POST['csrf_token'] ?? '';
} else {
    // Invalid request
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method',
        'error' => 'No logout request detected'
    ]);
    exit;
}

// ตรวจสอบว่าเป็น logout request
if ($is_logout_request) {
    // ตรวจสอบ CSRF Token (optional but recommended)
    if (isset($_SESSION['csrf_token']) && $csrf_token !== $_SESSION['csrf_token']) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid CSRF token',
            'error' => 'Security check failed'
        ]);
        exit;
    }

    // ตรวจสอบว่าผู้ใช้ล็อกอินอยู่หรือไม่
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $user_email = $_SESSION['user_email'] ?? 'Unknown';
        $login_time = $_SESSION['login_time'] ?? time(); // Get login time from session
        
        // Calculate session duration in seconds
        $logout_time = time();
        $session_duration = $logout_time - $login_time;
        
        try {
            // Update user activity tracking for logout
            $current_time = date('Y-m-d H:i:s');
            $update_logout = "UPDATE tb_user SET 
                user_last_logout = ?,
                user_is_online = 0,
                user_session_duration = ?,
                user_current_session_id = NULL,
                user_last_activity = ?
                WHERE user_id = ?";
            
            $update_stmt = $conn->prepare($update_logout);
            
            if ($update_stmt) {
                $update_stmt->bind_param("sisi", 
                    $current_time, 
                    $session_duration, 
                    $current_time,
                    $user_id
                );
                $update_stmt->execute();
                $update_stmt->close();
            }

            // บันทึกการล็อกเอาท์ใน log_activity
            $action = 'logout';
            $entity = 'user';
            $entity_id = $user_id;
            $session_minutes = round($session_duration / 60, 2);
            $additional_info = "User logged out with email: " . $user_email . " (Session duration: " . $session_minutes . " minutes)";
            
            // ตรวจสอบว่าฟังก์ชัน logAdminActivity มีอยู่หรือไม่
            if (function_exists('logAdminActivity')) {
                logAdminActivity($user_id, $action, $entity, $entity_id, $additional_info);
            }

            // เก็บข้อมูลสำคัญก่อนทำลาย session
            $user_name = ($_SESSION['user_firstname'] ?? '') . ' ' . ($_SESSION['user_lastname'] ?? '');
            
            // ล้างค่า Session ทั้งหมด
            session_destroy();

            echo json_encode([
                'success' => true,
                'message' => 'Logout successful',
                'data' => [
                    'user_name' => trim($user_name),
                    'session_duration' => $session_minutes . ' minutes'
                ]
            ]);

        } catch (Exception $e) {
            // Log error
            error_log("Logout error: " . $e->getMessage());
            
            // ถึงแม้จะเกิดข้อผิดพลาดในการอัพเดต database แต่ก็ควรทำลาย session
            session_destroy();
            
            echo json_encode([
                'success' => true,
                'message' => 'Logout successful (with minor issues)',
                'warning' => 'Some logout activities may not be recorded'
            ]);
        }

    } else {
        // ไม่มีผู้ใช้ล็อกอิน
        echo json_encode([
            'success' => false,
            'message' => 'No user is logged in',
            'error' => 'Session not found'
        ]);
    }
} else {
    // Request ไม่ถูกต้อง
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request',
        'error' => 'Logout parameter not found'
    ]);
}

// ปิดการเชื่อมต่อฐานข้อมูล
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>