<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csvData'])) {
    $csvData = $_POST['csvData'];
    $rows = str_getcsv($csvData, "\n");

    // Database connection settings
    $dbHost = 'localhost';
    $dbName = 'wanawat_tracking';
    $dbUser = 'root';
    $dbPass = '';

    try {
        // Connect to the database
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Start a transaction
        $pdo->beginTransaction();

        $duplicateRows = [];
        $importedRows = [];

        foreach ($rows as $row) {
            $data = str_getcsv($row);
            if (count($data) === 6) { // Check if the row has all 6 columns
                // Check if the data already exists in the database
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM tb_header WHERE bill_number = ?");
                $stmt->execute([$data[1]]);
                $count = $stmt->fetchColumn();

                if ($count > 0) {
                    // If data exists, add to duplicateRows
                    $duplicateRows[] = $row;
                } else {
                    // If data does not exist, insert into database
                    $stmt = $pdo->prepare("INSERT INTO tb_header (bill_date, bill_number, bill_customer_id, bill_customer_name, bill_total, bill_isCanceled) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute($data);
                    $importedRows[] = $row;
                }
            }
        }

        // Commit the transaction
        $pdo->commit();

        // Return the result as JSON
        echo json_encode([
            'duplicateRows' => $duplicateRows,
            'importedRows' => $importedRows
        ]);

    } catch (PDOException $e) {
        // Rollback the transaction on error
        $pdo->rollBack();
        // Display an error message using SweetAlert2
        echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "' . $e->getMessage() . '"
                });
              </script>';

              
    }
}
?>