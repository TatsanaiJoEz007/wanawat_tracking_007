<?php
header('Content-Type: application/json');

// Database connection settings
$dbHost = 'localhost';
$dbName = 'wanawat_tracking';
$dbUser = 'root';
$dbPass = '';

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_GET['action'] === 'importHead' && isset($_POST['csvData'])) {
        $csvData = $_POST['csvData'];
        $rows = str_getcsv($csvData, "\n");

        try {
            // Start a transaction
            $pdo->beginTransaction();

            foreach ($rows as $row) {
                $data = str_getcsv($row);
                if (count($data) === 6) {
                    $stmt = $pdo->prepare("INSERT INTO tb_header (bill_date, bill_number, bill_customer_id, bill_customer_name, bill_total, bill_isCanceled) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute($data);
                }
            }

            // Commit the transaction
            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Data imported successfully']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to import data: ' . $e->getMessage()]);
        }
    }

    if ($_GET['action'] === 'importLine' && isset($_POST['csvData2'])) {
        $csvData2 = $_POST['csvData2'];
        $rows = str_getcsv($csvData2, "\n");

        try {
            // Start a transaction
            $pdo->beginTransaction();

            foreach ($rows as $row) {
                $data2 = str_getcsv($row);
                if (count($data2) === 9) {
                    $stmt = $pdo->prepare("INSERT INTO tb_line (line_bill_number, item_sequence, item_code, item_desc, item_quantity, item_unit, item_price, line_total, line_weight) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute($data2);
                }
            }

            // Commit the transaction
            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Data imported successfully']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to import data: ' . $e->getMessage()]);
        }
    }
}
?>
