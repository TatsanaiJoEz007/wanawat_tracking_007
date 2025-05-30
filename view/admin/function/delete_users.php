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
    $user_ids = $_POST['user_ids'] ?? [];
    
    // Validation
    if (empty($user_ids) || !is_array($user_ids)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรายการผู้ใช้ที่ต้องการลบ']);
        exit;
    }
    
    // แปลงเป็น integer และกรองค่าที่ไม่ถูกต้อง
    $user_ids = array_map('intval', $user_ids);
    $user_ids = array_filter($user_ids, function($id) {
        return $id > 0;
    });
    
    if (empty($user_ids)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบรายการผู้ใช้ที่ถูกต้อง']);
        exit;
    }
    
    // ตรวจสอบว่าไม่มีการลบตัวเอง
    if (in_array($_SESSION['user_id'], $user_ids)) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบบัญชีของตัวเองได้']);
        exit;
    }
    
    // สร้าง placeholder สำหรับ IN clause
    $placeholders = str_repeat('?,', count($user_ids) - 1) . '?';
    
    // ตรวจสอบผู้ใช้ที่มีอยู่จริง
    $check_sql = "SELECT user_id, user_firstname, user_lastname, user_email, user_type FROM tb_user WHERE user_id IN ($placeholders) AND user_status = 1";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param(str_repeat('i', count($user_ids)), ...$user_ids);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    $existing_users = [];
    $admin_count_to_delete = 0;
    
    while ($user = $check_result->fetch_assoc()) {
        $existing_users[] = $user;
        if ($user['user_type'] == 999) {
            $admin_count_to_delete++;
        }
    }
    $check_stmt->close();
    
    if (empty($existing_users)) {
        echo json_encode(['success' => false, 'message' => 'ไม่พบผู้ใช้ที่ระบุ']);
        exit;
    }
    
    // ตรวจสอบไม่ให้ลบ Admin หมด
    if ($admin_count_to_delete > 0) {
        $total_admin = $conn->prepare("SELECT COUNT(*) as count FROM tb_user WHERE user_type = 999 AND user_status = 1");
        $total_admin->execute();
        $admin_result = $total_admin->get_result()->fetch_assoc();
        $total_admin->close();
        
        if ($admin_result['count'] <= $admin_count_to_delete) {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบผู้ดูแลระบบทั้งหมดได้ ต้องเหลืออย่างน้อย 1 คน']);
            exit;
        }
    }
    
    // เริ่ม transaction
    $conn->begin_transaction();
    
    try {
        $deleted_users = [];
        
        // ลบผู้ใช้ทีละคน (Soft Delete)
        foreach ($existing_users as $user) {
            $delete_sql = "UPDATE tb_user SET user_status = 0, customer_id = NULL WHERE user_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $user['user_id']);
            
            if ($delete_stmt->execute()) {
                $deleted_users[] = $user;
                
                // Log activity
                $activity_sql = "INSERT INTO admin_activity_log (userId, action, entity, entity_id, additional_info, create_at) VALUES (?, 'DELETE_USER_BULK', 'tb_user', ?, ?, NOW())";
                $activity_stmt = $conn->prepare($activity_sql);
                $additional_info = "ลบผู้ใช้ (รายการ): {$user['user_firstname']} {$user['user_lastname']} ({$user['user_email']})";
                $activity_stmt->bind_param("iis", $_SESSION['user_id'], $user['user_id'], $additional_info);
                $activity_stmt->execute();
                $activity_stmt->close();
            }
            $delete_stmt->close();
        }
        
        if (empty($deleted_users)) {
            throw new Exception("ไม่สามารถลบผู้ใช้ได้");
        }
        
        // Commit transaction
        $conn->commit();
        
        $message = 'ลบผู้ใช้ ' . count($deleted_users) . ' คน เรียบร้อยแล้ว';
        echo json_encode(['success' => true, 'message' => $message, 'deleted_count' => count($deleted_users)]);
        
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        throw $e;
    }
    
} catch (Exception $e) {
    error_log("Error in delete_users.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage()]);
}
?>