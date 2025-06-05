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
$type = isset($input['type']) ? $input['type'] : '';
$status = isset($input['status']) ? $input['status'] : '';

if (empty($type)) {
    echo json_encode(['error' => 'Type parameter is required']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $items = [];
    
    // Handle different types
    switch ($type) {
        case 'all_bills':
            // Get all bills
            $sql = "SELECT 
                        h.bill_number as delivery_number,
                        h.bill_date as delivery_date,
                        h.bill_customer_name,
                        h.bill_customer_id,
                        h.bill_weight,
                        COUNT(l.line_id) as item_count,
                        SUM(l.line_total) as total_amount,
                        'bill' as type,
                        NULL as delivery_status,
                        NULL as delivery_step1_received,
                        NULL as delivery_step2_transit,
                        NULL as delivery_step3_warehouse,
                        NULL as delivery_step4_last_mile,
                        NULL as delivery_step5_completed,
                        'ทั่วไป' as transfer_type
                    FROM tb_header h
                    LEFT JOIN tb_line l ON TRIM(h.bill_number) = TRIM(l.line_bill_number) AND l.line_status = '1'
                    WHERE h.bill_status = 1
                    GROUP BY h.bill_number, h.bill_date, h.bill_customer_name, h.bill_customer_id, h.bill_weight
                    ORDER BY h.bill_date DESC
                    LIMIT 100";
            break;
        
        case 'pending_bills':
            // Get bills that are not yet included in delivery
            $sql = "SELECT 
                        h.bill_number as delivery_number,
                        h.bill_date as delivery_date,
                        h.bill_customer_name,
                        h.bill_customer_id,
                        h.bill_weight,
                        COUNT(l.line_id) as item_count,
                        SUM(l.line_total) as total_amount,
                        'bill' as type,
                        NULL as delivery_status,
                        NULL as delivery_step1_received,
                        NULL as delivery_step2_transit,
                        NULL as delivery_step3_warehouse,
                        NULL as delivery_step4_last_mile,
                        NULL as delivery_step5_completed,
                        'ทั่วไป' as transfer_type
                    FROM tb_header h
                    LEFT JOIN tb_line l ON TRIM(h.bill_number) = TRIM(l.line_bill_number) AND l.line_status = '1'
                    WHERE h.bill_status = 1 
                    AND h.bill_number NOT IN (
                        SELECT DISTINCT di.bill_number 
                        FROM tb_delivery_items di 
                        WHERE di.bill_number IS NOT NULL
                    )
                    GROUP BY h.bill_number, h.bill_date, h.bill_customer_name, h.bill_customer_id, h.bill_weight
                    ORDER BY h.bill_date DESC
                    LIMIT 100";
            break;
            
        case 'all_delivery':
            // Get all deliveries
            $sql = "SELECT 
                        d.delivery_id,
                        d.delivery_number,
                        d.delivery_date,
                        COUNT(di.item_code) AS item_count,
                        d.delivery_status,
                        d.delivery_step1_received,
                        d.delivery_step2_transit,
                        d.delivery_step3_warehouse,
                        d.delivery_step4_last_mile,
                        d.delivery_step5_completed,
                        GROUP_CONCAT(DISTINCT di.transfer_type SEPARATOR ', ') as transfer_type
                    FROM tb_delivery d
                    LEFT JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id
                    GROUP BY d.delivery_id, d.delivery_number, d.delivery_date, d.delivery_status,
                             d.delivery_step1_received, d.delivery_step2_transit, d.delivery_step3_warehouse,
                             d.delivery_step4_last_mile, d.delivery_step5_completed
                    ORDER BY d.delivery_date DESC
                    LIMIT 50";
            break;
            
        default:
            // Get deliveries by status
            $statusCondition = '';
            if ($status !== 'all' && is_numeric($status)) {
                $statusCondition = "WHERE d.delivery_status = " . intval($status);
            }
            
            $sql = "SELECT 
                        d.delivery_id,
                        d.delivery_number,
                        d.delivery_date,
                        COUNT(di.item_code) AS item_count,
                        d.delivery_status,
                        d.delivery_step1_received,
                        d.delivery_step2_transit,
                        d.delivery_step3_warehouse,
                        d.delivery_step4_last_mile,
                        d.delivery_step5_completed,
                        GROUP_CONCAT(DISTINCT di.transfer_type SEPARATOR ', ') as transfer_type
                    FROM tb_delivery d
                    LEFT JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id
                    $statusCondition
                    GROUP BY d.delivery_id, d.delivery_number, d.delivery_date, d.delivery_status,
                             d.delivery_step1_received, d.delivery_step2_transit, d.delivery_step3_warehouse,
                             d.delivery_step4_last_mile, d.delivery_step5_completed
                    ORDER BY d.delivery_date DESC
                    LIMIT 100";
            break;
    }
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    // Execute query
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        // For bills, we need different handling
        if ($type === 'all_bills' || $type === 'pending_bills') {
            $items[] = [
                'delivery_id' => null, // Bills don't have delivery_id
                'delivery_number' => $row['delivery_number'],
                'delivery_date' => $row['delivery_date'],
                'bill_customer_name' => $row['bill_customer_name'],
                'bill_customer_id' => $row['bill_customer_id'],
                'bill_weight' => $row['bill_weight'],
                'item_count' => $row['item_count'] ?: 0,
                'total_amount' => $row['total_amount'] ?: 0,
                'delivery_status' => 'bill', // Special status for bills
                'delivery_step1_received' => null,
                'delivery_step2_transit' => null,
                'delivery_step3_warehouse' => null,
                'delivery_step4_last_mile' => null,
                'delivery_step5_completed' => null,
                'transfer_type' => $row['transfer_type'] ?: 'ทั่วไป',
                'type' => 'bill'
            ];
        } else {
            $items[] = [
                'delivery_id' => $row['delivery_id'],
                'delivery_number' => $row['delivery_number'],
                'delivery_date' => $row['delivery_date'],
                'item_count' => $row['item_count'] ?: 0,
                'delivery_status' => $row['delivery_status'],
                'delivery_step1_received' => $row['delivery_step1_received'],
                'delivery_step2_transit' => $row['delivery_step2_transit'],
                'delivery_step3_warehouse' => $row['delivery_step3_warehouse'],
                'delivery_step4_last_mile' => $row['delivery_step4_last_mile'],
                'delivery_step5_completed' => $row['delivery_step5_completed'],
                'transfer_type' => $row['transfer_type'] ?: 'ทั่วไป',
                'type' => 'delivery'
            ];
        }
    }
    
    $stmt->close();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total_count' => count($items),
        'type' => $type,
        'status' => $status
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_stat_data.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'success' => false
    ]);
}

$conn->close();
?>