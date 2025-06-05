<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

if (!isset($_SESSION)) {
    session_start();
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

require_once('../../../config/connect.php');

try {
    // ดึงข้อมูลผู้ใช้ที่ออนไลน์
    $sql = "SELECT 
                user_id,
                user_firstname,
                user_lastname,
                user_email,
                user_type,
                user_last_login,
                user_last_activity,
                user_last_ip,
                user_login_count
            FROM tb_user 
            WHERE user_is_online = 1 
            AND user_status = 1 
            ORDER BY user_last_activity DESC, user_last_login DESC
            LIMIT 10";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'user_id' => $row['user_id'],
            'user_firstname' => $row['user_firstname'],
            'user_lastname' => $row['user_lastname'],
            'user_email' => $row['user_email'],
            'user_type' => $row['user_type'],
            'user_last_login' => $row['user_last_login'],
            'user_last_activity' => $row['user_last_activity'],
            'user_last_ip' => $row['user_last_ip'],
            'user_login_count' => $row['user_login_count']
        ];
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'users' => $users,
        'total_online' => count($users)
    ]);

} catch (Exception $e) {
    error_log("Error in get_online_users.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูลผู้ใช้ออนไลน์',
        'error' => $e->getMessage()
    ]);
}
?>