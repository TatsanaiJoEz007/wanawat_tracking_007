<?php
// Step 1: Connect to your database
require_once('../../../view/config/connect.php');

// Step 2: Retrieve POST data
$data = json_decode(file_get_contents("php://input"), true);

header('Content-Type: application/json');

if (!isset($data['deliveries']) || !is_array($data['deliveries'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request. Missing deliveries data.']);
    exit;
}

$response = array();

foreach ($data['deliveries'] as $delivery) {
    $deliveryId = isset($delivery['deliveryId']) ? intval($delivery['deliveryId']) : null;
    $problem = isset($delivery['problem']) ? $delivery['problem'] : '';

    if ($deliveryId === null || $problem === '') {
        $response[] = array(
            'status' => 'error',
            'deliveryId' => $deliveryId,
            'message' => 'Missing deliveryId or problem description.'
        );
        continue;
    }

    // Step 3: Update delivery status in the database
    $sql = "UPDATE tb_delivery SET delivery_status = 99 WHERE delivery_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        $response[] = array(
            'status' => 'error',
            'deliveryId' => $deliveryId,
            'message' => 'Error preparing statement: ' . $conn->error
        );
        continue;
    }

    $stmt->bind_param("i", $deliveryId);

    if ($stmt->execute()) {
        $response[] = array(
            'status' => 'success',
            'deliveryId' => $deliveryId,
            'message' => 'Parcel problem reported successfully.'
        );
    } else {
        $response[] = array(
            'status' => 'error',
            'deliveryId' => $deliveryId,
            'message' => 'Error reporting parcel problem: ' . $stmt->error
        );
    }

    $stmt->close();
}

$conn->close();
echo json_encode($response);
?>