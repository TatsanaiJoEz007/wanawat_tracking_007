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

$filter = $input['filter'] ?? 'month';
$year = $input['year'] ?? date('Y');
$month = $input['month'] ?? date('m');
$day = $input['day'] ?? date('d');

try {
    $labels = [];
    $values = [];
    
    switch ($filter) {
        case 'day':
            // ข้อมูลรายวันในเดือนที่เลือก
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = $d;
                
                $sql = "SELECT COUNT(*) as count 
                        FROM tb_delivery 
                        WHERE YEAR(delivery_date) = ? 
                        AND MONTH(delivery_date) = ? 
                        AND DAY(delivery_date) = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("iii", $year, $month, $d);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = $result->fetch_assoc()['count'];
                $values[] = (int)$count;
                $stmt->close();
            }
            break;
            
        case 'month':
            // ข้อมูลรายเดือนในปีที่เลือก
            $thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $thaiMonths[$m-1];
                
                $sql = "SELECT COUNT(*) as count 
                        FROM tb_delivery 
                        WHERE YEAR(delivery_date) = ? 
                        AND MONTH(delivery_date) = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $year, $m);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = $result->fetch_assoc()['count'];
                $values[] = (int)$count;
                $stmt->close();
            }
            break;
            
        case 'year':
            // ข้อมูลรายปีย้อนหลัง 5 ปี
            $currentYear = date('Y');
            
            for ($y = $currentYear - 4; $y <= $currentYear; $y++) {
                $labels[] = $y;
                
                $sql = "SELECT COUNT(*) as count 
                        FROM tb_delivery 
                        WHERE YEAR(delivery_date) = ?";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $y);
                $stmt->execute();
                $result = $stmt->get_result();
                $count = $result->fetch_assoc()['count'];
                $values[] = (int)$count;
                $stmt->close();
            }
            break;
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'labels' => $labels,
            'values' => $values
        ],
        'filter' => $filter,
        'year' => $year,
        'month' => $month,
        'day' => $day
    ]);

} catch (Exception $e) {
    error_log("Error in get_delivery_chart_data.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'เกิดข้อผิดพลาดในการดึงข้อมูลกราฟการขนส่ง',
        'error' => $e->getMessage()
    ]);
}
?>