<?php
session_start();
require_once('../../config/connect.php');

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

// Get delivery IDs from POST data
$deliveryIds = isset($_POST['deliveryIds']) ? $_POST['deliveryIds'] : '';

if (empty($deliveryIds)) {
    echo json_encode(['error' => 'No delivery IDs provided']);
    exit;
}

// Convert comma-separated string to array and validate
$idArray = explode(',', $deliveryIds);
$idArray = array_map('intval', $idArray);
$idArray = array_filter($idArray, function($id) {
    return $id > 0;
});

if (empty($idArray)) {
    echo json_encode(['error' => 'Invalid delivery IDs']);
    exit;
}

try {
    // Create placeholders for prepared statement
    $placeholders = str_repeat('?,', count($idArray) - 1) . '?';
    
    // Query to get delivery information with items details
    $sql = "SELECT 
                d.delivery_id,
                d.delivery_number,
                d.delivery_status,
                d.delivery_date,
                d.delivery_step1_received,
                d.delivery_step2_transit,
                d.delivery_step3_warehouse,
                d.delivery_step4_last_mile,
                d.delivery_step5_completed,
                d.delivery_problem_desc,
                di.delivery_item_id,
                di.bill_number,
                di.bill_customer_name,
                di.bill_customer_id,
                di.item_code,
                di.item_desc,
                di.item_sequence,
                di.item_quantity,
                di.item_unit,
                di.item_price,
                di.line_total,
                di.item_weight,
                di.transfer_type
            FROM tb_delivery d
            LEFT JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id
            WHERE d.delivery_id IN ($placeholders)
            ORDER BY d.delivery_date DESC, di.delivery_item_id ASC";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    // Bind parameters
    $types = str_repeat('i', count($idArray));
    $stmt->bind_param($types, ...$idArray);
    
    // Execute query
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $deliveries = [];
    
    while ($row = $result->fetch_assoc()) {
        $delivery_id = $row['delivery_id'];
        
        // If this delivery doesn't exist in our array yet, create it
        if (!isset($deliveries[$delivery_id])) {
            $deliveries[$delivery_id] = [
                'delivery_id' => $delivery_id,
                'delivery_number' => $row['delivery_number'],
                'delivery_status' => $row['delivery_status'],
                'delivery_date' => $row['delivery_date'],
                'delivery_step1_received' => $row['delivery_step1_received'],
                'delivery_step2_transit' => $row['delivery_step2_transit'],
                'delivery_step3_warehouse' => $row['delivery_step3_warehouse'],
                'delivery_step4_last_mile' => $row['delivery_step4_last_mile'],
                'delivery_step5_completed' => $row['delivery_step5_completed'],
                'delivery_problem_desc' => $row['delivery_problem_desc'],
                'items' => [],
                'item_count' => 0,
                'transfer_types' => []
            ];
        }
        
        // Add item details if they exist
        if ($row['delivery_item_id']) {
            $deliveries[$delivery_id]['items'][] = [
                'delivery_item_id' => $row['delivery_item_id'],
                'bill_number' => $row['bill_number'],
                'bill_customer_name' => $row['bill_customer_name'],
                'bill_customer_id' => $row['bill_customer_id'],
                'item_code' => $row['item_code'],
                'item_desc' => $row['item_desc'],
                'item_sequence' => $row['item_sequence'],
                'item_quantity' => $row['item_quantity'],
                'item_unit' => $row['item_unit'],
                'item_price' => $row['item_price'],
                'line_total' => $row['line_total'],
                'item_weight' => $row['item_weight'],
                'transfer_type' => $row['transfer_type']
            ];
            
            $deliveries[$delivery_id]['item_count']++;
            
            // Collect unique transfer types
            if (!in_array($row['transfer_type'], $deliveries[$delivery_id]['transfer_types'])) {
                $deliveries[$delivery_id]['transfer_types'][] = $row['transfer_type'];
            }
        }
    }
    
    $stmt->close();
    
    // Convert to simple array and add transfer_type field
    $items = [];
    foreach ($deliveries as $delivery) {
        $delivery['transfer_type'] = !empty($delivery['transfer_types']) ? implode(', ', $delivery['transfer_types']) : 'N/A';
        unset($delivery['transfer_types']); // Remove temporary field
        $items[] = $delivery;
    }
    
    if (empty($items)) {
        echo json_encode(['error' => 'No delivery data found']);
        exit;
    }
    
    // Return success response
    echo json_encode([
        'success' => true,
        'items' => $items,
        'total_count' => count($items)
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>