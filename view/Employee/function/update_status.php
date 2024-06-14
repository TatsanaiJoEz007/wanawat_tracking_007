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

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON data sent from frontend
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if deliveryId is provided in JSON data
    if (isset($data['deliveryId'])) {
        // Sanitize and validate deliveryId (example)
        $deliveryId = mysqli_real_escape_string($conn, $data['deliveryId']);

        // Fetch current delivery status
        $selectQuery = "SELECT delivery_status FROM tb_delivery WHERE delivery_id = ?";
        $stmt = $conn->prepare($selectQuery);
        $stmt->bind_param('i', $deliveryId);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($currentStatus);
            $stmt->fetch();

            // Determine new status, ensuring it doesn't exceed 5
            if ($currentStatus == 99) {
                $newStatus = 1; // Reset to 1 if it was 99
            } else {
                $newStatus = min($currentStatus + 1, 5); // Increment by 1, max 5
            }

            // Update delivery status in database
            $updateQuery = "UPDATE tb_delivery SET delivery_status = ? WHERE delivery_id = ?";
            $stmt = $conn->prepare($updateQuery);
            $stmt->bind_param('ii', $newStatus, $deliveryId);

            if ($stmt->execute()) {
                // Return success response if update was successful
                $response = ['status' => 'success', 'message' => 'Delivery status updated successfully.'];
                http_response_code(200);
                echo json_encode($response);
                exit;
            } else {
                // Return error response if update failed
                $response = ['status' => 'error', 'message' => 'Failed to update delivery status.'];
                http_response_code(500); // Internal Server Error
                echo json_encode($response);
                exit;
            }
        } else {
            // Return error response if deliveryId is not found
            $response = ['status' => 'error', 'message' => 'Delivery ID not found.'];
            http_response_code(404); // Not Found
            echo json_encode($response);
            exit;
        }
    } else {
        // Return error response if deliveryId is not provided
        $response = ['status' => 'error', 'message' => 'Delivery ID not provided.'];
        http_response_code(400); // Bad Request
        echo json_encode($response);
        exit;
    }
} else {
    // Return error response if request method is not POST
    http_response_code(405); // Method Not Allowed
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
    exit;
}
?>
