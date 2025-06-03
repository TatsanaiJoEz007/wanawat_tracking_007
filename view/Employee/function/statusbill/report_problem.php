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
$problem_description = isset($input['problem_description']) ? trim($input['problem_description']) : '';
$problem_type = isset($input['problem_type']) ? trim($input['problem_type']) : '';
$user_id = $_SESSION['user_id'];

// Validate input
if (empty($delivery_ids)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No delivery IDs provided']);
    exit;
}

if (empty($problem_description)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Problem description is required']);
    exit;
}

// Validate problem description length
if (strlen($problem_description) < 10) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Problem description must be at least 10 characters']);
    exit;
}

if (strlen($problem_description) > 1000) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Problem description must not exceed 1000 characters']);
    exit;
}

try {
    $conn->begin_transaction();
    
    $updated_count = 0;
    $errors = [];
    $warnings = [];
    $updated_deliveries = [];
    
    foreach ($delivery_ids as $delivery_id) {
        $delivery_id = intval($delivery_id);
        
        if ($delivery_id <= 0) {
            $errors[] = "Invalid delivery ID: $delivery_id";
            continue;
        }
        
        // Check if delivery exists and get current status
        $stmt = $conn->prepare("SELECT delivery_number, delivery_status FROM tb_delivery WHERE delivery_id = ?");
        $stmt->bind_param("i", $delivery_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 0) {
            $errors[] = "Delivery not found: $delivery_id";
            $stmt->close();
            continue;
        }
        
        $delivery_data = $result->fetch_assoc();
        $delivery_number = $delivery_data['delivery_number'];
        $current_status = $delivery_data['delivery_status'];
        $stmt->close();
        
        // Check if delivery is already completed
        if ($current_status == 5) {
            $warnings[] = "การขนส่ง $delivery_number เสร็จสิ้นแล้ว แต่ยังคงรายงานปัญหาได้";
        }
        
        // Check if delivery already has problem status
        if ($current_status == 99) {
            $warnings[] = "การขนส่ง $delivery_number มีปัญหาอยู่แล้ว รายละเอียดปัญหาจะถูกอัปเดต";
        }
        
        // Prepare problem description with type if provided
        $full_problem_desc = $problem_description;
        if (!empty($problem_type) && $problem_type !== 'อื่นๆ') {
            $full_problem_desc = "ประเภทปัญหา: " . $problem_type . "\n\nรายละเอียด: " . $problem_description;
        }
        
        // Add timestamp and user info
        $timestamp = date('Y-m-d H:i:s');
        $full_problem_desc .= "\n\n--- รายงานโดย: User ID $user_id เมื่อ $timestamp ---";
        
        // Update delivery status to 99 (problem status) and save problem description
        $stmt = $conn->prepare("UPDATE tb_delivery SET delivery_status = 99, delivery_problem_desc = ? WHERE delivery_id = ?");
        $stmt->bind_param("si", $full_problem_desc, $delivery_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $updated_count++;
                $updated_deliveries[] = $delivery_number;
                
                // Optional: Log the problem in a separate problems log table if you have one
                // $log_stmt = $conn->prepare("INSERT INTO tb_delivery_problems_log (delivery_id, problem_type, problem_desc, reported_by, reported_at) VALUES (?, ?, ?, ?, NOW())");
                // $log_stmt->bind_param("issi", $delivery_id, $problem_type, $problem_description, $user_id);
                // $log_stmt->execute();
                // $log_stmt->close();
                
            } else {
                $warnings[] = "ไม่มีการเปลี่ยนแปลงสำหรับการขนส่ง $delivery_number";
            }
        } else {
            $errors[] = "Update failed for delivery $delivery_number: " . $stmt->error;
        }
        
        $stmt->close();
    }
    
    if ($updated_count > 0) {
        $conn->commit();
        
        $response = [
            'success' => true,
            'message' => "รายงานปัญหาสำเร็จ จำนวน $updated_count รายการ",
            'updated_count' => $updated_count,
            'updated_deliveries' => $updated_deliveries
        ];
        
        if (!empty($warnings)) {
            $response['warnings'] = $warnings;
        }
        
        if (!empty($errors)) {
            $response['partial_errors'] = $errors;
        }
        
        echo json_encode($response);
    } else {
        $conn->rollback();
        echo json_encode([
            'success' => false,
            'message' => 'ไม่สามารถรายงานปัญหาได้',
            'errors' => $errors
        ]);
    }
    
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error_details' => $e->getMessage()
    ]);
    
    // Log error for debugging (you should implement proper logging)
    error_log("Report Problem Error: " . $e->getMessage() . " in file " . __FILE__ . " on line " . __LINE__);
}

$conn->close();
?>