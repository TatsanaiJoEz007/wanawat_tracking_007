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
$bill_type = isset($input['bill_type']) ? strtolower($input['bill_type']) : '';

if (empty($type) || empty($bill_type)) {
    echo json_encode(['error' => 'Type and bill_type parameters are required']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    $items = [];
    
    // Determine IC or IV condition
    if ($bill_type === 'ic') {
        $bill_condition = "AND h.bill_number LIKE '%ic%'";
    } else if ($bill_type === 'iv') {
        $bill_condition = "AND h.bill_number NOT LIKE '%ic%'";
    } else {
        $bill_condition = "";
    }
    
    // Handle different types
    switch ($type) {
        case 'all_bills':
        case 'pending_bills':
            // Get all bills (IC or IV) - both types show the same data for now
            $sql = "SELECT 
                        h.bill_number,
                        h.bill_date,
                        h.bill_customer_name,
                        h.bill_customer_id,
                        h.bill_weight,
                        h.bill_total,
                        COUNT(l.line_id) as item_count,
                        COALESCE(SUM(CAST(l.line_total AS DECIMAL(10,2))), 0) as total_amount
                    FROM tb_header h
                    LEFT JOIN tb_line l ON TRIM(h.bill_number) = TRIM(l.line_bill_number) AND l.line_status = '1'
                    WHERE h.bill_status = 1 
                    $bill_condition
                    GROUP BY h.bill_number, h.bill_date, h.bill_customer_name, h.bill_customer_id, h.bill_weight, h.bill_total
                    ORDER BY h.bill_date DESC
                    LIMIT 100";
            break;
            
        default:
            throw new Exception("Invalid bill type: $type");
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
        // Use bill_total from header if line_total sum is 0
        $total_amount = $row['total_amount'] > 0 ? $row['total_amount'] : (float)$row['bill_total'];
        
        $items[] = [
            'bill_number' => $row['bill_number'],
            'bill_date' => $row['bill_date'],
            'bill_customer_name' => $row['bill_customer_name'],
            'bill_customer_id' => $row['bill_customer_id'],
            'bill_weight' => $row['bill_weight'],
            'item_count' => $row['item_count'] ?: 0,
            'total_amount' => $total_amount,
            'type' => 'bill',
            'bill_type' => strtoupper($bill_type)
        ];
    }
    
    $stmt->close();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total_count' => count($items),
        'type' => $type,
        'bill_type' => strtoupper($bill_type)
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_bill_data.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'success' => false
    ]);
}

$conn->close();
?>