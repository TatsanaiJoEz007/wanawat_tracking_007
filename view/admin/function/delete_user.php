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
    
    // Validation
    if (empty($user_id)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสผู้ใช้']);
        exit;
    }
    
    // ตรวจสอบว่าไม่ใช่การลบตัวเอง
    if ($user_id == $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบบัญชีของตัวเองได้']);
        exit;
    }
    
    // ตรวจสอบว่าผู้ใช้มีอยู่จริง
    $check_user = $conn->prepare("SELECT user_firstname, user_lastname, user_email, user_type FROM tb_user WHERE user_id = ? AND user_status = 1");
    $check_user->bind_param("i", $user_id);
    $check_user->execute();
    $user_result = $check_user->get_result();
    
    if ($user_result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบผู้ใช้ที่ระบุ']);
        exit;
    }
    
    $user_info = $user_result->fetch_assoc();
    $check_user->close();
    
    // ป้องกันการลบ Super Admin (หากมี)
    if ($user_info['user_type'] == 999) {
        // นับจำนวน Admin ที่เหลือ
        $admin_count = $conn->prepare("SELECT COUNT(*) as count FROM tb_user WHERE user_type = 999 AND user_status = 1 AND user_id != ?");
        $admin_count->bind_param("i", $user_id);
        $admin_count->execute();
        $count_result = $admin_count->get_result()->fetch_assoc();
        $admin_count->close();
        
        if ($count_result['count'] == 0) {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบผู้ดูแลระบบคนสุดท้ายได้']);
            exit;
        }
    }
    
    // เริ่ม transaction
    $conn->begin_transaction();
    
    try {
        // อัปเดตสถานะเป็น inactive แทนการลบจริง (Soft Delete)
        $delete_sql = "UPDATE tb_user SET user_status = 0, customer_id = NULL WHERE user_id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $user_id);
        
        if (!$delete_stmt->execute()) {
            throw new Exception("ไม่สามารถลบผู้ใช้ได้");
        }
        $delete_stmt->close();
        
        // Log activity
        $activity_sql = "INSERT INTO admin_activity_log (userId, action, entity, entity_id, additional_info, create_at) VALUES (?, 'DELETE_USER', 'tb_user', ?, ?, NOW())";
        $activity_stmt = $conn->prepare($activity_sql);
        $additional_info = "ลบผู้ใช้: {$user_info['user_firstname']} {$user_info['user_lastname']} ({$user_info['user_email']})";
        $activity_stmt->bind_param("iis", $_SESSION['user_id'], $user_id, $additional_info);
        $activity_stmt->execute();
        $activity_stmt->close();
        
        // Commit transaction
        $conn->commit();
        
        echo json_encode(['success' => true, 'message' => 'ลบผู้ใช้เรียบร้อยแล้ว']);
        
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Error in delete_user.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage()]);
}
?>