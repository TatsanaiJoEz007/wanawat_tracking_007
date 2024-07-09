<?php
// Connect to the database
require_once('../config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if trackingId is sent
if (isset($_POST['trackingId'])) {
    $trackingId = $_POST['trackingId'];

    // SQL query to find delivery_number in tb_delivery or bill_number in tb_delivery_items
    $sql = "
        SELECT d.*, di.*
        FROM tb_delivery d
        LEFT JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id
        WHERE d.delivery_number = ? OR TRIM(di.bill_number) = ?
    ";

    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $trackingId, $trackingId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if the query found a match
    if (mysqli_num_rows($result) > 0) {
        // Fetch the first row of the result
        $row = mysqli_fetch_assoc($result);
        // Retrieve the status from tb_delivery
        $status = $row['delivery_status'];
        // Prepare the response
        $response = array('status' => 'match', 'delivery_status' => $status);
    } else {
        // No match found in the database
        $response = array('status' => 'not_found');
    }

    // Encode the response as JSON and send it back to the client
    echo json_encode($response);
}
?>
