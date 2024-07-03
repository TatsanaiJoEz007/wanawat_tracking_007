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
    <link rel="stylesheet" href="function/importCSV/css/style.css">
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

    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <script src="function/importCSV/js/convertCSV.js"></script>
    <script src="function/importCSV/js/importToDatabase.js"></script>
    <?php require_once "function/importCSV/importHeader.php" ?>
    <script src="function/importCSV/js/convertCSV2.js"></script>
    <script src="function/importCSV/js/importToDatabase2.js"></script>
    <?php require_once "function/importCSV/importLine.php" ?>
</body>

</html>