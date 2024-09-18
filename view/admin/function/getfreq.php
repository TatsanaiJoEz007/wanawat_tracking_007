<?php
require_once('../../config/connect.php'); // Include database connection

header('Content-Type: application/json'); // Ensure the content type is JSON

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize the ID

    // Query to fetch the FAQ
    $query = "SELECT * FROM tb_freq WHERE freq_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $faq = $result->fetch_assoc();
        echo json_encode($faq); // Return the FAQ data as JSON
    } else {
        echo json_encode(['error' => 'FAQ not found']); // Handle case where FAQ is not found
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'Invalid request']); // Handle invalid request
}

$conn->close();
?>