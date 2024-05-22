<?php
header('Content-Type: application/json');

try {
    // Get the uploaded CSV file
    $csvFile = $_FILES['file']['tmp_name'];

    // Check if the file was uploaded successfully
    if (!is_uploaded_file($csvFile)) {
        throw new Exception('No file uploaded');
    }

    // Open the CSV file
    $fp = fopen($csvFile, 'r');

    // Read the CSV file line by line
    $records = [];
    while (($row = fgetcsv($fp, 0, ",")) !== FALSE) {
        $records[] = $row;
    }

    // Close the CSV file
    fclose($fp);

    // Check if there are any records
    if (empty($records)) {
        throw new Exception('No records in the CSV file');
    }

    // Connect to the database
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
        // Assign the values from the CSV row to the variables
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


<?php
header('Content-Type: application/json');

try {
    $input = file_get_contents('php://input');
    if (!$input) {
        throw new Exception('No input received');
    }

    $delimiter = ',';
    $rows = explode("\n", trim($input));

    $records = [];
    foreach ($rows as $row) {
        $records[] = str_getcsv(trim($row), $delimiter);
    }

    if (empty($records)) {
        throw new Exception('No records parsed from input');
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
    $csvString = implode("\n", array_map('implode', $records));
    $records = str_getcsv($csvString, "\n");

    foreach ($records as $record) {
        if (count($record) < 6) {
            $errors[] = "Record does not contain all required fields: " . json_encode($record);
            continue;
        }

        $bill_date = $record[0];
        $bill_number = $record[1];
        $bill_customer_id = $record[2];
        $bill_customer_name = $record[3];
        $bill_total = $record[4];
        $bill_isCanceled = $record[5];

        $sql = "INSERT INTO $tableName (bill_date, bill_number, bill_customer_id, bill_customer_name, bill_total, bill_isCanceled) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            $errors[] = 'Error preparing statement: ' . $conn->error;
            continue;
        }

        $stmt->bind_param("ssssss", $bill_date, $bill_number, $bill_customer_id, $bill_customer_name, $bill_total, $bill_isCanceled);

        if (!$stmt->execute()) {
            $errors[] = "Error executing statement: " . $stmt->error;
            error_log('Failed SQL query: ' . $sql);
            error_log('Failed record values: ' . json_encode($record));
        }

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
    error_log('Error in processCSV.php: ' . $e->getMessage());
}
?>