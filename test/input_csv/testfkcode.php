<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CSV Language Converter</title>
    <!-- Add Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Add DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss-daisyui@1.10.0/dist/full.css" rel="stylesheet">
    <!-- Add animate.css for animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <!-- Add SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11"></link>
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

        /* Custom animation for wiping effect */
        @keyframes wipeOut {
            0% {
                opacity: 1;
                transform: translateY(0);
            }
            100% {
                opacity: 0;
                transform: translateY(-100%);
            }
        }
    </style>
</head>

<body class="bg-gradient-to-r from-purple-400 via-pink-500 to-red-500">
    <div class="container mx-auto p-8 rounded-lg shadow-lg bg-white">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">CSV Language Converter</h1>
        <div class="flex items-center justify-center space-x-4 mb-8">
            <label for="csvFileInput" class="btn btn-primary cursor-pointer bg-blue-500 hover:bg-blue-700 text-white py-2 px-4 rounded-lg">
                <input type="file" id="csvFileInput" class="hidden">Choose File
            </label>
            <button onclick="convertCSV()" class="btn btn-secondary bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg border border-gray-300 animate__animated animate__bounce">Convert</button>
            <button onclick="importToDatabase()" id="importBtn" class="btn btn-secondary bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 px-4 rounded-lg border border-gray-300">Import to Database</button>
        </div>
        <div id="output" class="p-6 rounded-lg shadow-md bg-gray-200 animate__animated animate__fadeIn"></div>
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
                            convertedCSVData = Papa.unparse(results.data);
                            document.getElementById('output').innerText = convertedCSVData;
                        }
                    });
                };
                reader.readAsText(file, 'windows-874');
                document.getElementById('output').classList.add('animate__fadeIn');
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
                        output.classList.remove('animate__fadeIn');
                        output.classList.add('animate__animated', 'animate__wipeOut'); // Apply wipeOut animation

                        setTimeout(function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: "กำลัง Import จร้าาาา"
                            });
                            output.classList.remove('animate__wipeOut');
                            output.classList.add('animate__fadeIn');
                        }, 1000); // Wait for animation to complete (1 second)
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
