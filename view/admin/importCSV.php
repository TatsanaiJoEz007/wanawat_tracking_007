<?php require_once ('../config/connect.php'); ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <title>Import CSV</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <!-- เรียกใช้ Bootstrap CSS จาก CDN -->
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
</head>
<style>
    body {
        font-family: 'Kanit', sans-serif;
        background-color: #f4f4f4;
    }

    .container {
        background: white;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        width: 500px;
    }

    h1 {
        color: #333;
        text-align: center;
    }

    .file-upload-wrapper {
        margin: 20px 0;
    }

    .file-upload-input {
        display: none;
    }

    .file-upload-label {
        display: block;
        padding: 12px 20px;
        background-color: #007bff;
        color: white;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        margin-bottom: 10px;
    }

    .file-upload-label:hover {
        background-color: #0056b3;
    }

    .file-name {
        display: block;
        text-align: center;
        color: #555;
        font-size: 14px;
        margin-bottom: 20px;
    }

    .upload-button {
        display: block;
        width: 100%;
        padding: 10px 0;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    .upload-button:hover {
        background-color: #218838;
    }

    #output {
        margin-top: 20px;
        font-size: 14px;
        color: #333;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    #uploadIcon {
        display: block;
        margin: 0 auto;
        font-size: 48px;
        color: #007bff;
        cursor: pointer;
    }

    #uploadIcon:hover {
        color: #0056b3;
    }

    @media screen and (max-width: 768px) {

        /* Styles for screens smaller than 768px wide */
        .container {
            width: 90%;
        }

        .file-upload-label {
            font-size: 12px;
        }
    }
</style>

<?php require_once ('function/sidebar.php'); ?>

<body>


    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <div class="container">

        <h1>Upload CSV</h1>
        <form id="uploadForm">
            <div class="file-upload-wrapper">
                <label for="fileUpload" class="file-upload-label">
                    <i class="bi bi-cloud-upload" id="uploadIcon"></i> เลือกไฟล์ CSV
                </label>
                <input type="file" id="fileUpload" class="file-upload-input" accept=".csv">
                <span id="fileName" class="file-name">ไม่มีไฟล์ที่เลือก</span>
            </div>
            <button type="button" onclick="handleUpload()" class="upload-button">อัปโหลดไฟล์</button>
        </form>
        <div id="output"></div>
    </div>


    <script>
        document.getElementById('fileUpload').addEventListener('change', function () {
            var fileNameDisplay = document.getElementById('fileName');
            var file = this.files[0];
            fileNameDisplay.textContent = file ? file.name : 'ไม่มีไฟล์ที่เลือก';
        });

        function handleUpload() {
            var fileInput = document.getElementById('fileUpload');
            var file = fileInput.files[0];

            if (file) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var output = document.getElementById('output');
                    output.textContent = e.target.result;
                };
                reader.readAsText(file);
            } else {
                swal("โปรดเลือกไฟล์ CSV", "", "error");
            }
        }

    </script>

</body>

</html>