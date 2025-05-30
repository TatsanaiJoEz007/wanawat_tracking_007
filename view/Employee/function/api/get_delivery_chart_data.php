<?php
// api/get_delivery_chart_data.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// เริ่ม session
if (!isset($_SESSION)) {
    session_start();
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'ไม่ได้เข้าสู่ระบบ']);
    exit;
}

require_once('../../../config/connect.php');

try {
    // รับข้อมูลจาก POST request
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('ไม่พบข้อมูลที่ส่งมา');
    }
    
    $filter = $input['filter'] ?? 'month';
    $year = intval($input['year'] ?? date('Y'));
    $month = intval($input['month'] ?? date('n'));
    $day = intval($input['day'] ?? date('j'));
    
    $labels = [];
    $values = [];
    
    switch ($filter) {
        case 'day':
            // ข้อมูลรายวันในเดือนที่เลือก
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $labels[] = "{$d}";
                
                $stmt = $conn->prepare("
                    SELECT COUNT(*) as count 
                    FROM tb_delivery 
                    WHERE YEAR(delivery_date) = ? 
                    AND MONTH(delivery_date) = ? 
                    AND DAY(delivery_date) = ?
                ");
                $stmt->bind_param("iii", $year, $month, $d);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $values[] = intval($result['count']);
                $stmt->close();
            }
            break;
            
        case 'month':
            // ข้อมูลรายเดือนในปีที่เลือก
            $thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
            
            for ($m = 1; $m <= 12; $m++) {
                $labels[] = $thaiMonths[$m - 1];
                
                $stmt = $conn->prepare("
                    SELECT COUNT(*) as count 
                    FROM tb_delivery 
                    WHERE YEAR(delivery_date) = ? 
                    AND MONTH(delivery_date) = ?
                ");
                $stmt->bind_param("ii", $year, $m);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $values[] = intval($result['count']);
                $stmt->close();
            }
            break;
            
        case 'year':
            // ข้อมูลรายปี (5 ปีย้อนหลัง)
            $startYear = $year - 4;
            
            for ($y = $startYear; $y <= $year; $y++) {
                $labels[] = strval($y + 543); // แปลงเป็นปี พ.ศ.
                
                $stmt = $conn->prepare("
                    SELECT COUNT(*) as count 
                    FROM tb_delivery 
                    WHERE YEAR(delivery_date) = ?
                ");
                $stmt->bind_param("i", $y);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                $values[] = intval($result['count']);
                $stmt->close();
            }
            break;
            
        default:
            throw new Exception('ประเภทการกรองข้อมูลไม่ถูกต้อง');
    }
    
    // ส่งข้อมูลกลับ
    echo json_encode([
        'success' => true,
        'data' => [
            'labels' => $labels,
            'values' => $values
        ],
        'filter' => $filter,
        'period' => [
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'error_code' => 'CHART_DATA_ERROR'
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>