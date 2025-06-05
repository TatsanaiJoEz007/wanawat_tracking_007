<?php
// เริ่ม session ก่อนมี output ใดๆ
if (!isset($_SESSION)) {
    session_start();
}

// Security headers
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized access',
        'message' => 'กรุณาเข้าสู่ระบบ'
    ]);
    exit;
}

// Check if user has permission to manage users
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
if (!isset($permissions['manage_permission']) || $permissions['manage_permission'] != 1) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Access denied',
        'message' => 'คุณไม่มีสิทธิ์จัดการผู้ใช้งาน'
    ]);
    exit;
}

// Include database connection
require_once('../../config/connect.php');

try {
    // Get all users with status = 9 (pending approval)
    $sql = "SELECT 
                user_id,
                user_firstname, 
                user_lastname, 
                user_email, 
                user_tel,
                user_address,
                user_img,
                user_status,
                user_type,
                customer_id,
                province_id,
                amphure_id,
                district_id,
                user_create_at
            FROM tb_user 
            WHERE user_status = 9
            ORDER BY user_create_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        // แปลง image เป็น base64 ถ้ามี
        if ($row['user_img']) {
            $row['user_img'] = base64_encode($row['user_img']);
        }
        $users[] = $row;
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true, 
        'users' => $users,
        'count' => count($users)
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_pending_users.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage()
    ]);
}
?>