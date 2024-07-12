<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>CSV Language Converter</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .heading {
            text-align: center;
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 30px;
            color: #333;
        }

        .sub-heading {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
            color: #555;
        }

        .section {
            margin-bottom: 50px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .btn-custom,
        .btn-custom2 {
            background-color: #F0592E;
            color: #fff;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            margin: 0 15px;
            font-size: 18px;
            text-decoration: none;
        }

        .btn-custom2:hover {
            background-color: #ee4616;
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-custom:hover {
            background-color: #ee4616;
            transform: scale(1.05);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .file-input {
            display: none;
        }

        .output-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            word-wrap: break-word;
            font-size: 16px;
            line-height: 1.6;
            color: #555;
            max-height: 300px;
            overflow-y: auto;
        }

        .fa {
            margin-right: 8px;
        }

        .d-none {
            display: none;
        }

        .ml-3 {
            margin-left: 1rem;
        }

        .file-selected {
            background-color: #F0592E;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 12px;
            /* Adjust width for vertical scrollbar */
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            /* Color for scrollbar thumb */
            border-radius: 10px;
            /* Rounded corners for scrollbar thumb */
        }

        /* Container Styling */
        .home-section {
            max-height: 100vh;
            /* Adjust height as needed */
            overflow-y: auto;
            /* Allow vertical scroll */
            overflow-x: hidden;
            /* Prevent horizontal scroll */
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .rejected-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .rejected-table th,
        .rejected-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .rejected-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .rejected-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .rejected-table tr:hover {
            background-color: #ddd;
        }
    </style>
</head>

<body>

    <?php require_once('function/sidebar_employee.php'); ?>

    <div class="container">
        <h1 class="heading">Import Head CSV</h1>
        <div class="section">
            <h3 class="sub-heading">Upload and convert your Head CSV File</h3>
            <div class="buttons">
                <label for="csvFileInput1" class="btn-custom btn-upload">
                    <i class="fas fa-file-upload"></i>
                    <span id="fileInputText1">&nbsp;Choose Head CSV File</span>
                    <input type="file" id="csvFileInput1" class="file-input">
                </label>
                <button onclick="convertCSV('head')" class="btn-custom">
                    <i class="fas fa-sync-alt"></i>
                    <span>&nbsp;Convert</span>
                </button>
                <button onclick="importToDatabase('head')" class="btn-custom">
                    <i class="fas fa-database"></i>
                    <span>&nbsp;Import to Database</span>
                </button>
            </div>
            <div id="output1" class="output-container"></div>
        </div>
    </div>

    <div class="container">
        <h1 class="heading">Import Line CSV</h1>
        <div class="section">
            <h3 class="sub-heading">Upload and convert your Line CSV File</h3>
            <div class="buttons">
                <label for="csvFileInput2" class="btn-custom btn-upload">
                    <i class="fas fa-file-upload"></i>
                    <span id="fileInputText2">&nbsp;Choose Line CSV File</span>
                    <input type="file" id="csvFileInput2" class="file-input">
                </label>
                <button onclick="convertCSV('line')" class="btn-custom">
                    <i class="fas fa-sync-alt"></i>
                    <span>&nbsp;Convert</span>
                </button>
                <button onclick="importToDatabase('line')" class="btn-custom">
                    <i class="fas fa-database"></i>
                    <span>&nbsp;Import to Database</span>
                </button>
            </div>
            <div id="output2" class="output-container"></div>
        </div>
    </div>

    <!-- Add SweetAlert2 library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <script>
        let convertedCSVData = {};

        function filterDuplicates(data) {
            const uniqueData = [];
            const seen = new Set();

            for (const row of data) {
                const rowString = JSON.stringify(row);
                if (!seen.has(rowString)) {
                    seen.add(rowString);
                    uniqueData.push(row);
                }
            }

            return uniqueData;
        }

        function convertCSV(type) {
            const fileInput = document.getElementById(type === 'head' ? 'csvFileInput1' : 'csvFileInput2');
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const csvData = event.target.result;
                    Papa.parse(csvData, {
                        complete: function(results) {
                            const filteredData = results.data.filter(row => row.some(cell => cell.trim() !== ''));
                            const uniqueData = filterDuplicates(filteredData);
                            convertedCSVData[type] = Papa.unparse(uniqueData);
                            document.getElementById(type === 'head' ? 'output1' : 'output2').innerText = convertedCSVData[type];
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

        function importToDatabase(type) {
            if (convertedCSVData[type]) {
                const formData = new FormData();
                formData.append('csvType', type);
                formData.append('csvData', convertedCSVData[type]);

                fetch('../../API/import.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: data.message
                            });

                            if (data.rejected.length > 0) {
                                let rejectedText = '<table class="rejected-table"><tr><th>#</th><th>Rejected Row</th></tr>';
                                data.rejected.forEach((row, index) => {
                                    rejectedText += `<tr><td>${index + 1}</td><td>${row.join(', ')}</td></tr>`;
                                });
                                rejectedText += '</table>';

                                Swal.fire({
                                    icon: 'info',
                                    title: 'Rejected Data',
                                    html: rejectedText,
                                    width: 'auto'
                                });
                            }

                            document.getElementById(type === 'head' ? 'output1' : 'output2').innerText = '';
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message
                        });
                    });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please convert a CSV file first.'
                });
            }
        }
    </script>
</body>

</html>