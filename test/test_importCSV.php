<!DOCTYPE html>
<html lang="en">

<?php session_start() ?>
<head>
    <meta charset="UTF-8">
    <title>CSV Language Converter</title>
    <!-- Add SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <!-- Add custom styles -->
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

        .instruction-box {
            background-color: #e9ecef;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .instruction-box h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }

        .instruction-box ol {
            padding-left: 20px;
        }

        .instruction-box li {
            margin-bottom: 10px;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>

<body>

    <?php require_once('function/sidebar_employee.php'); ?>

    <div class="container">
        <h1 class="heading">Import Head CSV</h1>
        <div class="instruction-box">
            <h2>วิธีการใช้งาน</h2>
            <ol>
                <li>กด <b>Choose Head CSV File</b> เพื่อเลือกไฟล์ CSV ที่ต้องการอัปโหลด</li>
                <li>กด <b>Convert</b> เพื่อแปลงไฟล์ CSV เป็น UTF-8</li>
                <li>กด <b>Import to Database</b> เพื่ออัปโหลดข้อมูลเข้าสู่ฐานข้อมูล</li>
            </ol>
        </div>
        <div class="section">
            <h3 class="sub-heading">Upload and convert your Head CSV File</h3>
            <div class="buttons">
                <label for="csvFileInput1" class="btn-custom btn-upload">
                    <i class="fas fa-file-upload"></i>
                    <span id="fileInputText1">&nbsp;Choose Head CSV File</span>
                    <input type="file" id="csvFileInput1" class="file-input">
                </label>
                <button onclick="convertCSV()" class="btn-custom">
                    <i class="fas fa-sync-alt"></i>
                    <span>&nbsp;Convert</span>
                </button>
                <button onclick="importToDatabase()" id="importBtn" class="btn-custom">
                    <i class="fas fa-database"></i>
                    <span>&nbsp;Import to Database</span>
                </button>
            </div>
            <div id="output1" class="output-container"></div>
        </div>
    </div>

    <div class="container">
        <h1 class="heading">Import Line CSV</h1>
        <div class="instruction-box">
            <h2>วิธีการใช้งาน</h2>
            <ol>
                <li>กด <b>Choose Line CSV File</b> เพื่อเลือกไฟล์ CSV ที่ต้องการอัปโหลด</li>
                <li>กด <b>Convert</b> เพื่อแปลงไฟล์ CSV เป็น UTF-8</li>
                <li>กด <b>Import to Database</b> เพื่ออัปโหลดข้อมูลเข้าสู่ฐานข้อมูล</li>
            </ol>
        </div>
        <div class="section">
            <h3 class="sub-heading">Upload and convert your Line CSV File</h3>
            <div class="buttons">
                <label for="csvFileInput2" class="btn-custom btn-upload">
                    <i class="fas fa-file-upload"></i>
                    <span id="fileInputText2">&nbsp;Choose Line CSV File</span>
                    <input type="file" id="csvFileInput2" class="file-input">
                </label>
                <button onclick="convertCSV2()" class="btn-custom">
                    <i class="fas fa-sync-alt"></i>
                    <span>&nbsp;Convert</span>
                </button>
                <button onclick="importToDatabase2()" id="importBtn" class="btn-custom">
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
        let convertedCSVData; // Store converted CSV data globally

        function convertCSV() {
            const fileInput = document.getElementById('csvFileInput1');
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const csvData = event.target.result;
                    Papa.parse(csvData, {
                        complete: function(results) {
                            const filteredData = results.data.filter(row => row.some(cell => cell.trim() !== ''));
                            convertedCSVData = Papa.unparse(filteredData);
                            document.getElementById('output1').innerText = convertedCSVData;
                            Swal.fire('Success', 'CSV file has been converted to UTF-8.', 'success');
                        }
                    });
                };
                reader.readAsText(file);
            } else {
                Swal.fire('Error', 'Please select a CSV file.', 'error');
            }
        }

        function importToDatabase() {
            if (convertedCSVData) {
                fetch('function/function_importCSV.php?action=importHead', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ data: convertedCSVData })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', 'Data has been imported to the database.', 'success');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', error.message, 'error');
                });
            } else {
                Swal.fire('Error', 'No converted CSV data available.', 'error');
            }
        }

        let convertedCSVData2; // Store converted CSV data globally for Line CSV

        function convertCSV2() {
            const fileInput = document.getElementById('csvFileInput2');
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const csvData = event.target.result;
                    Papa.parse(csvData, {
                        complete: function(results) {
                            const filteredData = results.data.filter(row => row.some(cell => cell.trim() !== ''));
                            convertedCSVData2 = Papa.unparse(filteredData);
                            document.getElementById('output2').innerText = convertedCSVData2;
                            Swal.fire('Success', 'CSV file has been converted to UTF-8.', 'success');
                        }
                    });
                };
                reader.readAsText(file);
            } else {
                Swal.fire('Error', 'Please select a CSV file.', 'error');
            }
        }

        function importToDatabase2() {
            if (convertedCSVData2) {
                fetch('function/function_importCSV.php?action=importLine', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ data: convertedCSVData2 })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Success', 'Data has been imported to the database.', 'success');
                    } else {
                        Swal.fire('Error', data.message, 'error');
                    }
                })
                .catch(error => {
                    Swal.fire('Error', error.message, 'error');
                });
            } else {
                Swal.fire('Error', 'No converted CSV data available.', 'error');
            }
        }

        document.getElementById('csvFileInput1').addEventListener('change', function() {
            const fileInputText = document.getElementById('fileInputText1');
            const fileName = this.files[0].name;
            fileInputText.textContent = fileName;
            fileInputText.classList.add('file-selected');
        });

        document.getElementById('csvFileInput2').addEventListener('change', function() {
            const fileInputText = document.getElementById('fileInputText2');
            const fileName = this.files[0].name;
            fileInputText.textContent = fileName;
            fileInputText.classList.add('file-selected');
        });

    </script>
</body>
</html>
