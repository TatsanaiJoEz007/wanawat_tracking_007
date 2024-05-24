<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CSV Language Converter</title>
    <!-- Add Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Add SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11"></link>
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <!-- Add custom styles -->
    <style>
        .rotate {
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }

        /* Hover effect for buttons */
        .btn-custom:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Click animation for buttons */
        .btn-custom:active {
            transform: scale(0.95);
        }
    </style>
</head>

<body style="background: white; color: black;">
    <div class="container p-4 rounded-lg shadow-lg bg-white mt-5">
        <h1 class="text-4xl font-bold text-center text-dark mb-8">CSV Language Converter</h1>
        <div class="d-flex justify-content-center mb-4">
            <label for="csvFileInput" class="btn btn-primary btn-custom cursor-pointer d-flex align-items-center">
                <i class="fas fa-file-upload mr-2"></i>
                <span>Choose File</span>
                <input type="file" id="csvFileInput" class="d-none">
            </label>
            <button onclick="convertCSV()" class="btn btn-secondary btn-custom ml-3 d-flex align-items-center">
                <i class="fas fa-sync-alt mr-2"></i>
                <span>Convert</span>
            </button>
            <button onclick="importToDatabase()" id="importBtn" class="btn btn-secondary btn-custom ml-3 d-flex align-items-center">
                <i class="fas fa-database mr-2"></i>
                <span>Import to Database</span>
            </button>
        </div>
        <div id="output" class="p-4 rounded-lg shadow-md bg-light text-dark"></div>
    </div>

    <!-- Add SweetAlert2 library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <script>
        let convertedCSVData; // Store converted CSV data globally

        function convertCSV() {
            const fileInput = document.getElementById('csvFileInput');
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const csvData = event.target.result;
                    Papa.parse(csvData, {
                        complete: function(results) {
                            // Filter out blank rows
                            const filteredData = results.data.filter(row => row.some(cell => cell.trim() !== ''));
                            convertedCSVData = Papa.unparse(filteredData);
                            document.getElementById('output').innerText = convertedCSVData;
                        }
                    });
                };
                reader.readAsText(file, 'windows-874');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please select a CSV file.'
                });
            }
        }

        function importToDatabase() {
            if (convertedCSVData) {
                const formData = new FormData();
                formData.append('csvData', convertedCSVData); // Pass converted CSV data

                fetch('', { // Use current file path
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(message => {
                        const output = document.getElementById('output');
                        output.innerText = '';
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: "Importing data successfully!"
                        });
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please convert a CSV file first.'
                });
            }
        }
    </script>

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

            foreach ($rows as $row) {
                $data = str_getcsv($row);
                if (count($data) === 6) { // Check if the row has all 6 columns
                    $stmt = $pdo->prepare("INSERT INTO tb_bill (bill_date, bill_number, bill_customer_id, bill_customer_name, bill_total, bill_isCanceled) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute($data);
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
</body>

</html>
