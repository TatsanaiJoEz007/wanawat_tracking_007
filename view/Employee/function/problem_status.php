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
$deliveryId = isset($_POST['deliveryId']) ? intval($_POST['deliveryId']) : null;

if ($deliveryId === null) {
    die("Invalid request. Missing deliveryId.");
}

// Step 3: Update delivery status in the database
$sql = "UPDATE tb_delivery SET delivery_status = 5 WHERE delivery_id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $deliveryId);

if ($stmt->execute()) {
    echo "Parcel problem reported successfully.";
} else {
    echo "Error reporting parcel problem: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
