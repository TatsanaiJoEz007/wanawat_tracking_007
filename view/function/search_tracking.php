<?php
// Connect to the database
require_once('../config/connect.php');

// Check if trackingId is sent
if (isset($_POST['trackingId'])) {
    $trackingId = $_POST['trackingId'];

    // SQL query to find delivery_number in tb_delivery
    $sql = "SELECT * FROM tb_delivery WHERE delivery_number = ?";

    // Use prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $trackingId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if the query found a match
    if (mysqli_num_rows($result) > 0) {
        // delivery_number found in the database
        $response = array('status' => 'match');
    } else {
        // delivery_number not found in the database
        $response = array('status' => 'not_found');
    }

    // Encode the response as JSON and send it back to the client
    echo json_encode($response);
}
?>
