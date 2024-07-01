<?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csvData2'])) {
        $csvData2 = $_POST['csvData2'];
        $rows = str_getcsv($csvData2, "\n");

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

            foreach ($rows as $row) {
                $data2 = str_getcsv($row);
                if (count($data2) === 8) { // Check if the row has all 8 columns
                    // $stmt = $pdo->prepare("INSERT INTO tb_line (line_bill_number, item_sequence, item_code, item_desc, item_quantity, item_unit, item_price, line_total , line_weight) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt = $pdo->prepare("INSERT INTO tb_line (line_bill_number, item_sequence, item_code, item_desc, item_quantity, item_unit, item_price, line_total ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute($data2);
                }
            }

            // Commit the transaction
            $pdo->commit();
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