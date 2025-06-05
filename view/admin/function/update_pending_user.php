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
    // Get form data
    $user_id = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null;
    $new_password = trim($_POST['new_password'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $province_id = !empty($_POST['province_id']) ? (int)$_POST['province_id'] : null;
    $amphure_id = !empty($_POST['amphure_id']) ? (int)$_POST['amphure_id'] : null;
    $district_id = !empty($_POST['district_id']) ? $_POST['district_id'] : null;
    $approve = isset($_POST['approve']) ? (int)$_POST['approve'] : 0;

    // Validate required fields
    if (!$user_id) {
        throw new Exception('ไม่พบรหัสผู้ใช้งาน');
    }

    // Check if user exists and has status = 9
    $check_user = $conn->prepare("SELECT user_email, user_type FROM tb_user WHERE user_id = ? AND user_status = 9");
    $check_user->bind_param("i", $user_id);
    $check_user->execute();
    $existing_user = $check_user->get_result()->fetch_assoc();
    $check_user->close();
    
    if (!$existing_user) {
        throw new Exception('ไม่พบผู้ใช้งานที่รอการยืนยัน');
    }

    // Prepare update data
    $update_fields = [];
    $params = [];
    $param_types = '';

    // Update password if provided
    if (!empty($new_password)) {
        if (strlen($new_password) < 6) {
            throw new Exception('รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร');
        }
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_fields[] = "user_pass = ?";
        $params[] = $hashed_password;
        $param_types .= "s";
    }

    // Update address if provided
    if (!empty($address)) {
        $update_fields[] = "user_address = ?";
        $params[] = $address;
        $param_types .= "s";
    }

    // Update province if provided
    if ($province_id) {
        $update_fields[] = "province_id = ?";
        $params[] = $province_id;
        $param_types .= "i";
    }

    // Update amphure if provided
    if ($amphure_id) {
        $update_fields[] = "amphure_id = ?";
        $params[] = $amphure_id;
        $param_types .= "i";
    }

    // Update district if provided
    if ($district_id) {
        $update_fields[] = "district_id = ?";
        $params[] = $district_id;
        $param_types .= "s";
    }

    // Update status if approving
    if ($approve == 1) {
        $update_fields[] = "user_status = ?";
        $params[] = 1; // Approve user
        $param_types .= "i";
    }

    // Update last activity
    $update_fields[] = "user_last_activity = NOW()";

    // Execute update if there are fields to update
    if (!empty($update_fields)) {
        $sql = "UPDATE tb_user SET " . implode(", ", $update_fields) . " WHERE user_id = ?";
        $params[] = $user_id;
        $param_types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($param_types, ...$params);
        
        if ($stmt->execute()) {
            $stmt->close();
            
            // Log activity
            $action_desc = $approve == 1 ? 'อนุมัติผู้ใช้งาน' : 'อัปเดตข้อมูลผู้ใช้งานที่รอการยืนยัน';
            logActivity($conn, $_SESSION['user_id'], 'UPDATE_PENDING_USER', 'tb_user', $user_id, $action_desc);
            
            $message = $approve == 1 ? 'อนุมัติผู้ใช้งานเรียบร้อยแล้ว' : 'อัปเดตข้อมูลผู้ใช้งานเรียบร้อยแล้ว';
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'user_id' => $user_id,
                'approved' => $approve == 1
            ]);
        } else {
            throw new Exception('ไม่สามารถอัปเดตข้อมูลผู้ใช้งานได้: ' . $stmt->error);
        }
    } else {
        throw new Exception('ไม่มีข้อมูลที่ต้องอัปเดต');
    }

} catch (Exception $e) {
    error_log("Update Pending User Error: " . $e->getMessage());
    
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