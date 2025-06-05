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

require_once('../config/connect.php');

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];

// ตรวจสอบสิทธิ์ในการเข้าถึงหน้าจัดการ FAQ
if (!isset($permissions['manage_website']) || $permissions['manage_website'] != 1) {
    echo '<script>alert("คุณไม่มีสิทธิ์เข้าถึงหน้านี้"); location.href="../dashboard.php"</script>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>FAQ Management - Wanawat Tracking System</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS Dependencies -->
    <link rel="icon" type="image/x-icon" href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
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

        /* Main Content */
        .content {
            position: relative;
            background: transparent;
            min-height: 100vh;
            left: 300px;
            width: calc(100% - 300px);
            transition: all 0.5s ease;
            padding: 20px;
            overflow-y: auto;
        }

        .sidebar.close ~ .content {
            left: 78px;
            width: calc(100% - 78px);
        }

        /* Mobile responsive content */
        @media screen and (max-width: 768px) {
            .content {
                left: 0;
                width: 100%;
                padding: 15px;
            }
        }

        /* Header Content */
        .home-content {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .home-content .bx-menu,
        .home-content .text {
            color: #fff;
            font-size: 35px;
        }

        .home-content .bx-menu {
            cursor: pointer;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .home-content .bx-menu:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .home-content .text {
            font-size: 26px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        @media (max-width: 768px) {
            .home-content .text {
                font-size: 20px;
            }

            .home-content .bx-menu {
                font-size: 28px;
            }
        }

        .container {
            width: 100%;
            max-width: 1400px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #F0592E;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .page-title i {
            margin-right: 10px;
            color: #F0592E;
        }

        /* ปุ่มย้อนกลับ */
        .back-button {
            display: inline-flex;
            align-items: center;
            padding: 8px 15px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(240, 89, 46, 0.3);
            border-radius: 8px;
            color: #F0592E;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.2);
        }

        .back-button:hover {
            background: rgba(240, 89, 46, 0.1);
            color: #D84315;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.3);
        }

        .back-button i {
            margin-right: 5px;
        }

        /* Action Section */
        .action-section {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn-add-faq {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            border: none;
            color: white;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
            font-size: 1rem;
            cursor: pointer;
        }

        .btn-add-faq:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.4);
            color: white;
        }

        /* Stats Cards */
        .stats-section {
            margin-bottom: 25px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #F0592E;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .stat-title {
            font-size: 1rem;
            color: #718096;
            font-weight: 500;
        }

        /* FAQ Cards */
        .faq-container {
            display: grid;
            gap: 20px;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        }

        .faq-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            border-left: 4px solid #F0592E;
        }

        .faq-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
            border-left-color: #D84315;
        }

        .faq-header {
            font-size: 1.3rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 15px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .faq-content {
            color: #718096;
            margin-bottom: 15px;
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .faq-meta {
            font-size: 0.85rem;
            color: #a0aec0;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .faq-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            flex: 1;
            justify-content: center;
            min-width: 80px;
        }

        .btn-edit {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #e0a800, #d39e00);
            transform: translateY(-2px);
            color: #212529;
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .btn-delete:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-2px);
            color: white;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            color: #718096;
            grid-column: 1 / -1;
        }

        .empty-state i {
            font-size: 5rem;
            color: #adb5bd;
            margin-bottom: 20px;
            display: block;
        }

        .empty-state h3 {
            color: #2d3748;
            margin-bottom: 10px;
            font-size: 1.5rem;
        }

        .empty-state p {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        /* Loading States */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(5px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            display: none;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(240, 89, 46, 0.2);
            border-top: 4px solid #F0592E;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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

        /* Custom SweetAlert2 styling */
        .swal2-popup {
            border-radius: 15px !important;
            font-family: 'Kanit', sans-serif !important;
        }

        .swal2-title {
            color: #2d3748 !important;
            font-weight: 600 !important;
        }

        .swal2-input, .swal2-textarea {
            border: 2px solid rgba(240, 89, 46, 0.2) !important;
            border-radius: 10px !important;
            font-family: 'Kanit', sans-serif !important;
            font-size: 1rem !important;
        }

        .swal2-input:focus, .swal2-textarea:focus {
            border-color: #F0592E !important;
            box-shadow: 0 0 0 0.2rem rgba(240, 89, 46, 0.25) !important;
        }

        .swal2-confirm {
            background: linear-gradient(135deg, #F0592E, #FF8A65) !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            padding: 10px 20px !important;
        }

        .swal2-cancel {
            background: #6c757d !important;
            border: none !important;
            border-radius: 8px !important;
            font-weight: 500 !important;
            padding: 10px 20px !important;
        }

        .swal2-textarea {
            resize: vertical !important;
            min-height: 120px !important;
            max-height: 300px !important;
        }

        /* Custom scrollbar */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .faq-container {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .faq-card {
                padding: 20px;
            }
            
            .faq-actions {
                flex-direction: column;
            }
            
            .action-btn {
                min-width: auto;
            }
            
            .action-section {
                flex-direction: column;
                align-items: stretch;
                gap: 15px;
            }
            
            .stats-section .row {
                row-gap: 15px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
                margin: 10px;
                width: calc(100% - 20px);
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .stat-card {
                padding: 15px;
            }
            
            .stat-icon {
                font-size: 2rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
        }

        /* Mobile sidebar overlay */
        @media screen and (max-width: 768px) {
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 99;
                display: none;
                opacity: 0;
                transition: opacity 0.3s ease;
            }
            
            .sidebar.active + .sidebar-overlay {
                display: block;
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <!-- Include Sidebar -->
    <?php include('function/sidebar.php'); ?>

    <!-- Main Content -->
    <div class="content">
        <!-- Header with menu button -->
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text">FAQ Management</span>
        </div>
        
        <div class="container animate__fadeInUp">
            <a href="dashboard" class="back-button">
                <i class="bi bi-arrow-left"></i> กลับไปหน้า Dashboard
            </a>
            
            <div class="page-title">
                <i class="bi bi-question-circle"></i>จัดการคำถามที่พบบ่อย (FAQ)
            </div>

            <!-- Stats Section -->
            <div class="stats-section">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-question-circle"></i>
                            </div>
                            <div class="stat-number">
                                <?php
                                $count_query = $conn->query("SELECT COUNT(*) as total FROM tb_freq");
                                echo $count_query ? $count_query->fetch_assoc()['total'] : '0';
                                ?>
                            </div>
                            <div class="stat-title">FAQ ทั้งหมด</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-calendar-plus"></i>
                            </div>
                            <div class="stat-number">
                                <?php
                                // Check if created_at column exists
                                $columns = $conn->query("SHOW COLUMNS FROM tb_freq LIKE 'freq_create_at'");
                                if ($columns && $columns->num_rows > 0) {
                                    $today_query = $conn->query("SELECT COUNT(*) as total FROM tb_freq WHERE DATE(freq_create_at) = CURDATE()");
                                    echo ($today_query && $today_query->num_rows > 0) ? $today_query->fetch_assoc()['total'] : '0';
                                } else {
                                    echo '0';
                                }
                                ?>
                            </div>
                            <div class="stat-title">เพิ่มวันนี้</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="stat-number">
                                <?php
                                $week_query = $conn->query("SELECT COUNT(*) as total FROM tb_freq WHERE freq_create_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
                                echo ($week_query && $week_query->num_rows > 0) ? $week_query->fetch_assoc()['total'] : '0';
                                ?>
                            </div>
                            <div class="stat-title">เพิ่มสัปดาห์นี้</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-graph-up"></i>
                            </div>
                            <div class="stat-number">
                                <?php
                                $month_query = $conn->query("SELECT COUNT(*) as total FROM tb_freq WHERE MONTH(freq_create_at) = MONTH(NOW()) AND YEAR(freq_create_at) = YEAR(NOW())");
                                echo ($month_query && $month_query->num_rows > 0) ? $month_query->fetch_assoc()['total'] : '0';
                                ?>
                            </div>
                            <div class="stat-title">เพิ่มเดือนนี้</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Section -->
            <div class="action-section">
                <h5 class="mb-0" style="color: #2d3748; font-weight: 600;">
                    <i class="bi bi-list-ul me-2"></i>รายการคำถามที่พบบ่อย
                </h5>
                <button type="button" class="btn-add-faq" id="new-faq-btn">
                    <i class="bi bi-plus-circle"></i>เพิ่ม FAQ ใหม่
                </button>
            </div>

            <!-- FAQ Container -->
            <div class="faq-container" id="faqContainer">
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="spinner"></div>
                </div>
                
                <?php
                // Fetch all FAQs from the database
                $query = "SELECT * FROM tb_freq ORDER BY freq_id DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        // Format date
                        $created_date = date('d/m/Y H:i', strtotime($row['freq_create_at']));
                ?>
                        <div class="faq-card">
                            <h3 class="faq-header"><?php echo htmlspecialchars($row['freq_header']); ?></h3>
                            <p class="faq-content"><?php echo nl2br(htmlspecialchars($row['freq_content'])); ?></p>
                            <div class="faq-meta">
                                <i class="bi bi-calendar"></i>
                                <span>สร้างเมื่อ: <?php echo $created_date; ?></span>
                            </div>
                            <div class="faq-actions">
                                <button class="action-btn btn-edit edit-faq-btn" data-id="<?php echo htmlspecialchars($row['freq_id']); ?>">
                                    <i class="bi bi-pencil"></i> แก้ไข
                                </button>
                                <button class="action-btn btn-delete delete-faq-btn" data-id="<?php echo htmlspecialchars($row['freq_id']); ?>">
                                    <i class="bi bi-trash"></i> ลบ
                                </button>
                            </div>
                        </div>
                <?php
                    }
                } else {
                ?>
                    <div class="empty-state">
                        <i class="bi bi-question-circle"></i>
                        <h3>ยังไม่มีคำถามที่พบบ่อย</h3>
                        <p>เริ่มต้นด้วยการเพิ่ม FAQ แรกของคุณ</p>
                        <button type="button" class="btn-add-faq" onclick="document.getElementById('new-faq-btn').click()">
                            <i class="bi bi-plus-circle"></i>เพิ่ม FAQ ใหม่
                        </button>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // FAQ Management JavaScript only
        document.addEventListener("DOMContentLoaded", function() {
            console.log("FAQ Management page loaded"); // Debug log
        });

        // Add new FAQ
        $('#new-faq-btn').click(function() {
            Swal.fire({
                title: '<i class="bi bi-plus-circle me-2"></i>เพิ่ม FAQ ใหม่',
                html: '<input id="header" class="swal2-input" placeholder="หัวข้อคำถาม">' +
                    '<textarea id="content" class="swal2-textarea" placeholder="คำตอบหรือรายละเอียด"></textarea>',
                showCancelButton: true,
                confirmButtonText: '<i class="bi bi-check-circle me-1"></i>เพิ่ม FAQ',
                cancelButtonText: '<i class="bi bi-x-circle me-1"></i>ยกเลิก',
                showLoaderOnConfirm: true,
                allowOutsideClick: false,
                preConfirm: () => {
                    var header = $('#header').val().trim();
                    var content = $('#content').val().trim();

                    if (!header) {
                        Swal.showValidationMessage('กรุณากรอกหัวข้อคำถาม');
                        return false;
                    }

                    if (!content) {
                        Swal.showValidationMessage('กรุณากรอกคำตอบหรือรายละเอียด');
                        return false;
                    }

                    return $.ajax({
                        url: 'function/new_faq.php',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            freq_header: header,
                            freq_content: content
                        }
                    }).done(function(response) {
                        if (response.status === "success") {
                            Swal.fire({
                                title: '<i class="bi bi-check-circle text-success me-2"></i>สำเร็จ!',
                                text: response.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: '<i class="bi bi-exclamation-triangle text-warning me-2"></i>เกิดข้อผิดพลาด',
                                text: response.message,
                                icon: 'error',
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    }).fail(function(jqXHR, textStatus, errorThrown) {
                        Swal.fire({
                            title: '<i class="bi bi-exclamation-triangle text-danger me-2"></i>เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถเพิ่ม FAQ ได้ กรุณาลองใหม่อีกครั้ง',
                            icon: 'error',
                            confirmButtonText: 'ตกลง'
                        });
                    });
                }
            });
        });

        // Edit FAQ
        $(document).on('click', '.edit-faq-btn', function() {
            var freq_id = $(this).data('id');
            
            // Show loading
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.style.display = 'flex';

            $.ajax({
                url: 'function/getfreq.php?id=' + freq_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    loadingOverlay.style.display = 'none';
                    
                    if (data.error) {
                        Swal.fire({
                            title: '<i class="bi bi-exclamation-triangle text-warning me-2"></i>เกิดข้อผิดพลาด',
                            text: data.error,
                            icon: 'error',
                            confirmButtonText: 'ตกลง'
                        });
                    } else {
                        Swal.fire({
                            title: '<i class="bi bi-pencil me-2"></i>แก้ไข FAQ',
                            html: '<input id="editedHeader" class="swal2-input" placeholder="หัวข้อคำถาม" value="' + (data.freq_header || '') + '">' +
                                '<textarea id="editedContent" class="swal2-textarea" placeholder="คำตอบหรือรายละเอียด">' + (data.freq_content || '') + '</textarea>',
                            showCancelButton: true,
                            confirmButtonText: '<i class="bi bi-check-circle me-1"></i>บันทึก',
                            cancelButtonText: '<i class="bi bi-x-circle me-1"></i>ยกเลิก',
                            showLoaderOnConfirm: true,
                            allowOutsideClick: false,
                            preConfirm: () => {
                                var header = $('#editedHeader').val().trim();
                                var content = $('#editedContent').val().trim();

                                if (!header) {
                                    Swal.showValidationMessage('กรุณากรอกหัวข้อคำถาม');
                                    return false;
                                }

                                if (!content) {
                                    Swal.showValidationMessage('กรุณากรอกคำตอบหรือรายละเอียด');
                                    return false;
                                }

                                return $.ajax({
                                    url: 'function/edit_faq.php?id=' + freq_id,
                                    type: 'POST',
                                    data: {
                                        header: header,
                                        content: content
                                    }
                                }).done(function(response) {
                                    Swal.fire({
                                        title: '<i class="bi bi-check-circle text-success me-2"></i>สำเร็จ!',
                                        text: 'แก้ไข FAQ เรียบร้อยแล้ว',
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                }).fail(function() {
                                    Swal.fire({
                                        title: '<i class="bi bi-exclamation-triangle text-danger me-2"></i>เกิดข้อผิดพลาด',
                                        text: 'ไม่สามารถแก้ไข FAQ ได้',
                                        icon: 'error',
                                        confirmButtonText: 'ตกลง'
                                    });
                                });
                            }
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    loadingOverlay.style.display = 'none';
                    console.log('Error:', jqXHR.responseText);
                    Swal.fire({
                        title: '<i class="bi bi-exclamation-triangle text-danger me-2"></i>เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถดึงข้อมูล FAQ ได้',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                }
            });
        });

        // Delete FAQ
        $(document).on('click', '.delete-faq-btn', function() {
            var freq_id = $(this).data('id');
            Swal.fire({
                title: '<i class="bi bi-exclamation-triangle text-warning me-2"></i>ยืนยันการลบ',
                text: "คุณแน่ใจหรือไม่ว่าต้องการลบ FAQ นี้? หากลบแล้วจะไม่สามารถเรียกคืนได้อีก!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash me-1"></i>ใช่, ลบเลย!',
                cancelButtonText: '<i class="bi bi-x-circle me-1"></i>ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    const loadingOverlay = document.getElementById('loadingOverlay');
                    loadingOverlay.style.display = 'flex';
                    
                    $.ajax({
                        url: 'function/delete_faq.php?id=' + freq_id,
                        type: 'POST'
                    }).done(function(response) {
                        loadingOverlay.style.display = 'none';
                        Swal.fire({
                            title: '<i class="bi bi-check-circle text-success me-2"></i>ลบสำเร็จ!',
                            text: 'ลบ FAQ เรียบร้อยแล้ว',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    }).fail(function() {
                        loadingOverlay.style.display = 'none';
                        Swal.fire({
                            title: '<i class="bi bi-exclamation-triangle text-danger me-2"></i>เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถลบ FAQ ได้',
                            icon: 'error',
                            confirmButtonText: 'ตกลง'
                        });
                    });
                }
            });
        });
    </script>
</body>
</html>