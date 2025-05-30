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
    $customer_id = trim($_POST['customer_id'] ?? '');
    
    // Validation
    if (empty($user_id)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสผู้ใช้']);
        exit;
    }
    
    if (empty($customer_id)) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกรหัสลูกค้า']);
        exit;
    }
    
    // ตรวจสอบรูปแบบรหัสลูกค้า
    if (!preg_match('/^[A-Za-z0-9]{1,20}$/', $customer_id)) {
        echo json_encode(['success' => false, 'message' => 'รหัสลูกค้าต้องเป็นตัวอักษรและตัวเลข ไม่เกิน 20 ตัวอักษร']);
        exit;
    }
    
    // ตรวจสอบว่าผู้ใช้มีอยู่จริงและเป็นลูกค้า (user_type = 0)
    $check_user = $conn->prepare("SELECT user_firstname, user_lastname, user_email, user_type FROM tb_user WHERE user_id = ? AND user_status = 1");
    $check_user->bind_param("i", $user_id);
    $check_user->execute();
    $user_result = $check_user->get_result();
    
    if ($user_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบผู้ใช้ที่ระบุ']);
        exit;
    }
    
    $user_info = $user_result->fetch_assoc();
    
    if ($user_info['user_type'] != 0) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถกำหนดรหัสลูกค้าให้กับประเภทผู้ใช้นี้ได้']);
        exit;
    }
    
    $check_user->close();
    
    // ตรวจสอบรหัสลูกค้าซ้ำ (ยกเว้นตัวเอง)
    $check_customer_id = $conn->prepare("SELECT user_id FROM tb_user WHERE customer_id = ? AND user_id != ?");
    $check_customer_id->bind_param("si", $customer_id, $user_id);
    $check_customer_id->execute();
    
    if ($check_customer_id->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'รหัสลูกค้านี้มีอยู่ในระบบแล้ว']);
        exit;
    }
    $check_customer_id->close();
    
    // อัปเดตรหัสลูกค้า
    $update_sql = "UPDATE tb_user SET customer_id = ? WHERE user_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $customer_id, $user_id);
    
    if ($update_stmt->execute()) {
        // Log activity
        $activity_sql = "INSERT INTO admin_activity_log (userId, action, entity, entity_id, additional_info, create_at) VALUES (?, 'UPDATE_CUSTOMER_ID', 'tb_user', ?, ?, NOW())";
        $activity_stmt = $conn->prepare($activity_sql);
        $additional_info = "อัปเดตรหัสลูกค้า: {$user_info['user_firstname']} {$user_info['user_lastname']} -> {$customer_id}";
        $activity_stmt->bind_param("iis", $_SESSION['user_id'], $user_id, $additional_info);
        $activity_stmt->execute();
        $activity_stmt->close();
        
        echo json_encode(['success' => true, 'message' => 'อัปเดตรหัสลูกค้าเรียบร้อยแล้ว']);
    } else {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปเดตรหัสลูกค้า']);
    }
    
    $update_stmt->close();
    
} catch (Exception $e) {
    error_log("Error in update_customer_id.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage()]);
}
?>