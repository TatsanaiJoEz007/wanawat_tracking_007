<?php
header('Content-Type: application/json');

require_once('../../../view/config/connect.php');

try {
    // Connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$db", $username, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get action from query string
    $action = $_GET['action'] ?? '';

    // Get raw POST data
    $postData = file_get_contents('php://input');
    $data = json_decode($postData, true);

    if ($action === 'importHead' && isset($data['data'])) {
        $csvData = $data['data'];
        $rows = str_getcsv($csvData, "\n");

        try {
            // Start a transaction
            $pdo->beginTransaction();

            foreach ($rows as $row) {
                $rowData = str_getcsv($row);
                if (count($rowData) === 6) {
                    // Check for duplicates
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tb_header WHERE bill_number = ?");
                    $stmt->execute([$rowData[1]]);
                    $count = $stmt->fetchColumn();

                    if ($count == 0) {
                        $stmt = $pdo->prepare("INSERT INTO tb_header (bill_date, bill_number, bill_customer_id, bill_customer_name, bill_total, bill_isCanceled) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute($rowData);
                    }
                }
            }

            // Commit the transaction
            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Data imported successfully']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to import data: ' . $e->getMessage()]);
        }
    } elseif ($action === 'importLine' && isset($data['data'])) {
        $csvData2 = $data['data'];
        $rows = str_getcsv($csvData2, "\n");

        try {
            // Start a transaction
            $pdo->beginTransaction();

            foreach ($rows as $row) {
                $rowData = str_getcsv($row);
                if (count($rowData) === 8) {
                    // Check for duplicates
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM tb_line WHERE line_bill_number = ? AND item_sequence = ?");
                    $stmt->execute([$rowData[0], $rowData[1]]);
                    $count = $stmt->fetchColumn();

                    if ($count == 0) {
                        $stmt = $pdo->prepare("INSERT INTO tb_line (line_bill_number, item_sequence, item_code, item_desc, item_quantity, item_unit, item_price, line_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute($rowData);
                    }
                }
            }

            // Commit the transaction
            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Data imported successfully']);
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Failed to import data: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action or missing data.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
