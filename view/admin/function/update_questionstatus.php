<?php
// Include database connection file
require_once('../../config/connect.php');

// Check if ID is set in POST request
if(isset($_POST['question_id'])) {
    // Sanitize the ID to prevent SQL injection
    $question_id = mysqli_real_escape_string($conn, $_POST['question_id']);

    // Update the question_status to 0
    $sql = "UPDATE tb_question SET question_status = 0 WHERE question_id = '$question_id'";
    if(mysqli_query($conn, $sql)) {
        // If update successful, send success response
        echo "success";
    } else {
        // If update failed, send error response
        echo "error";
    }
} else {
    // If ID is not set in POST request, send error response
    echo "error";
}

// Close database connection
mysqli_close($conn);
?>
