<?php
header('Content-Type: application/json');

$input = file_get_contents('php://input');
error_log("Received input: " . $input);

$data = json_decode($input, true);
error_log("Parsed data: " . print_r($data, true));

if (isset($data['deliveryIds']) && is_array($data['deliveryIds'])) {
    $deliveryIds = $data['deliveryIds'];
    error_log("Delivery IDs: " . implode(", ", $deliveryIds));

    // Assuming you have a database connection in $conn
    include '../../../view/config/connect.php';

    // Check if connection is established
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

    // Log prepared statement
    if ($stmt === false) {
        error_log("Prepare failed: (" . $conn->errno . ") " . $conn->error);
        echo json_encode(['status' => 'error', 'message' => 'Prepare failed']);
        exit;
    }

    // Bind the delivery IDs to the placeholders
    $types = str_repeat('i', count($deliveryIds));
    error_log("Bind param types: " . $types);
    $stmt->bind_param($types, ...$deliveryIds);

    // Execute the statement and log the result
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $deliveries = $result->fetch_all(MYSQLI_ASSOC);
        error_log("Fetched deliveries: " . print_r($deliveries, true));

        // Prepare the update statement
        $updateSql = "UPDATE tb_delivery SET delivery_status = ? WHERE delivery_id = ?";
        $updateStmt = $conn->prepare($updateSql);

        if ($updateStmt === false) {
            error_log("Update prepare failed: (" . $conn->errno . ") " . $conn->error);
            echo json_encode(['status' => 'error', 'message' => 'Update prepare failed']);
            $conn->rollback();
            exit;
        }

        // Update each delivery status
        foreach ($deliveries as $delivery) {
            $status = $delivery['delivery_status'];

            if ($status == 99) {
                $status = 1;
            } elseif ($status >= 5) {
                echo json_encode(['status' => 'error', 'code' => 'status_limit', 'message' => 'Status cannot be more than 5.']);
                $conn->rollback();
                exit;
            } else {
                $status++;
            }

            error_log("Updating delivery_id: " . $delivery['delivery_id'] . " to status: " . $status);
            $updateStmt->bind_param('ii', $status, $delivery['delivery_id']);
            $updateStmt->execute();
        }

        $conn->commit();
        echo json_encode(['status' => 'success']);
    } else {
        error_log("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
        echo json_encode(['status' => 'error', 'message' => 'Failed to fetch delivery statuses.']);
        $conn->rollback();
    }
} else {
    error_log("Invalid delivery IDs format or empty delivery IDs.");
    echo json_encode(['status' => 'error', 'message' => 'Invalid delivery IDs.']);
}
?>