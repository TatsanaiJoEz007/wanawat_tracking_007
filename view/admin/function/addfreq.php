<?php
require_once('../../config/connect.php');

header('Content-Type: application/json');

// Check if form data is received through POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $freq_header = $_POST['freq_header'];
    $freq_content = $_POST['freq_content'];

    // Check if form fields are not empty
    if (!empty($freq_header) && !empty($freq_content)) {
        $stmt = $conn->prepare("INSERT INTO tb_freq (freq_header, freq_content, freq_create_at, freq_status) VALUES (?, ?, CURRENT_TIMESTAMP(), '1')");
        $stmt->bind_param("ss", $freq_header, $freq_content);
        $stmt->execute();

        // Check if data is inserted successfully
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save data to the database.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Form data is incomplete.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

$conn->close();
?>
