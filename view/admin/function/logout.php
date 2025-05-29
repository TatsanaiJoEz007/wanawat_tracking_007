<?php
session_start();

// ส่วนหัว JSON
header('Content-Type: application/json');

// ตรวจสอบ HTTP method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    exit;
}

try {
    // อ่านข้อมูล JSON จาก request
    $input = json_decode(file_get_contents('php://input'), true);
    
    // ตรวจสอบ CSRF token (ถ้าใช้)
    if (isset($_SESSION['csrf_token']) && isset($input['csrf_token'])) {
        if (!hash_equals($_SESSION['csrf_token'], $input['csrf_token'])) {
            throw new Exception('Invalid CSRF token');
        }
    }
    
    // บันทึก log การออกจากระบบ (ถ้าจำเป็น)
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'] ?? 'Unknown';
        
        // สามารถบันทึก log ลงฐานข้อมูลได้ที่นี่
        error_log("User logout: ID={$user_id}, Username={$username}, IP=" . $_SERVER['REMOTE_ADDR'] . ", Time=" . date('Y-m-d H:i:s'));
    }
    
    // ทำลาย session
    session_unset();
    session_destroy();
    
    // ลบ session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // ส่งการตอบกลับที่สำเร็จ
    echo json_encode([
        'success' => true,
        'message' => 'ออกจากระบบสำเร็จ'
    ]);
    
} catch (Exception $e) {
    // จัดการข้อผิดพลาด
    error_log("Logout error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>