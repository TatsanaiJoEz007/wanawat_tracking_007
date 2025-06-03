<?php
session_start();
require_once('../../../config/connect.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit;
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    $input = $_POST;
}

$delivery_ids = isset($input['delivery_ids']) ? $input['delivery_ids'] : [];
$new_status = isset($input['new_status']) ? intval($input['new_status']) : 0;
$user_id = $_SESSION['user_id'];

// Validate input
if (empty($delivery_ids) || $new_status <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input data']);
    exit;
}

// Validate status range
if ($new_status < 1 || $new_status > 5) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid status value']);
    exit;
}

try {
    $conn->begin_transaction();
    
    $updated_count = 0;
    $errors = [];
    
    foreach ($delivery_ids as $delivery_id) {
        $delivery_id = intval($delivery_id);
        
        if ($delivery_id <= 0) {
            $errors[] = "Invalid delivery ID: $delivery_id";
            continue;
        }
        
        // Get current status to check if we need to update timestamps
        $stmt = $conn->prepare("SELECT delivery_status FROM tb_delivery WHERE delivery_id = ?");
        $stmt->bind_param("i", $delivery_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $errors[] = "Delivery not found: $delivery_id";
            $stmt->close();
            continue;
        }
        
        $current_data = $result->fetch_assoc();
        $current_status = intval($current_data['delivery_status']);
        $stmt->close();
        
        // Determine which timestamp column to update based on new status
        $timestamp_column = '';
        switch ($new_status) {
            case 1:
                $timestamp_column = 'delivery_step1_received';
                break;
            case 2:
                $timestamp_column = 'delivery_step2_transit';
                break;
            case 3:
                $timestamp_column = 'delivery_step3_warehouse';
                break;
            case 4:
                $timestamp_column = 'delivery_step4_last_mile';
                break;
            case 5:
                $timestamp_column = 'delivery_step5_completed';
                break;
        }
        
        // Build the update query
        $update_fields = [];
        $update_values = [];
        $types = '';
        
        // Always update the status
        $update_fields[] = 'delivery_status = ?';
        $update_values[] = $new_status;
        $types .= 'i';
        
        // Update timestamp if status is progressing forward or if timestamp is null
        if ($timestamp_column) {
            // Check if timestamp column is already set
            $stmt = $conn->prepare("SELECT $timestamp_column FROM tb_delivery WHERE delivery_id = ?");
            $stmt->bind_param("i", $delivery_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $timestamp_data = $result->fetch_assoc();
            $stmt->close();
            
            // Update timestamp if it's null or if we're moving to a higher status
            if (is_null($timestamp_data[$timestamp_column]) || $new_status > $current_status) {
                $update_fields[] = "$timestamp_column = NOW()";
            }
            
            // Also update all previous step timestamps if they're null (for consistency)
            for ($i = 1; $i < $new_status; $i++) {
                $prev_column = '';
                switch ($i) {
                    case 1: $prev_column = 'delivery_step1_received'; break;
                    case 2: $prev_column = 'delivery_step2_transit'; break;
                    case 3: $prev_column = 'delivery_step3_warehouse'; break;
                    case 4: $prev_column = 'delivery_step4_last_mile'; break;
                }
                
                if ($prev_column) {
                    // Check if previous step timestamp is null
                    $stmt = $conn->prepare("SELECT $prev_column FROM tb_delivery WHERE delivery_id = ?");
                    $stmt->bind_param("i", $delivery_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $prev_data = $result->fetch_assoc();
                    $stmt->close();
                    
                    if (is_null($prev_data[$prev_column])) {
                        $update_fields[] = "$prev_column = NOW()";
                    }
                }
            }
        }
        
        // Add user_id for audit trail (if column exists)
        // $update_fields[] = 'updated_by = ?';
        // $update_values[] = $user_id;
        // $types .= 'i';
        
        // Execute the update
        $sql = "UPDATE tb_delivery SET " . implode(', ', $update_fields) . " WHERE delivery_id = ?";
        $update_values[] = $delivery_id;
        $types .= 'i';
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $errors[] = "Prepare failed for delivery $delivery_id: " . $conn->error;
            continue;
        }
        
        $stmt->bind_param($types, ...$update_values);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $updated_count++;
            } else {
                $errors[] = "No changes made for delivery $delivery_id";
            }
        } else {
            $errors[] = "Update failed for delivery $delivery_id: " . $stmt->error;
        }
        
        $stmt->close();
    }
    
    if ($updated_count > 0) {
        $conn->commit();
        
        // Prepare success response
        $status_text = '';
        switch ($new_status) {
            case 1: $status_text = 'รับคำสั่งซื้อ'; break;
            case 2: $status_text = 'กำลังจัดส่งไปศูนย์'; break;
            case 3: $status_text = 'ถึงศูนย์กระจาย'; break;
            case 4: $status_text = 'กำลังส่งลูกค้า'; break;
            case 5: $status_text = 'ส่งสำเร็จ'; break;
        }
        
        $response = [
            'success' => true,
            'message' => "อัปเดตสถานะเป็น '$status_text' สำเร็จ จำนวน $updated_count รายการ",
            'updated_count' => $updated_count,
            'new_status' => $new_status,
            'status_text' => $status_text
        ];
        
        if (!empty($errors)) {
            $response['warnings'] = $errors;
        }
        
        echo json_encode($response);
    } else {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'ไม่สามารถอัปเดตข้อมูลได้',
            'errors' => $errors
        ]);
    }
    
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$conn->close();
?>