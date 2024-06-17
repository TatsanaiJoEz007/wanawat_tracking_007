<?php
// Step 1: Connect to your database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wanawat_tracking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// อัพเดทสถานะเป็น 0 ในฐานข้อมูลสำหรับเลขบิลที่กำหนด
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $billNumber = $_POST['bill_number'];
    $sql = "UPDATE tb_line SET line_status = 0 WHERE bill_number = '$billNumber'";
    if ($conn->query($sql) === TRUE) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . $conn->error;
    }
}

$conn->close();
// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Decode JSON data sent from frontend
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if selected_items is provided in JSON data
    if (isset($data['selected_items']) && is_array($data['selected_items'])) {
        $selectedItems = $data['selected_items'];
        $error = false;

        // Start transaction
        $conn->begin_transaction();

        foreach ($selectedItems as $item) {
            // Sanitize and validate item data (example)
            $itemCode = mysqli_real_escape_string($conn, $item['item_code']);

            // Query to fetch line_bill_number from tb_header based on item_code
            $selectHeaderQuery = "SELECT th.line_bill_number
                                  FROM tb_header th
                                  INNER JOIN tb_line tl ON th.bill_number = tl.line_bill_number
                                  WHERE tl.item_code = ?";
            $stmt = $conn->prepare($selectHeaderQuery);
            $stmt->bind_param('s', $itemCode);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $stmt->bind_result($lineBillNumber);
                $stmt->fetch();

                // Update tb_line to set line_status = 0 based on line_bill_number and item_code
                $updateQuery = "UPDATE tb_line 
                                SET line_status = 0 
                                WHERE line_bill_number = ? 
                                AND item_code = ?";
                $stmtUpdate = $conn->prepare($updateQuery);
                $stmtUpdate->bind_param('ss', $lineBillNumber, $itemCode);

                if ($stmtUpdate->execute()) {
                    echo "Line status updated for item_code: $itemCode<br>";
                } else {
                    $error = true;
                    echo "Failed to update line status for item_code: $itemCode<br>";
                    break; // Exit loop if any query fails
                }
            } else {
                // If line_bill_number is not found, set error flag
                $error = true;
                echo "No line_bill_number found for item_code: $itemCode<br>";
                break; // Exit loop if no line_bill_number found
            }
        }

        if (!$error) {
            // Commit transaction if all queries are successful
            $conn->commit();
            $response = ['status' => 'success', 'message' => 'Line status updated successfully.'];
            http_response_code(200);
            echo json_encode($response);
            exit;
        } else {
            // Rollback transaction if any query fails
            $conn->rollback();
            $response = ['status' => 'error', 'message' => 'Failed to update line status.'];
            http_response_code(500); // Internal Server Error
            echo json_encode($response);
            exit;
        }
    } else {
        // Return error response if selected_items is not provided or not an array
        $response = ['status' => 'error', 'message' => 'Invalid selected items data.'];
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
