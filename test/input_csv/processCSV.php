<?php
// Check if the file is uploaded successfully
if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] != 0) {
    echo json_encode(['message' => 'Error uploading file', 'errors' => ['File upload failed']]);
    exit;
}

// Get the uploaded file
$uploadedFile = $_FILES['csvFile']['tmp_name'];

// Detect the encoding of the CSV file
$encoding = mb_detect_encoding(file_get_contents($uploadedFile), 'ASCII, windows-874, UTF-8, ISO-8859-11');

// Convert the file to UTF-8
$fileContent = file_get_contents($uploadedFile);
$utf8Content = mb_convert_encoding($fileContent, 'UTF-8', $encoding);
file_put_contents($uploadedFile, $utf8Content);

// Import the CSV file to the database
$delimiter = ',';
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'wanawat_tracking';

$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    echo json_encode(['message' => 'Error connecting to database', 'errors' => [$conn->connect_error]]);
    exit;
}

$conn->set_charset('utf8mb4');

$fp = fopen($uploadedFile, 'r');
while (($row = fgetcsv($fp, 0, $delimiter)) !== FALSE) {
    $insertQuery = "INSERT INTO tb_bill (bill_date, bill_number, bill_customer_id, bill_customer_name, bill_total, bill_isCanceled) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertQuery);
    if ($stmt) {
        $stmt->bind_param('ssssss', $row[0], $row[1], $row[2], $row[3], $row[4], $row[5]);
        $result = $stmt->execute();
        if (!$result) {
            echo json_encode(['message' => 'Error inserting data', 'errors' => [$conn->error]]);
            exit;
        }
        $stmt->close();
    } else {
        echo json_encode(['message' => 'Error preparing statement', 'errors' => [$conn->error]]);
        exit;
    }
}

fclose($fp);
$conn->close();

echo json_encode(['message' => 'CSV file imported successfully']);