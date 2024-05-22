<?php
header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input === null && json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input');
    }

    $records = $input['records'] ?? [];
    if (empty($records)) {
        throw new Exception('No records received');
    }

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "wanawat_tracking";
    $tableName = "tb_bill";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        throw new Exception('Connection failed: ' . $conn->connect_error);
    }

    $errors = [];
    foreach ($records as $record) {
        // Only one column in the CSV, so set the value directly
        $bill_date = $record[0];
        $bill_number = $record[1]; // Modify as per your requirement
        $bill_customer_id = $record[2]; // Modify as per your requirement
        $bill_customer_name = $record[3]; // Modify as per your requirement
        $bill_total = $record[4]; // Modify as per your requirement
        $bill_isCanceled = $record[5]; // Modify as per your requirement

        $sql = "INSERT INTO $tableName (bill_date, bill_number, bill_customer_id, bill_customer_name, bill_total, bill_isCanceled) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception('Error preparing statement: ' . $conn->error);
        }

        // Bind parameters
        $stmt->bind_param("ssssss", $bill_date, $bill_number, $bill_customer_id, $bill_customer_name, $bill_total, $bill_isCanceled);

        // Execute statement
        if (!$stmt->execute()) {
            $errors[] = "Error executing statement: " . $stmt->error;
            // Log the failed SQL query for debugging
            error_log('Failed SQL query: ' . $sql);
            // Log the values being inserted for debugging
            error_log('Failed record values: ' . json_encode($record));
        }

        // Close statement
        $stmt->close();
    }

    $conn->close();
    
    if (empty($errors)) {
        echo json_encode(["status" => "success", "message" => "Data imported successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Errors occurred during import", "errors" => $errors]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    // Log the error for debugging
    error_log('Error in processCSV.php: ' . $e->getMessage());
}
?>
