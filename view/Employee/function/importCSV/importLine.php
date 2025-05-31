<?php
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['login'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Check for CSV data (accept both csvData and csvData2 for compatibility)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isset($_POST['csvData2']) || isset($_POST['csvData']))) {
    require_once('../../../../view/config/connect.php');
    
    // Get CSV data from either parameter
    $csvData = isset($_POST['csvData2']) ? $_POST['csvData2'] : $_POST['csvData'];
    $rows = str_getcsv($csvData, "\n");
    
    // Remove empty rows
    $rows = array_filter($rows, function($row) {
        return !empty(trim($row));
    });
    
    if (empty($rows)) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลที่ถูกต้องในไฟล์ CSV']);
        exit;
    }

    try {
        // Connect to the database using config variables
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $username, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Start a transaction
        $pdo->beginTransaction();
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($rows as $rowIndex => $row) {
            $data = str_getcsv($row);
            
            // Skip if not enough columns (expecting 8 columns)
            if (count($data) < 8) {
                $errorCount++;
                $errors[] = "แถวที่ " . ($rowIndex + 1) . ": ข้อมูลไม่ครบ (ต้องการ 8 คอลัมน์ แต่ได้ " . count($data) . " คอลัมน์)";
                continue;
            }
            
            // Clean data - trim whitespace
            $data = array_map('trim', $data);
            
            // Validate required fields (bill number and item code should not be empty)
            if (empty($data[0]) || empty($data[2])) {
                $errorCount++;
                $errors[] = "แถวที่ " . ($rowIndex + 1) . ": เลขที่บิลหรือรหัสสินค้าไม่สามารถเป็นค่าว่างได้";
                continue;
            }

            try {
                // Insert into tb_line table
                $stmt = $pdo->prepare("INSERT INTO tb_line (line_bill_number, item_sequence, item_code, item_desc, item_quantity, item_unit, item_price, line_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $data[0], // line_bill_number
                    $data[1], // item_sequence
                    $data[2], // item_code
                    $data[3], // item_desc
                    $data[4], // item_quantity
                    $data[5], // item_unit
                    $data[6], // item_price
                    $data[7]  // line_total
                ]);
                $successCount++;
            } catch (PDOException $e) {
                $errorCount++;
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    $errors[] = "แถวที่ " . ($rowIndex + 1) . ": ข้อมูลซ้ำกับที่มีอยู่แล้ว (Bill: {$data[0]}, Item: {$data[2]})";
                } else {
                    $errors[] = "แถวที่ " . ($rowIndex + 1) . ": " . $e->getMessage();
                }
            }
        }

        // Commit the transaction
        $pdo->commit();

        // Prepare response message
        $message = "นำเข้าข้อมูล Line สำเร็จ: {$successCount} แถว";
        if ($errorCount > 0) {
            $message .= ", ผิดพลาด: {$errorCount} แถว";
        }

        // Return JSON response
        echo json_encode([
            'status' => $successCount > 0 ? 'success' : 'error',
            'message' => $message,
            'details' => [
                'success_count' => $successCount,
                'error_count' => $errorCount,
                'total_rows' => count($rows),
                'errors' => array_slice($errors, 0, 10) // Limit to first 10 errors
            ]
        ]);

    } catch (PDOException $e) {
        // Rollback the transaction on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        echo json_encode([
            'status' => 'error', 
            'message' => 'เกิดข้อผิดพลาดในฐานข้อมูล: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid request method or missing CSV data parameter'
    ]);
}
?>