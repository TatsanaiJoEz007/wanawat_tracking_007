<?php
require_once('../../config/connect.php');

header('Content-Type: application/json'); // Important: Set header to JSON

$bannerName = $_POST['user_firstname'];
$bannerImg = $_FILES['user_img']['tmp_name']; // Use 'tmp_name' to read the temporary file

// Read the file content as binary data
$imgData = file_get_contents($bannerImg);

if ($imgData !== false) {
    $stmt = $conn->prepare("INSERT INTO tb_banner (banner_name, banner_img) VALUES (?, ?)");
    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Prepare statement failed: ' . $conn->error]);
        $conn->close();
        exit();
    }

    $null = NULL;
    $stmt->bind_param("sb", $bannerName, $null);
    $stmt->send_long_data(1, $imgData); // Send binary data to the second parameter
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Execute statement failed: ' . $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Unable to read file']);
}
$conn->close();
?>
