<?php
header('Content-Type: application/json'); // Ensure the response is JSON

require_once('../../../../view/config/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csvData'])) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    $csvData = $_POST['csvData'];
    $rows = str_getcsv($csvData, "\n");

    try {
        // Connect to the database
        $pdo = new PDO("mysql:host=$host;dbname=$db", $username, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Start a transaction
        $pdo->beginTransaction();

        foreach ($rows as $row) {
            $data = str_getcsv($row);
            if (count($data) === 6) { // Check if the row has all 6 columns
                $stmt = $pdo->prepare("INSERT INTO tb_header (bill_date, bill_number, bill_customer_id, bill_customer_name, bill_total, bill_isCanceled) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute($data);
            }
        }

        // Commit the transaction
        $pdo->commit();

        // Return a success message
        echo json_encode(['status' => 'success', 'message' => 'Data imported successfully']);
    } catch (PDOException $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        // Return an error message
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method or missing csvData']);
}
?>