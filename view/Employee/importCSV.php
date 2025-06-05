<?php
// เริ่ม session ก่อนมี output ใดๆ
if (!isset($_SESSION)) {
    session_start();
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    echo '<script>location.href="../../view/login"</script>';
    exit;
}

require_once('../../view/config/connect.php');

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>นำเข้าข้อมูล CSV - Wanawat Tracking System</title>
    
    <!-- CSS Dependencies -->
    <link rel="icon" type="image/x-icon" href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    
    <style>
        /* Google Fonts Import Link */
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #F0592E 0%, #FF8A65 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Main Content - ใช้ home-section เหมือน dashboard */
        .home-section {
            position: relative;
            background: transparent;
            min-height: 100vh;
            left: 300px;
            width: calc(100% - 300px);
            transition: all 0.5s ease;
            padding: 12px;
            overflow-y: auto;
        }

        .sidebar.close ~ .home-section {
            left: 78px;
            width: calc(100% - 78px);
        }

        /* Header Content */
        .home-content {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .home-section .home-content .bx-menu,
        .home-section .home-content .text {
            color: #fff;
            font-size: 35px;
        }

        .home-section .home-content .bx-menu {
            cursor: pointer;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .home-section .home-content .bx-menu:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .home-section .home-content .text {
            font-size: 26px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Content Container */
        .content-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto 30px auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* ปุ่มย้อนกลับ */
        .back-button {
            display: inline-flex;
            align-items: center;
            padding: 10px 18px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(240, 89, 46, 0.3);
            border-radius: 10px;
            color: #F0592E;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.2);
            font-size: 0.95rem;
        }

        .back-button:hover {
            background: rgba(240, 89, 46, 0.1);
            color: #D84315;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.3);
        }

        .back-button i {
            margin-right: 8px;
            font-size: 1rem;
        }

        /* Page Title */
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #F0592E;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .page-title i {
            margin-right: 15px;
            color: #F0592E;
            font-size: 1.8rem;
        }

        /* Section Title */
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: #F0592E;
        }

        /* Instruction Box */
        .instruction-box {
            background: rgba(240, 89, 46, 0.05);
            border: 1px solid rgba(240, 89, 46, 0.2);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.1);
        }

        .instruction-box h2 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #F0592E;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .instruction-box ol {
            padding-left: 25px;
            margin: 0;
        }

        .instruction-box li {
            margin-bottom: 12px;
            font-size: 1rem;
            color: #2d3748;
            line-height: 1.5;
        }

        .instruction-box li b {
            color: #F0592E;
            font-weight: 600;
        }

        /* Section Container */
        .section {
            margin-bottom: 40px;
        }

        /* Button Container */
        .buttons {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        /* Custom Buttons */
        .btn-custom,
        .btn-custom2 {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            font-size: 1rem;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
            min-width: 180px;
            justify-content: center;
        }

        .btn-custom:hover,
        .btn-custom2:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.4);
            color: white;
        }

        .btn-custom:active,
        .btn-custom2:active {
            transform: translateY(0);
        }

        .btn-custom i,
        .btn-custom2 i {
            margin-right: 8px;
            font-size: 1rem;
        }

        /* File Input */
        .file-input {
            display: none;
        }

        .btn-upload {
            position: relative;
            overflow: hidden;
        }

        /* Output Container */
        .output-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(240, 89, 46, 0.2);
            margin-top: 20px;
            word-wrap: break-word;
            font-size: 0.9rem;
            line-height: 1.6;
            color: #2d3748;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            font-family: 'Courier New', monospace;
        }

        .output-container:empty::before {
            content: "ผลลัพธ์จะแสดงที่นี่หลังจากการแปลงไฟล์...";
            color: #718096;
            font-style: italic;
            font-family: 'Kanit', sans-serif;
        }

        /* File Selected State */
        .file-selected {
            background: linear-gradient(135deg, #28a745, #20c997) !important;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
        }

        /* Table Styling */
        .rejected-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .rejected-table th,
        .rejected-table td {
            border: 1px solid rgba(240, 89, 46, 0.2);
            padding: 12px 15px;
            text-align: left;
        }

        .rejected-table th {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .rejected-table tr:nth-child(even) {
            background-color: rgba(240, 89, 46, 0.05);
        }

        .rejected-table tr:hover {
            background-color: rgba(240, 89, 46, 0.1);
            transition: background-color 0.3s ease;
        }

        /* Loading States */
        .loading {
            position: relative;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Status Messages */
        .status-message {
            padding: 12px 20px;
            border-radius: 10px;
            margin: 15px 0;
            font-weight: 500;
        }

        .status-success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #155724;
        }

        .status-error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #721c24;
        }

        .status-warning {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .home-section {
                left: 0;
                width: 100%;
                padding: 12px 8px;
            }

            .home-content .text {
                font-size: 20px;
            }

            .home-content .bx-menu {
                font-size: 28px;
            }

            .content-container {
                padding: 20px;
                margin: 0 auto 20px auto;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 1.3rem;
            }

            .buttons {
                flex-direction: column;
                align-items: stretch;
            }

            .btn-custom,
            .btn-custom2 {
                width: 100%;
                min-width: auto;
            }

            .instruction-box {
                padding: 20px;
            }

            .output-container {
                padding: 20px;
                font-size: 0.85rem;
            }
        }

        @media (max-width: 480px) {
            .home-section {
                padding: 8px;
            }

            .home-content .text {
                font-size: 18px;
            }

            .home-content .bx-menu {
                font-size: 24px;
            }

            .content-container {
                padding: 15px;
            }

            .page-title {
                font-size: 1.3rem;
                flex-direction: column;
                text-align: center;
                gap: 8px;
            }

            .instruction-box {
                padding: 15px;
            }

            .instruction-box h2 {
                font-size: 1.1rem;
            }

            .instruction-box li {
                font-size: 0.9rem;
            }

            .btn-custom,
            .btn-custom2 {
                padding: 10px 16px;
                font-size: 0.9rem;
            }
        }

        /* Animations */
        .animate__fadeInUp {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate__fadeIn {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Hover Effects */
        .content-container {
            transition: all 0.3s ease;
        }

        .content-container:hover {
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.25);
        }

        /* Focus States */
        .btn-custom:focus,
        .btn-custom2:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(240, 89, 46, 0.3);
        }
    </style>
</head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>

    <section class="home-section">
        <!-- Header with menu button -->
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text">นำเข้าข้อมูล CSV</span>
        </div>

        <!-- Import Head CSV Section -->
        <div class="content-container animate__fadeInUp">
            <a href="dashboard" class="back-button">
                <i class="bi bi-arrow-left"></i> กลับไปหน้า Dashboard
            </a>
            
            <div class="page-title">
                <i class="bi bi-file-earmark-arrow-up"></i>
                นำเข้าข้อมูล Head CSV
            </div>
            
            <div class="instruction-box">
                <h2>
                    <i class="bi bi-info-circle"></i>
                    วิธีการใช้งาน
                </h2>
                <ol>
                    <li>กด <b>เลือกไฟล์ Head CSV</b> เพื่อเลือกไฟล์ CSV ที่ต้องการอัปโหลด</li>
                    <li>กด <b>แปลงไฟล์</b> เพื่อแปลงไฟล์ CSV เป็น UTF-8</li>
                    <li>กด <b>นำเข้าฐานข้อมูล</b> เพื่ออัปโหลดข้อมูลเข้าสู่ฐานข้อมูล</li>
                </ol>
            </div>
            
            <div class="section">
                <div class="section-title">
                    <i class="bi bi-upload"></i>
                    อัปโหลดและแปลงไฟล์ Head CSV
                </div>
                <div class="buttons">
                    <label for="csvFileInput1" class="btn-custom btn-upload">
                        <i class="fas fa-file-upload"></i>
                        <span id="fileInputText1">เลือกไฟล์ Head CSV</span>
                        <input type="file" id="csvFileInput1" class="file-input" accept=".csv">
                    </label>
                    <button onclick="convertCSV('head')" class="btn-custom">
                        <i class="fas fa-sync-alt"></i>
                        <span>แปลงไฟล์</span>
                    </button>
                    <button onclick="importToDatabase('head')" class="btn-custom">
                        <i class="fas fa-database"></i>
                        <span>นำเข้าฐานข้อมูล</span>
                    </button>
                </div>
                <div id="output1" class="output-container"></div>
            </div>
        </div>

        <!-- Import Line CSV Section -->
        <div class="content-container animate__fadeInUp">
            <div class="page-title">
                <i class="bi bi-file-earmark-spreadsheet"></i>
                นำเข้าข้อมูล Line CSV
            </div>
            
            <div class="instruction-box">
                <h2>
                    <i class="bi bi-info-circle"></i>
                    วิธีการใช้งาน
                </h2>
                <ol>
                    <li>กด <b>เลือกไฟล์ Line CSV</b> เพื่อเลือกไฟล์ CSV ที่ต้องการอัปโหลด</li>
                    <li>กด <b>แปลงไฟล์</b> เพื่อแปลงไฟล์ CSV เป็น UTF-8</li>
                    <li>กด <b>นำเข้าฐานข้อมูล</b> เพื่ออัปโหลดข้อมูลเข้าสู่ฐานข้อมูล</li>
                </ol>
            </div>
            
            <div class="section">
                <div class="section-title">
                    <i class="bi bi-upload"></i>
                    อัปโหลดและแปลงไฟล์ Line CSV
                </div>
                <div class="buttons">
                    <label for="csvFileInput2" class="btn-custom btn-upload">
                        <i class="fas fa-file-upload"></i>
                        <span id="fileInputText2">เลือกไฟล์ Line CSV</span>
                        <input type="file" id="csvFileInput2" class="file-input" accept=".csv">
                    </label>
                    <button onclick="convertCSV('line')" class="btn-custom">
                        <i class="fas fa-sync-alt"></i>
                        <span>แปลงไฟล์</span>
                    </button>
                    <button onclick="importToDatabase('line')" class="btn-custom">
                        <i class="fas fa-database"></i>
                        <span>นำเข้าฐานข้อมูล</span>
                    </button>
                </div>
                <div id="output2" class="output-container"></div>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    
    <script>
        let convertedCSVData = {};

        // File input change handlers
        document.getElementById('csvFileInput1').addEventListener('change', function(e) {
            updateFileInputText('fileInputText1', e.target.files[0]);
        });

        document.getElementById('csvFileInput2').addEventListener('change', function(e) {
            updateFileInputText('fileInputText2', e.target.files[0]);
        });

        function updateFileInputText(textElementId, file) {
            const textElement = document.getElementById(textElementId);
            const label = textElement.closest('.btn-custom');
            
            if (file) {
                textElement.textContent = file.name;
                label.classList.add('file-selected');
                
                // Show success message
                showStatusMessage(`เลือกไฟล์: ${file.name}`, 'success');
            } else {
                const isHead = textElementId.includes('1');
                textElement.textContent = isHead ? 'เลือกไฟล์ Head CSV' : 'เลือกไฟล์ Line CSV';
                label.classList.remove('file-selected');
            }
        }

        function showStatusMessage(message, type) {
            // Remove existing status messages
            document.querySelectorAll('.status-message').forEach(msg => msg.remove());
            
            const statusDiv = document.createElement('div');
            statusDiv.className = `status-message status-${type}`;
            statusDiv.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'exclamation-triangle'}"></i> ${message}`;
            
            // Insert after the buttons
            const buttonsContainer = event.target.closest('.content-container').querySelector('.buttons');
            buttonsContainer.parentNode.insertBefore(statusDiv, buttonsContainer.nextSibling);
            
            // Auto remove after 5 seconds
            setTimeout(() => {
                if (statusDiv.parentNode) {
                    statusDiv.remove();
                }
            }, 5000);
        }

        function filterDuplicates(data) {
            const uniqueData = [];
            const duplicates = [];
            const seen = new Set();

            for (const row of data) {
                // Clean up and normalize each cell in the row
                const normalizedRow = row.map(cell => cell.trim());
                const rowString = JSON.stringify(normalizedRow);

                if (!seen.has(rowString)) {
                    seen.add(rowString);
                    uniqueData.push(normalizedRow); // Push the cleaned row
                } else {
                    duplicates.push(normalizedRow);
                }
            }

            return { uniqueData, duplicates };
        }

        function convertCSV(type) {
            const fileInput = document.getElementById(type === 'head' ? 'csvFileInput1' : 'csvFileInput2');
            const outputElement = document.getElementById(type === 'head' ? 'output1' : 'output2');
            const file = fileInput.files[0];

            if (!file) {
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: 'กรุณาเลือกไฟล์ CSV ก่อน',
                    confirmButtonColor: '#F0592E'
                });
                return;
            }

            // Show loading state
            const convertButton = event.target;
            const originalText = convertButton.innerHTML;
            convertButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังแปลง...';
            convertButton.disabled = true;

            const reader = new FileReader();
            reader.onload = function(event) {
                try {
                    const csvData = event.target.result;
                    Papa.parse(csvData, {
                        complete: function(results) {
                            try {
                                const filteredData = results.data.filter(row => row.some(cell => cell.trim() !== ''));
                                const { uniqueData, duplicates } = filterDuplicates(filteredData);
                                
                                convertedCSVData[type] = Papa.unparse(uniqueData);
                                
                                // Display results
                                let output = `✅ แปลงไฟล์สำเร็จ!\n`;
                                output += `📊 จำนวนแถวทั้งหมด: ${results.data.length}\n`;
                                output += `✨ จำนวนแถวที่ไม่ซ้ำ: ${uniqueData.length}\n`;
                                output += `🔄 จำนวนแถวที่ซ้ำ: ${duplicates.length}\n\n`;
                                output += `📋 ตัวอย่างข้อมูล (5 แถวแรก):\n`;
                                output += `${Papa.unparse(uniqueData.slice(0, 5))}`;
                                
                                outputElement.textContent = output;
                                
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'แปลงไฟล์สำเร็จ!',
                                    text: `แปลงไฟล์ ${type.toUpperCase()} สำเร็จ พบข้อมูล ${uniqueData.length} แถว`,
                                    confirmButtonColor: '#F0592E'
                                });
                                
                            } catch (parseError) {
                                throw new Error('เกิดข้อผิดพลาดในการประมวลผลข้อมูล CSV');
                            }
                        },
                        error: function(error) {
                            throw new Error('เกิดข้อผิดพลาดในการอ่านไฟล์ CSV');
                        }
                    });
                } catch (error) {
                    console.error('Error:', error);
                    outputElement.textContent = `❌ เกิดข้อผิดพลาด: ${error.message}`;
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: error.message,
                        confirmButtonColor: '#F0592E'
                    });
                } finally {
                    // Reset button state
                    convertButton.innerHTML = originalText;
                    convertButton.disabled = false;
                }
            };

            reader.onerror = function() {
                outputElement.textContent = '❌ เกิดข้อผิดพลาดในการอ่านไฟล์';
                convertButton.innerHTML = originalText;
                convertButton.disabled = false;
                
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถอ่านไฟล์ได้',
                    confirmButtonColor: '#F0592E'
                });
            };

            reader.readAsText(file, 'windows-874');
        }

        function importToDatabase(type) {
            if (!convertedCSVData[type]) {
                Swal.fire({
                    icon: 'warning',
                    title: 'คำเตือน',
                    text: 'กรุณาแปลงไฟล์ก่อนนำเข้าฐานข้อมูล',
                    confirmButtonColor: '#F0592E'
                });
                return;
            }

            // Show loading state
            const importButton = event.target;
            const originalText = importButton.innerHTML;
            importButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังนำเข้า...';
            importButton.disabled = true;

            // Prepare form data
            const formData = new FormData();
            
            // Use different parameter names for different types
            if (type === 'head') {
                formData.append('csvData', convertedCSVData[type]);
            } else {
                formData.append('csvData2', convertedCSVData[type]); // importLine.php expects csvData2
            }
            
            // Add CSRF token if available
            <?php if (isset($_SESSION['csrf_token'])): ?>
            formData.append('csrf_token', '<?php echo $_SESSION['csrf_token']; ?>');
            <?php endif; ?>

            // Determine the correct PHP file
            const phpFile = type === 'head' ? 'function/importCSV/importHeader.php' : 'function/importCSV/importLine.php';

            // Make AJAX request
            fetch(phpFile, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Check if response is ok
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text(); // Get as text first to handle both JSON and HTML responses
            })
            .then(responseText => {
                console.log('Response:', responseText); // For debugging
                
                try {
                    // Try to parse as JSON first
                    const data = JSON.parse(responseText);
                    
                    if (data.status === 'success') {
                        // Success case
                        let successMessage = data.message || `ข้อมูล ${type.toUpperCase()} CSV ถูกนำเข้าฐานข้อมูลเรียบร้อยแล้ว`;
                        
                        // Add details if available
                        if (data.details && data.details.success_count) {
                            successMessage += `\n\nรายละเอียด:\n- นำเข้าสำเร็จ: ${data.details.success_count} แถว`;
                            if (data.details.error_count > 0) {
                                successMessage += `\n- ข้อผิดพลาด: ${data.details.error_count} แถว`;
                            }
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'นำเข้าข้อมูลสำเร็จ!',
                            text: successMessage,
                            confirmButtonColor: '#F0592E'
                        });

                        // Clear the output
                        const outputElement = document.getElementById(type === 'head' ? 'output1' : 'output2');
                        outputElement.textContent = '';
                        
                        // Clear the converted data
                        delete convertedCSVData[type];
                        
                        // Reset file input
                        const fileInput = document.getElementById(type === 'head' ? 'csvFileInput1' : 'csvFileInput2');
                        fileInput.value = '';
                        const textElementId = type === 'head' ? 'fileInputText1' : 'fileInputText2';
                        updateFileInputText(textElementId, null);
                        
                    } else {
                        // Error case
                        let errorMessage = data.message || 'เกิดข้อผิดพลาดในการนำเข้าข้อมูล';
                        
                        // Add error details if available
                        if (data.details && data.details.errors && data.details.errors.length > 0) {
                            errorMessage += '\n\nรายละเอียดข้อผิดพลาด:\n' + data.details.errors.slice(0, 5).join('\n');
                            if (data.details.errors.length > 5) {
                                errorMessage += `\n... และอีก ${data.details.errors.length - 5} ข้อผิดพลาด`;
                            }
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: errorMessage,
                            confirmButtonColor: '#F0592E'
                        });
                    }
                } catch (parseError) {
                    // If JSON parsing fails, check if it's HTML with success/error indicators
                    console.log('JSON parse error:', parseError);
                    console.log('Response text:', responseText);
                    
                    if (responseText.includes('success') || responseText.includes('สำเร็จ') || responseText.trim() === '') {
                        Swal.fire({
                            icon: 'success',
                            title: 'นำเข้าข้อมูลสำเร็จ!',
                            text: `ข้อมูล ${type.toUpperCase()} CSV ถูกนำเข้าฐานข้อมูลเรียบร้อยแล้ว`,
                            confirmButtonColor: '#F0592E'
                        });

                        // Clear the output
                        const outputElement = document.getElementById(type === 'head' ? 'output1' : 'output2');
                        outputElement.textContent = '';
                        
                        // Clear the converted data
                        delete convertedCSVData[type];
                        
                        // Reset file input
                        const fileInput = document.getElementById(type === 'head' ? 'csvFileInput1' : 'csvFileInput2');
                        fileInput.value = '';
                        const textElementId = type === 'head' ? 'fileInputText1' : 'fileInputText2';
                        updateFileInputText(textElementId, null);
                        
                    } else {
                        // Extract error message from HTML if possible
                        let errorMessage = 'เกิดข้อผิดพลาดในการนำเข้าข้อมูล';
                        const scriptMatch = responseText.match(/text: "(.*?)"/);
                        if (scriptMatch) {
                            errorMessage = scriptMatch[1];
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: errorMessage,
                            confirmButtonColor: '#F0592E'
                        });
                        
                        // Show raw response in output for debugging
                        const outputElement = document.getElementById(type === 'head' ? 'output1' : 'output2');
                        outputElement.textContent = `❌ เกิดข้อผิดพลาด:\n${responseText}`;
                    }
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                
                // Show detailed error message
                let errorMessage = error.message || 'ไม่สามารถนำเข้าข้อมูลได้';
                
                // Handle specific error types
                if (error.message.includes('HTTP error')) {
                    errorMessage = 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้ กรุณาตรวจสอบ path ของไฟล์ PHP';
                } else if (error.message.includes('NetworkError') || error.message.includes('fetch')) {
                    errorMessage = 'เกิดข้อผิดพลาดในการเชื่อมต่อเครือข่าย';
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: errorMessage,
                    confirmButtonColor: '#F0592E'
                });

                // Show error in output container for debugging
                const outputElement = document.getElementById(type === 'head' ? 'output1' : 'output2');
                outputElement.textContent = `❌ เกิดข้อผิดพลาด: ${errorMessage}`;
            })
            .finally(() => {
                // Reset button state
                importButton.innerHTML = originalText;
                importButton.disabled = false;
            });
        }

        // Add drag and drop functionality
        document.querySelectorAll('.btn-upload').forEach(label => {
            const fileInput = label.querySelector('input[type="file"]');
            
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                label.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                label.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                label.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                label.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
                label.style.transform = 'scale(1.02)';
            }

            function unhighlight(e) {
                label.style.background = 'linear-gradient(135deg, #F0592E, #FF8A65)';
                label.style.transform = 'scale(1)';
            }

            label.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;

                if (files.length > 0) {
                    fileInput.files = files;
                    const event = new Event('change', { bubbles: true });
                    fileInput.dispatchEvent(event);
                }
            }
        });

        // Add smooth scrolling between sections
        document.addEventListener('DOMContentLoaded', function() {
            // Animate elements on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            document.querySelectorAll('.content-container').forEach((el) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });
            
            console.log('Import CSV page loaded successfully');
        });
    </script>
</body>

</html>