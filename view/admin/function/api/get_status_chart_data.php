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

// อ่านข้อมูล JSON จาก request body
$input = json_decode(file_get_contents('php://input'), true);
$year = $input['year'] ?? date('Y');

try {
    $statusData = [
        'status_1' => 0,  // เตรียมสินค้า
        'status_2' => 0,  // ส่งไปศูนย์กระจาย
        'status_3' => 0,  // อยู่ที่ศูนย์ปลายทาง
        'status_4' => 0,  // ส่งให้ลูกค้า
        'status_5' => 0,  // ส่งสำเร็จ
        'status_99' => 0  // มีปัญหา
    ];
    
    // ดึงข้อมูลสถานะการขนส่งในปีที่เลือก
    $sql = "SELECT 
                delivery_status,
                COUNT(*) as count
            FROM tb_delivery 
            WHERE YEAR(delivery_date) = ?
            GROUP BY delivery_status";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $year);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $status = (int)$row['delivery_status'];
        $count = (int)$row['count'];
        
        switch ($status) {
            case 1:
                $statusData['status_1'] = $count;
                break;
            case 2:
                $statusData['status_2'] = $count;
                break;
            case 3:
                $statusData['status_3'] = $count;
                break;
            case 4:
                $statusData['status_4'] = $count;
                break;
            case 5:
                $statusData['status_5'] = $count;
                break;
            case 99:
                $statusData['status_99'] = $count;
                break;
        }
    }
    
    $stmt->close();
    
    // คำนวณเปอร์เซ็นต์
    $total = array_sum($statusData);
    $percentages = [];
    
    foreach ($statusData as $key => $value) {
        $percentages[$key] = $total > 0 ? round(($value / $total) * 100, 1) : 0;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $statusData,
        'percentages' => $percentages,
        'total' => $total,
        'year' => $year
    ]);

} catch (Exception $e) {
    error_log("Error in get_status_chart_data.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูลกราฟสถานะ',
        'error' => $e->getMessage()
    ]);
}
?>