<style>
  body {
    font-family: 'Kanit', sans-serif;
    background-color: #f4f4f4;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

.container {
    background: white;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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

</style>

<?php require_once('../function/sidebar.php');  ?>
<body>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<div class="container">
        <h1>Upload CSV</h1>
        <form id="uploadForm">
            <div class="file-upload-wrapper">
                <label for="fileUpload" class="file-upload-label">เลือกไฟล์ CSV</label>
                <input type="file" id="fileUpload" class="file-upload-input" accept=".csv">
                <span id="fileName" class="file-name">ไม่มีไฟล์ที่เลือก</span>
            </div>
            <button type="button" onclick="handleUpload()" class="upload-button">อัปโหลดไฟล์</button>
        </form>
        <div id="output"></div>
    </div>
    

    <script>
       document.getElementById('fileUpload').addEventListener('change', function() {
    var fileNameDisplay = document.getElementById('fileName');
    var file = this.files[0];
    fileNameDisplay.textContent = file ? file.name : 'ไม่มีไฟล์ที่เลือก';
});

function handleUpload() {
    var fileInput = document.getElementById('fileUpload');
    var file = fileInput.files[0];

    if (file) {
        var reader = new FileReader();
        reader.onload = function(e) {
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

