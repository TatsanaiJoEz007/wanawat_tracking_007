<?php
header('Content-Type: application/json; charset=utf-8');

try {
    $input = file_get_contents('php://input');
    if ($input === false) {
        throw new Exception('Failed to retrieve input data');
    }
    
    // Convert Windows Thai to UTF-8
    $input_utf8 = iconv('Windows-874', 'UTF-8', $input);
    
    // Split the input data by comma to get individual records
    $records = explode("\n", $input_utf8); // Assuming each record is in a new line

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
        // Explode each record by comma to get individual values
        $fields = explode(",", $record);
        
        // Assuming each record has 6 fields
        if (count($fields) != 6) {
            $errors[] = "Invalid record format: " . $record;
            continue; // Skip this record
        }

        // Assigning values to variables
        list($bill_date, $bill_number, $bill_customer_id, $bill_customer_name, $bill_total, $bill_isCanceled) = $fields;

        // Prepare SQL statement
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
            error_log('Failed record values: ' . json_encode($fields));
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
