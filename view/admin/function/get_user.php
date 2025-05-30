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

try {
    $user_type = isset($_GET['user_type']) ? intval($_GET['user_type']) : null;
    
    if ($user_type === null) {
        echo json_encode(['success' => false, 'message' => 'User type is required']);
        exit;
    }
    
    // สร้าง SQL query - เอา create_at ออก
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
                district_id
            FROM tb_user 
            WHERE user_type = ? AND user_status = 1
            ORDER BY user_firstname ASC, user_lastname ASC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_type);
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
        'data' => $users,
        'count' => count($users)
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_users.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูล: ' . $e->getMessage()
    ]);
}
?>