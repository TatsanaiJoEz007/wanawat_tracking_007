<?php
require_once('../../config/connect.php');

header('Content-Type: application/json');

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $freq_header = $_POST['freq_header'];
    $freq_content = $_POST['freq_content'];

    // Check if form fields are not empty
    if (!empty($freq_header) && !empty($freq_content)) {
        $sql = "INSERT INTO tb_freq (freq_header, freq_content, freq_create_at, freq_status) VALUES (?, ?, CURRENT_TIMESTAMP(), '1')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $freq_header, $freq_content); // Fixed binding parameters

        // Execute the statement
        if ($stmt->execute()) {
            $response = ['success' => true, 'message' => 'Data inserted successfully.'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to insert data into database'];
        }
        $stmt->close();
    } else {
        $response = ['success' => false, 'message' => 'Form fields cannot be empty'];
    }
    echo json_encode($response);
} else {
    $response = ['success' => false, 'message' => 'Invalid request'];
    echo json_encode($response);
}

$conn->close();
?>
