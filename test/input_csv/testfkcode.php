<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CSV Language Converter</title>
    <!-- Add Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Add DaisyUI -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss-daisyui@1.10.0/dist/full.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-r from-purple-400 via-pink-500 to-red-500">
    <div class="container mx-auto p-8 rounded-lg shadow-lg bg-white">
        <h1 class="text-4xl font-bold text-center text-gray-800 mb-8">CSV Language Converter</h1>
        <div class="flex items-center justify-center space-x-4 mb-8">
            <label for="csvFileInput" class="btn btn-primary cursor-pointer">
                <input type="file" id="csvFileInput" class="hidden">Choose File
            </label>
            <button onclick="convertCSV()" class="btn btn-secondary">Convert</button>
        </div>
        <div id="output" class="p-6 rounded-lg shadow-md bg-gray-200"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <script>
        function convertCSV() {
            const fileInput = document.getElementById('csvFileInput');
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const csvData = event.target.result;
                    Papa.parse(csvData, {
                        complete: function(results) {
                            const convertedCSV = Papa.unparse(results.data);
                            document.getElementById('output').innerText = convertedCSV;
                        }
                    });
                };
                reader.readAsText(file, 'windows-874');
            } else {
                alert('Please select a CSV file.');
            }
        }
    </script>
</body>

</html>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csvFile'])) {
    $csvFile = $_FILES['csvFile'];

    // Check if the file is a CSV file
    $fileExtension = pathinfo($csvFile['name'], PATHINFO_EXTENSION);
    if ($fileExtension === 'csv') {
        // Convert encoding from windows-874 to UTF-8
        $csvData = file_get_contents($csvFile['tmp_name']);
        $utf8Data = iconv('windows-874', 'UTF-8', $csvData);

        // Save the converted CSV data to a new file
        $outputFileName = 'converted_' . $csvFile['name'];
        file_put_contents($outputFileName, $utf8Data);

        // Database connection settings
        $dbHost = 'localhost';
        $dbName = 'wanawat_tracking';
        $dbUser = 'root';
        $dbPass = '';

        try {
            // Connect to the database
            $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare a SQL statement for insertion
            $insertQuery = "INSERT INTO tb_bill (bill_date, bill_number, bill_customer_id, bill_customer_name, bill_total, bill_isCanceled) VALUES (:bill_date, :bill_number, :bill_customer_id, :bill_customer_name, :bill_total, :bill_isCanceled)";
            $stmt = $pdo->prepare($insertQuery);

            // Parse the CSV data and bind the values to the statement
            $rows = explode("\n", $utf8Data);
            foreach ($rows as $row) {
                $data = str_getcsv($row);
                if (count($data) === 6) { // Check if the row has all 6 columns
                    $stmt->bindValue(':bill_date', $data[0]);
                    $stmt->bindValue(':bill_number', $data[1]);
                    $stmt->bindValue(':bill_customer_id', $data[2]);
                    $stmt->bindValue(':bill_customer_name', $data[3]);
                    $stmt->bindValue(':bill_total', $data[4]);
                    $stmt->bindValue(':bill_isCanceled', $data[5]);
                    $stmt->execute();
                }
            }

            // Display a success message
            echo 'The CSV data has been converted and uploaded successfully.';
        } catch (PDOException $e) {
            // Display an error message   
            echo 'Error: ' . $e->getMessage();
        }
    } else {
        // Display an error message if the file is not a CSV file
        echo 'Error: Please upload a CSV file.';
    }
} else {
    // Display an error message if the file is not uploaded
    echo 'Error: No file uploaded.';
}
?>


