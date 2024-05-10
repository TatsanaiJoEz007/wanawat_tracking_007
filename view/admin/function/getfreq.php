<?php
// Include database connection
require_once('../../config/connect.php');

// Check if freq_id is set and not empty
if(isset($_POST['freq_id']) && !empty($_POST['freq_id'])) {
    // Sanitize the input to prevent SQL injection
    $freq_id = mysqli_real_escape_string($conn, $_POST['freq_id']);

    // Prepare SQL statement to fetch data based on freq_id
    $sql = "SELECT * FROM tb_freq WHERE freq_id = '$freq_id'";
    // Execute the query
    $result = mysqli_query($conn, $sql);
    // Check if query was successful
    if($result) {
        // Fetch the data as an associative array
        $row = mysqli_fetch_assoc($result);

        // Return data as JSON
        echo json_encode($row);
    } else {
        // Return an error message if query fails
        echo json_encode(['error' => 'Failed to fetch data']);
    }
} else {
    // Return an error message if freq_id is not set or empty
    echo json_encode(['error' => 'freq_id is not set or empty']);
}
?>
