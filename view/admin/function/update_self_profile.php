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

// Include database connection
require_once('../../config/connect.php');

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed',
        'message' => 'เฉพาะ POST method เท่านั้น'
    ]);
    exit;
}

try {
    // Get current user ID from session
    $user_id = $_SESSION['user_id'];
    
    // Get form data
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $province_id = !empty($_POST['province_id']) ? (int)$_POST['province_id'] : null;
    $amphure_id = !empty($_POST['amphure_id']) ? (int)$_POST['amphure_id'] : null;
    $district_id = !empty($_POST['district_id']) ? $_POST['district_id'] : null;

    // Validate required fields
    if (empty($new_password) || empty($confirm_password) || empty($address)) {
        throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน (รหัสผ่านใหม่ และที่อยู่)');
    }

    // Validate password
    if ($new_password !== $confirm_password) {
        throw new Exception('รหัสผ่านไม่ตรงกัน');
    }

    if (strlen($new_password) < 6) {
        throw new Exception('รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร');
    }

    // Check if user exists and has status = 9
    $check_user = $conn->prepare("SELECT user_email, user_status FROM tb_user WHERE user_id = ?");
    $check_user->bind_param("i", $user_id);
    $check_user->execute();
    $existing_user = $check_user->get_result()->fetch_assoc();
    $check_user->close();
    
    if (!$existing_user) {
        throw new Exception('ไม่พบข้อมูลผู้ใช้งาน');
    }

    if ($existing_user['user_status'] != 9) {
        throw new Exception('ข้อมูลผู้ใช้งานไม่อยู่ในสถานะที่ต้องการการยืนยัน');
    }

    // Hash new password
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Handle default values for location
    if (!$province_id) $province_id = 1; // Default province
    if (!$amphure_id) $amphure_id = 1; // Default amphure
    if (!$district_id) $district_id = '100101'; // Default district

    // Update user data and activate account
    $sql = "UPDATE tb_user SET 
                user_pass = ?, 
                user_address = ?, 
                province_id = ?, 
                amphure_id = ?, 
                district_id = ?, 
                user_status = 1,
                user_last_activity = NOW()
            WHERE user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiisi", 
        $hashed_password, 
        $address, 
        $province_id, 
        $amphure_id, 
        $district_id, 
        $user_id
    );
    
    if ($stmt->execute()) {
        $stmt->close();
        
        // Update session data
        $_SESSION['user_status'] = 1;
        
        // Log activity
        logActivity($conn, $user_id, 'ACTIVATE_ACCOUNT', 'tb_user', $user_id, 'ผู้ใช้งานยืนยันข้อมูลและเปิดใช้งานบัญชี');
        
        echo json_encode([
            'success' => true,
            'message' => 'อัปเดตข้อมูลเรียบร้อยแล้ว บัญชีของคุณได้รับการเปิดใช้งานแล้ว',
            'user_id' => $user_id,
            'activated' => true
        ]);
    } else {
        throw new Exception('ไม่สามารถอัปเดตข้อมูลได้: ' . $stmt->error);
    }

} catch (Exception $e) {
    error_log("Update Self Profile Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Update failed',
        'message' => $e->getMessage()
    ]);
    
} finally {
    // Close database connection
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}

// Helper function to log activity
function logActivity($conn, $user_id, $action, $entity, $entity_id, $details) {
    try {
        // Check if log table exists
        $check_table = $conn->query("SHOW TABLES LIKE 'admin_activity_log'");
        if ($check_table->num_rows > 0) {
            $log_sql = "INSERT INTO admin_activity_log (userId, action, entity, entity_id, additional_info, create_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("issis", $user_id, $action, $entity, $entity_id, $details);
            $log_stmt->execute();
            $log_stmt->close();
        }
    } catch (Exception $e) {
        // Log error but don't stop execution
        error_log("Activity log error: " . $e->getMessage());
    }
}
?>