<?php
// Include database connection
require_once('../../config/connect.php');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if all required fields are set and not empty
    if (isset($_POST['freq_id']) && isset($_POST['freq_header']) && isset($_POST['freq_content']) &&
        !empty($_POST['freq_id']) && !empty($_POST['freq_header']) && !empty($_POST['freq_content'])) {
        
        // Sanitize the input to prevent SQL injection
        $freq_id = mysqli_real_escape_string($conn, $_POST['freq_id']);
        $freq_header = mysqli_real_escape_string($conn, $_POST['freq_header']);
        $freq_content = mysqli_real_escape_string($conn, $_POST['freq_content']);

        // Prepare SQL statement to update data
        $sql = "UPDATE tb_freq SET freq_header = '$freq_header', freq_content = '$freq_content' WHERE freq_id = '$freq_id'";

        // Execute the query
        $result = mysqli_query($conn, $sql);

        // Check if the query was successful
        if ($result) {
            // Return success message as JSON
            echo json_encode(['success' => true]);
        } else {
            // Return error message if query fails
            echo json_encode(['error' => 'Failed to update FAQ']);
        }
    } else {
        // Return error message if required fields are missing or empty
        echo json_encode(['error' => 'Missing or empty fields']);
    }
} else {
    // Return error message if request method is not POST
    echo json_encode(['error' => 'Invalid request method']);
}
?>
