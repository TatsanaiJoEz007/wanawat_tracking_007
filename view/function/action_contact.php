<?php
header('Content-Type: application/json');

require_once ('../config/connect.php');

// Initialize response array
$response = array();

// Check for POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize
    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $description = mysqli_real_escape_string($conn, $_POST["description"]);

    // Prepare SQL statement
    $sql = "INSERT INTO tb_question (question_sender_name, question_sender_email, question_content, question_create_at, question_status) 
    VALUES ('$name', '$email', '$description', NOW() , 1)";

    // Execute SQL statement
    if ($conn->query($sql) === TRUE) {
        $response["success"] = true;
        $response["message"] = "Question submitted successfully!";
    } else {
        $response["success"] = false;
        $response["message"] = "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    $response["success"] = false;
    $response["message"] = "Invalid request method.";
}

// Send JSON response
echo json_encode($response);

// Close connection
$conn->close();
?>
