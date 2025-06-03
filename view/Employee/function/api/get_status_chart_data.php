<?php
session_start();
require_once('../../../config/connect.php');

// Set header for JSON response
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON input']);
    exit;
}

// Get parameters
$year = isset($input['year']) ? intval($input['year']) : date('Y');

try {
    $status_data = [
        'status_1' => 0,  // เตรียมสินค้า
        'status_2' => 0,  // ส่งไปศูนย์กระจาย
        'status_3' => 0,  // อยู่ที่ศูนย์ปลายทาง
        'status_4' => 0,  // ส่งให้ลูกค้า
        'status_5' => 0,  // ส่งสำเร็จ
        'status_99' => 0  // มีปัญหา
    ];
    
    // Get status counts for the selected year
    $sql = "SELECT 
                delivery_status,
                COUNT(*) as count
            FROM tb_delivery 
            WHERE YEAR(delivery_date) = ?
            GROUP BY delivery_status";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $year);
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $status = intval($row['delivery_status']);
        $count = intval($row['count']);
        
        switch ($status) {
            case 1:
                $status_data['status_1'] = $count;
                break;
            case 2:
                $status_data['status_2'] = $count;
                break;
            case 3:
                $status_data['status_3'] = $count;
                break;
            case 4:
                $status_data['status_4'] = $count;
                break;
            case 5:
                $status_data['status_5'] = $count;
                break;
            case 99:
                $status_data['status_99'] = $count;
                break;
        }
    }
    
    $stmt->close();
    
    // Calculate total for percentage
    $total = array_sum($status_data);
    
    // Calculate percentages
    $percentages = [];
    foreach ($status_data as $key => $value) {
        $percentages[$key] = $total > 0 ? round(($value / $total) * 100, 1) : 0;
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'data' => $status_data,
        'percentages' => $percentages,
        'total' => $total,
        'year' => $year,
        'labels' => [
            'เตรียมสินค้า (' . $status_data['status_1'] . ')',
            'ส่งไปศูนย์กระจาย (' . $status_data['status_2'] . ')',
            'อยู่ที่ศูนย์ปลายทาง (' . $status_data['status_3'] . ')',
            'ส่งให้ลูกค้า (' . $status_data['status_4'] . ')',
            'ส่งสำเร็จ (' . $status_data['status_5'] . ')',
            'มีปัญหา (' . $status_data['status_99'] . ')'
        ],
        'values' => [
            $status_data['status_1'],
            $status_data['status_2'],
            $status_data['status_3'],
            $status_data['status_4'],
            $status_data['status_5'],
            $status_data['status_99']
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_status_chart_data.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'success' => false
    ]);
}

$conn->close();
?>