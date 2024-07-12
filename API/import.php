<?php
require_once('../view/config/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['csvType']) && $_POST['csvType'] === 'head') {
        handleCSVImport('head');
    } elseif (isset($_POST['csvType']) && $_POST['csvType'] === 'line') {
        handleCSVImport('line');
    }
}

function handleCSVImport($type) {
    $csvData = $_POST['csvData'];
    $rows = str_getcsv($csvData, "\n");

    global $host, $db, $username, $pass;

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $username, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->beginTransaction();

        $rejectedData = [];

        if ($type === 'head') {
            $stmt = $pdo->prepare("INSERT INTO tb_header (bill_date, bill_number, bill_customer_id, bill_customer_name, bill_total, bill_isCanceled , bill_weight) VALUES (?, ?, ?, ?, ?, ? , ?)");
            foreach ($rows as $row) {
                $data = str_getcsv($row);
                if (count($data) === 7) {
                    // Check for duplicates
                    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM tb_header WHERE bill_number = ?");
                    $checkStmt->execute([$data[1]]);
                    $count = $checkStmt->fetchColumn();

                    if ($count == 0) {
                        $stmt->execute($data);
                    } else {
                        $rejectedData[] = $data;
                    }
                }
            }
        } elseif ($type === 'line') {
            $stmt = $pdo->prepare("INSERT INTO tb_line (line_bill_number, item_sequence, item_code, item_desc, item_quantity, item_unit, item_price, line_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($rows as $row) {
                $data = str_getcsv($row);
                if (count($data) === 8) {
                    // Check for duplicates
                    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM tb_line WHERE line_bill_number = ? AND item_sequence = ?");
                    $checkStmt->execute([$data[0], $data[1]]);
                    $count = $checkStmt->fetchColumn();

                    if ($count == 0) {
                        $stmt->execute($data);
                    } else {
                        $rejectedData[] = $data;
                    }
                }
            }
        }

        $pdo->commit();
        echo json_encode([
            'status' => 'success',
            'message' => 'Importing data successfully!',
            'rejected' => $rejectedData
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
