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
    // ดึงข้อมูลกิจกรรมล่าสุด JOIN กับ tb_user
    $sql = "SELECT 
                aal.id,
                aal.userId,
                aal.action,
                aal.entity,
                aal.entity_id,
                aal.create_at,
                aal.additional_info,
                u.user_firstname,
                u.user_lastname,
                u.user_email,
                u.user_type
            FROM admin_activity_log aal
            LEFT JOIN tb_user u ON aal.userId = u.user_id
            ORDER BY aal.create_at DESC
            LIMIT 15";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $activities = [];
    while ($row = $result->fetch_assoc()) {
        $activities[] = [
            'id' => $row['id'],
            'userId' => $row['userId'],
            'action' => $row['action'],
            'entity' => $row['entity'],
            'entity_id' => $row['entity_id'],
            'create_at' => $row['create_at'],
            'additional_info' => $row['additional_info'],
            'user_firstname' => $row['user_firstname'] ?? 'ไม่ระบุ',
            'user_lastname' => $row['user_lastname'] ?? 'ผู้ใช้',
            'user_email' => $row['user_email'] ?? '',
            'user_type' => $row['user_type'] ?? 0
        ];
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'activities' => $activities,
        'total_activities' => count($activities)
    ]);

} catch (Exception $e) {
    error_log("Error in get_recent_activity.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูลกิจกรรม',
        'error' => $e->getMessage()
    ]);
}
?>