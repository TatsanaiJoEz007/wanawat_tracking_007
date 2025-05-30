<?php
// เริ่ม session ก่อนมี output ใดๆ
if (!isset($_SESSION)) {
    session_start();
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once('../../config/connect.php');

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];

// ตรวจสอบสิทธิ์ในการเข้าถึง
if (!isset($permissions['manage_permission']) || $permissions['manage_permission'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// ตั้งค่า header สำหรับ JSON response
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $user_id = intval($_POST['user_id'] ?? 0);
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    // Validation
    if (empty($user_id)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสผู้ใช้']);
        exit;
    }
    
    if (empty($new_password)) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกรหัสผ่านใหม่']);
        exit;
    }
    
    if ($new_password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน']);
        exit;
    }
    
    if (strlen($new_password) < 6) {
        echo json_encode(['success' => false, 'message' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 6 ตัวอักษร']);
        exit;
    }
    
    // ตรวจสอบว่าผู้ใช้มีอยู่จริง
    $check_user = $conn->prepare("SELECT user_firstname, user_lastname, user_email FROM tb_user WHERE user_id = ? AND user_status = 1");
    $check_user->bind_param("i", $user_id);
    $check_user->execute();
    $user_result = $check_user->get_result();
    
    if ($user_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบผู้ใช้ที่ระบุ']);
        exit;
    }
    
    $user_info = $user_result->fetch_assoc();
    $check_user->close();
    
    // เข้ารหัสรหัสผ่านใหม่
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    
    // อัปเดตรหัสผ่าน
    $update_sql = "UPDATE tb_user SET user_password = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $hashed_password, $user_id);
    
    if ($update_stmt->execute()) {
        // Log activity
        $activity_sql = "INSERT INTO admin_activity_log (userId, action, entity, entity_id, additional_info, create_at) VALUES (?, 'RESET_PASSWORD', 'tb_user', ?, ?, NOW())";
        $activity_stmt = $conn->prepare($activity_sql);
        $additional_info = "รีเซ็ตรหัสผ่านผู้ใช้: {$user_info['user_firstname']} {$user_info['user_lastname']} ({$user_info['user_email']})";
        $activity_stmt->bind_param("iis", $_SESSION['user_id'], $user_id, $additional_info);
        $activity_stmt->execute();
        $activity_stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'รีเซ็ตรหัสผ่านเรียบร้อยแล้ว']);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน']);
    }
    
    $update_stmt->close();
    
} catch (Exception $e) {
    error_log("Error in reset_password.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage()]);
}
?>