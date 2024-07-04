<?php
header('Content-Type: application/json');

// Get the input data
$input = file_get_contents('php://input');
error_log("Received input: " . $input);

$data = json_decode($input, true);
error_log("Parsed data: " . print_r($data, true));

if (isset($data['deliveryIds']) && is_array($data['deliveryIds'])) {
    // Sanitize and validate delivery IDs
    $deliveryIds = array_map('intval', $data['deliveryIds']);
    $deliveryIds = array_filter($deliveryIds, function($id) {
        return $id > 0; // Ensure all IDs are positive integers
    });
    error_log("Sanitized Delivery IDs: " . implode(", ", $deliveryIds));

    if (empty($deliveryIds)) {
        error_log("Empty or invalid delivery IDs.");
        echo json_encode(['status' => 'error', 'message' => 'Invalid delivery IDs.']);
        exit;
    }

    // Database connection
    include '../../../view/config/connect.php';

    if ($conn->connect_error) {
        error_log("Connection failed: " . $conn->connect_error);
        echo json_encode(['status' => 'error', 'message' => 'Connection failed']);
        exit;
    }

    $conn->begin_transaction();

    // Prepare the SQL query with placeholders
    $placeholders = implode(',', array_fill(0, count($deliveryIds), '?'));
    $sql = "SELECT delivery_id, delivery_status FROM tb_delivery WHERE delivery_id IN ($placeholders) FOR UPDATE";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed']);
        exit;
    }

    $types = str_repeat('i', count($deliveryIds));
    $stmt->bind_param($types, ...$deliveryIds);

    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $deliveries = $result->fetch_all(MYSQLI_ASSOC);
        error_log("Fetched deliveries: " . print_r($deliveries, true));

        $updateSql = "UPDATE tb_delivery SET delivery_status = ? WHERE delivery_id = ?";
        $updateStmt = $conn->prepare($updateSql);

        if ($updateStmt === false) {
            error_log("Update prepare failed: (" . $conn->errno . ") " . $conn->error);
            echo json_encode(['status' => 'error', 'message' => 'Update prepare failed']);
            $conn->rollback();
            exit;
        }

        foreach ($deliveries as $delivery) {
            $status = $delivery['delivery_status'];

            if ($status == 99) {
                $status = 1;
            } elseif ($status >= 5) {
                echo json_encode(['status' => 'error', 'code' => 'status_limit', 'message' => 'อยู่ในสถานะสำเร็จแล้ว ไม่สามารถอัพเดทได้อีก']);
                $conn->rollback();
                exit;
            } else {
                $status++;
            }

            $updateStmt->bind_param('ii', $status, $delivery['delivery_id']);
            if ($updateStmt->execute() === false) {
                error_log("Update execute failed: (" . $updateStmt->errno . ") " . $updateStmt->error);
                echo json_encode(['status' => 'error', 'message' => 'Update execute failed']);
                $conn->rollback();
                exit;
            } else {
                error_log("Updated delivery_id: " . $delivery['delivery_id'] . " to status: " . $status);
            }
        }

        $conn->commit();
        echo json_encode(['status' => 'success']);
    } else {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch delivery statuses.']);
        $conn->rollback();
    }

    // Close statements and connections
    $stmt->close();
    $updateStmt->close();
    $conn->close();
} else {
    error_log("Invalid delivery IDs format or empty delivery IDs.");
    echo json_encode(['status' => 'error', 'message' => 'Invalid delivery IDs.']);
}
?>