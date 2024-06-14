<?php
// Step 1: Connect to your database
$servername = "localhost";  // Change this if your database is on a different server
$username = "root";
$password = "";
$dbname = "wanawat_tracking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Retrieve POST data
$data = json_decode(file_get_contents("php://input"), true);

$deliveryId = isset($data['deliveryId']) ? intval($data['deliveryId']) : null;
$problem = isset($data['problem']) ? $data['problem'] : '';

if ($deliveryId === null || $problem === '') {
    die("Invalid request. Missing deliveryId or problem description.");
}

// Step 3: Update delivery status in the database
$sql = "UPDATE tb_delivery SET delivery_status = 99 WHERE delivery_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $deliveryId);

if ($stmt->execute()) {
    $response = array(
        'status' => 'success',
        'message' => 'Parcel problem reported successfully.'
    );
    echo json_encode($response);
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Error reporting parcel problem: ' . $stmt->error
    );
    echo json_encode($response);
}

$stmt->close();
$conn->close();
