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

// ตรวจสอบสิทธิ์ในการเข้าถึงหน้าจัดการเว็บไซต์
if (!isset($permissions['manage_website']) || $permissions['manage_website'] != 1) {
    echo '<script>alert("คุณไม่มีสิทธิ์เข้าถึงหน้านี้"); location.href="../dashboard.php"</script>';
    exit;
}

function BannerImage($conn, $bannerId)
{
    $sql = "SELECT banner_img FROM tb_banner WHERE banner_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bannerId);
    $stmt->execute();
    return $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
}

// Function to convert image data to base64
function base64img($imageData)
{
    return 'data:image/jpeg;base64,' . base64_encode($imageData);
}

// Function to check if column exists in table
function columnExists($conn, $table, $column) {
    $query = $conn->query("SHOW COLUMNS FROM `{$table}` LIKE '{$column}'");
    return $query && $query->num_rows > 0;
}

// Check if banner_status column exists
$hasBannerStatus = columnExists($conn, 'tb_banner', 'banner_status');
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>Banner Management - Wanawat Tracking System</title>
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

        /* Main Content - ใช้เหมือน activity.php */
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
            .content {
                left: 0;
                width: 100%;
                padding: 15px;
            }

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

        /* Action Buttons */
        .action-section {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .btn-add-banner {
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
        }

        .btn-add-banner:hover {
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

        /* Table Styling */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            padding: 20px 15px;
            font-weight: 600;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            font-size: 1rem;
        }

        .table tbody td {
            padding: 20px 15px;
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid rgba(240, 89, 46, 0.1);
            color: #2d3748;
        }

        .table tbody tr:nth-child(even) {
            background-color: rgba(240, 89, 46, 0.05);
        }

        .table tbody tr:hover {
            background-color: rgba(240, 89, 46, 0.1);
            transition: background-color 0.3s;
        }

        /* Banner Image Styling */
        .banner-image {
            width: 100%;
            max-width: 150px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid rgba(240, 89, 46, 0.3);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .banner-image:hover {
            border-color: #F0592E;
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
        }

        /* Action Buttons in Table */
        .table-action-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            margin: 3px;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
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

        .btn-status {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .btn-status:hover {
            background: linear-gradient(135deg, #218838, #1e7e34);
            transform: translateY(-2px);
            color: white;
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .modal-header {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
            padding: 20px 25px;
        }

        .modal-title {
            font-weight: 600;
            font-size: 1.3rem;
        }

        .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-body {
            padding: 25px;
        }

        .modal-footer {
            border: none;
            padding: 20px 25px;
        }

        .form-control, .form-select {
            border: 2px solid rgba(240, 89, 46, 0.2);
            border-radius: 10px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: #F0592E;
            box-shadow: 0 0 0 0.2rem rgba(240, 89, 46, 0.25);
        }

        .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 1rem;
        }

        /* Modal Buttons */
        .btn-secondary {
            background: #6c757d;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
        }

        .btn-primary {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
            transform: translateY(-1px);
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

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #718096;
        }

        .empty-state i {
            font-size: 4rem;
            color: #adb5bd;
            margin-bottom: 20px;
            display: block;
        }

        .empty-state h3 {
            color: #2d3748;
            margin-bottom: 10px;
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

        /* Alert styling */
        .alert-warning {
            background: linear-gradient(135deg, #ffeaa7, #fdcb6e);
            border: 1px solid #e17055;
            color: #2d3748;
            border-radius: 10px;
            margin-bottom: 20px;
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

        /* File Upload Styling */
        .file-upload-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            width: 100%;
        }

        .file-upload-input {
            position: absolute;
            left: -9999px;
        }

        .file-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border: 2px dashed rgba(240, 89, 46, 0.3);
            border-radius: 10px;
            background: rgba(240, 89, 46, 0.05);
            color: #F0592E;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .file-upload-label:hover {
            border-color: #F0592E;
            background: rgba(240, 89, 46, 0.1);
        }

        .file-upload-label i {
            margin-right: 10px;
            font-size: 1.2rem;
        }

        /* Image Preview */
        .image-preview {
            margin-top: 15px;
            text-align: center;
        }

        .preview-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 10px;
            border: 2px solid rgba(240, 89, 46, 0.3);
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
            <span class="text">Banner Management</span>
        </div>
        
        <div class="container animate__fadeInUp">
            <a href="dashboard" class="back-button">
                <i class="bi bi-arrow-left"></i> กลับไปหน้า Dashboard
            </a>
            
            <div class="page-title">
                <i class="bi bi-image"></i>จัดการแบนเนอร์เว็บไซต์
            </div>

            <?php if (!$hasBannerStatus): ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>คำเตือน:</strong> ตาราง tb_banner ยังไม่มีคอลัมน์ banner_status กรุณาเพิ่มคอลัมน์นี้เพื่อใช้งานฟีเจอร์การจัดการสถานะแบนเนอร์
                <br><small>SQL: ALTER TABLE tb_banner ADD COLUMN banner_status TINYINT(1) DEFAULT 1;</small>
            </div>
            <?php endif; ?>

            <!-- Stats Section -->
            <div class="stats-section">
                <div class="row">
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-images"></i>
                            </div>
                            <div class="stat-number" id="totalBanners">
                                <?php
                                $count_query = $conn->query("SELECT COUNT(*) as total FROM tb_banner");
                                echo $count_query ? $count_query->fetch_assoc()['total'] : '0';
                                ?>
                            </div>
                            <div class="stat-title">แบนเนอร์ทั้งหมด</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-eye"></i>
                            </div>
                            <div class="stat-number">
                                <?php
                                if ($hasBannerStatus) {
                                    $active_query = $conn->query("SELECT COUNT(*) as total FROM tb_banner WHERE banner_status = 1");
                                    echo $active_query ? $active_query->fetch_assoc()['total'] : '0';
                                } else {
                                    echo 'N/A';
                                }
                                ?>
                            </div>
                            <div class="stat-title">แบนเนอร์ที่แสดง</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-calendar-plus"></i>
                            </div>
                            <div class="stat-number">
                                <?php
                                // Check if created_at column exists, if not use banner_id as fallback
                                $columns = $conn->query("SHOW COLUMNS FROM tb_banner LIKE 'created_at'");
                                if ($columns && $columns->num_rows > 0) {
                                    $today_query = $conn->query("SELECT COUNT(*) as total FROM tb_banner WHERE DATE(created_at) = CURDATE()");
                                } else {
                                    // If no created_at column, show 0 or use alternative logic
                                    $today_query = null;
                                }
                                echo ($today_query && $today_query->num_rows > 0) ? $today_query->fetch_assoc()['total'] : '0';
                                ?>
                            </div>
                            <div class="stat-title">เพิ่มวันนี้</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                        <div class="stat-card">
                            <div class="stat-icon">
                                <i class="bi bi-file-image"></i>
                            </div>
                            <div class="stat-number">
                                <?php
                                $size_query = $conn->query("SELECT SUM(LENGTH(banner_img)) as total_size FROM tb_banner WHERE banner_img IS NOT NULL");
                                $total_size = ($size_query && $size_query->num_rows > 0) ? $size_query->fetch_assoc()['total_size'] : 0;
                                echo round(($total_size ?: 0) / 1024 / 1024, 1);
                                ?>
                            </div>
                            <div class="stat-title">ขนาดรวม (MB)</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Section -->
            <div class="action-section">
                <h5 class="mb-0" style="color: #2d3748; font-weight: 600;">
                    <i class="bi bi-list-ul me-2"></i>รายการแบนเนอร์
                </h5>
                <button type="button" class="btn-add-banner" data-bs-toggle="modal" data-bs-target="#bannerModal">
                    <i class="bi bi-plus-circle"></i>เพิ่มแบนเนอร์ใหม่
                </button>
            </div>

            <!-- Table Container -->
            <div class="table-container">
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="spinner"></div>
                </div>
                
                <table class="table" id="bannerTable">
                    <thead>
                        <tr>
                            <th width="8%">#</th>
                            <th width="25%">ชื่อแบนเนอร์</th>
                            <th width="30%">รูปภาพ</th>
                            <?php if ($hasBannerStatus): ?>
                            <th width="12%">สถานะ</th>
                            <th width="25%">จัดการ</th>
                            <?php else: ?>
                            <th width="37%">จัดการ</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_SESSION['user_id'])) {
                            $i = 1;
                            $sql = "SELECT * FROM tb_banner ORDER BY banner_id DESC";
                            $query = $conn->query($sql);
                            
                            if ($query && $query->num_rows > 0) {
                                while ($row = $query->fetch_assoc()) {
                                    $bannerImg = BannerImage($conn, $row['banner_id']);
                                    $defaultImagePath = '../../view/assets/img/logo/mascot.png';
                                    
                                    if (!empty($bannerImg['banner_img'])) {
                                        $imageBase64 = base64img($bannerImg['banner_img']);
                                    } else {
                                        $imageBase64 = $defaultImagePath;
                                    }
                                    
                                    // Handle banner status safely
                                    if ($hasBannerStatus) {
                                        $status = isset($row['banner_status']) && $row['banner_status'] == 1 ? 'แสดง' : 'ซ่อน';
                                        $statusClass = isset($row['banner_status']) && $row['banner_status'] == 1 ? 'success' : 'secondary';
                                        $statusValue = isset($row['banner_status']) ? $row['banner_status'] : 0;
                                    }
                        ?>
                                    <tr>
                                        <td><strong><?php echo $i++; ?></strong></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($row['banner_name']); ?></strong>
                                            <br><small class="text-muted">ID: <?php echo $row['banner_id']; ?></small>
                                        </td>
                                        <td>
                                            <?php if (!empty($imageBase64)): ?>
                                                <img src="<?php echo $imageBase64; ?>" alt="Banner Image" class="banner-image" onclick="previewImage('<?php echo $imageBase64; ?>', '<?php echo htmlspecialchars($row['banner_name']); ?>')">
                                            <?php else: ?>
                                                <div class="empty-state">
                                                    <i class="bi bi-image"></i>
                                                    <span>ไม่มีรูปภาพ</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <?php if ($hasBannerStatus): ?>
                                        <td>
                                            <span class="badge bg-<?php echo $statusClass; ?> px-3 py-2" style="font-size: 0.9rem;">
                                                <?php echo $status; ?>
                                            </span>
                                        </td>
                                        <?php endif; ?>
                                        <td>
                                            <button type="button" class="table-action-btn btn-edit" onclick="editBanner('<?php echo $row['banner_id']; ?>', '<?php echo htmlspecialchars($row['banner_name']); ?>')">
                                                <i class="bi bi-pencil"></i> แก้ไข
                                            </button>
                                            <?php if ($hasBannerStatus): ?>
                                            <button type="button" class="table-action-btn btn-status" onclick="toggleStatus('<?php echo $row['banner_id']; ?>', '<?php echo $statusValue; ?>')">
                                                <i class="bi bi-eye<?php echo $statusValue == 1 ? '-slash' : ''; ?>"></i> 
                                                <?php echo $statusValue == 1 ? 'ซ่อน' : 'แสดง'; ?>
                                            </button>
                                            <?php endif; ?>
                                            <button type="button" class="table-action-btn btn-delete" onclick="delBanner('<?php echo $row['banner_id']; ?>')">
                                                <i class="bi bi-trash"></i> ลบ
                                            </button>
                                        </td>
                                    </tr>
                        <?php
                                }
                            } else {
                        ?>
                                <tr>
                                    <td colspan="<?php echo $hasBannerStatus ? '5' : '4'; ?>" class="empty-state">
                                        <i class="bi bi-image"></i>
                                        <h3>ยังไม่มีแบนเนอร์</h3>
                                        <p>เริ่มต้นด้วยการเพิ่มแบนเนอร์แรกของคุณ</p>
                                    </td>
                                </tr>
                        <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Banner Modal -->
    <div class="modal fade" id="bannerModal" tabindex="-1" aria-labelledby="bannerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bannerModalLabel">
                        <i class="bi bi-plus-circle me-2"></i>เพิ่มแบนเนอร์ใหม่
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bannerForm" method="post" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="banner_name" class="form-label">
                                <i class="bi bi-tag me-2"></i>ชื่อแบนเนอร์
                            </label>
                            <input type="text" class="form-control" id="banner_name" name="user_firstname" placeholder="กรอกชื่อแบนเนอร์..." required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="bi bi-image me-2"></i>รูปภาพแบนเนอร์
                            </label>
                            <div class="file-upload-wrapper">
                                <input type="file" class="file-upload-input" id="banner_image" name="user_img" accept="image/*" required>
                                <label for="banner_image" class="file-upload-label">
                                    <i class="bi bi-cloud-upload"></i>
                                    <span>คลิกเพื่อเลือกรูปภาพ หรือลากไฟล์มาวาง</span>
                                </label>
                            </div>
                            <div class="image-preview" id="imagePreview" style="display: none;">
                                <img id="previewImg" class="preview-image" alt="Preview">
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                รองรับไฟล์: JPG, PNG, GIF | ขนาดแนะนำ: 1200x400 พิกเซล
                            </small>
                        </div>

                        <?php if ($hasBannerStatus): ?>
                        <div class="mb-4">
                            <label for="banner_status" class="form-label">
                                <i class="bi bi-eye me-2"></i>สถานะการแสดง
                            </label>
                            <select class="form-select" id="banner_status" name="banner_status">
                                <option value="1">แสดง</option>
                                <option value="0">ซ่อน</option>
                            </select>
                        </div>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>ยกเลิก
                    </button>
                    <button type="button" class="btn btn-primary" onclick="submitBannerForm()">
                        <i class="bi bi-check-circle me-1"></i>บันทึกแบนเนอร์
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalTitle">ตัวอย่างแบนเนอร์</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="fullPreviewImage" class="img-fluid" style="border-radius: 10px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const hasBannerStatus = <?php echo $hasBannerStatus ? 'true' : 'false'; ?>;

        document.addEventListener('DOMContentLoaded', function() {
            // File upload preview
            const fileInput = document.getElementById('banner_image');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const uploadLabel = document.querySelector('.file-upload-label span');

            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                        uploadLabel.textContent = file.name;
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Drag and drop functionality
            const uploadWrapper = document.querySelector('.file-upload-wrapper');
            const uploadLabelEl = document.querySelector('.file-upload-label');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                uploadLabelEl.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                uploadLabelEl.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                uploadLabelEl.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                uploadLabelEl.style.borderColor = '#F0592E';
                uploadLabelEl.style.background = 'rgba(240, 89, 46, 0.15)';
            }

            function unhighlight(e) {
                uploadLabelEl.style.borderColor = 'rgba(240, 89, 46, 0.3)';
                uploadLabelEl.style.background = 'rgba(240, 89, 46, 0.05)';
            }

            uploadLabelEl.addEventListener('drop', handleDrop, false);

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

        function submitBannerForm() {
            const form = document.getElementById('bannerForm');
            const formData = new FormData(form);
            
            // Show loading
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.style.display = 'flex';
            
            fetch('function/action_uploadbanner.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loadingOverlay.style.display = 'none';
                handleResponse(data, 'เพิ่ม');
            })
            .catch(error => {
                loadingOverlay.style.display = 'none';
                handleError(error);
            });
        }

        function editBanner(id, name) {
            // TODO: Implement edit functionality
            Swal.fire({
                title: 'แก้ไขแบนเนอร์',
                text: `แก้ไขแบนเนอร์: ${name}`,
                icon: 'info',
                confirmButtonText: 'ตกลง'
            });
        }

        function toggleStatus(id, currentStatus) {
            if (!hasBannerStatus) {
                Swal.fire({
                    title: 'ไม่สามารถใช้งานได้',
                    text: 'ระบบยังไม่รองรับการจัดการสถานะแบนเนอร์',
                    icon: 'warning',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            const newStatus = currentStatus == 1 ? 0 : 1;
            const statusText = newStatus == 1 ? 'แสดง' : 'ซ่อน';
            
            Swal.fire({
                title: 'ยืนยันการเปลี่ยนสถานะ',
                text: `คุณต้องการ${statusText}แบนเนอร์นี้หรือไม่?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: `<i class="bi bi-check me-1"></i>${statusText}`,
                cancelButtonText: '<i class="bi bi-x-circle me-1"></i>ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    const loadingOverlay = document.getElementById('loadingOverlay');
                    loadingOverlay.style.display = 'flex';
                    
                    $.ajax({
                        url: 'function/action_toggle_banner_status.php',
                        type: 'POST',
                        data: {
                            id: id,
                            status: newStatus
                        },
                        success: function(response) {
                            loadingOverlay.style.display = 'none';
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: `เปลี่ยนสถานะแบนเนอร์เป็น${statusText}เรียบร้อยแล้ว`,
                                icon: 'success',
                                confirmButtonText: 'ตกลง',
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            loadingOverlay.style.display = 'none';
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถเปลี่ยนสถานะแบนเนอร์ได้',
                                icon: 'error',
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    });
                }
            });
        }

        function delBanner(id) {
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบแบนเนอร์นี้หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-trash me-1"></i>ลบแบนเนอร์',
                cancelButtonText: '<i class="bi bi-x-circle me-1"></i>ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    const loadingOverlay = document.getElementById('loadingOverlay');
                    loadingOverlay.style.display = 'flex';
                    
                    $.ajax({
                        url: 'function/action_delbanner.php',
                        type: 'POST',
                        data: {
                            id: id,
                            delBanner: 1
                        },
                        success: function(response) {
                            loadingOverlay.style.display = 'none';
                            Swal.fire({
                                title: 'สำเร็จ!',
                                text: 'ลบแบนเนอร์เรียบร้อยแล้ว',
                                icon: 'success',
                                confirmButtonText: 'ตกลง',
                                timer: 2000
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr, status, error) {
                            loadingOverlay.style.display = 'none';
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถลบแบนเนอร์ได้',
                                icon: 'error',
                                confirmButtonText: 'ตกลง'
                            });
                        }
                    });
                }
            });
        }

        function previewImage(imageSrc, bannerName) {
            document.getElementById('previewModalTitle').textContent = `ตัวอย่าง: ${bannerName}`;
            document.getElementById('fullPreviewImage').src = imageSrc;
            new bootstrap.Modal(document.getElementById('imagePreviewModal')).show();
        }

        function handleResponse(data, action) {
            console.log(data);
            if (data.success) {
                // Hide modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('bannerModal'));
                modal.hide();
                
                Swal.fire({
                    title: 'สำเร็จ!',
                    text: `แบนเนอร์ถูก${action}เรียบร้อยแล้ว`,
                    icon: 'success',
                    confirmButtonText: 'ตกลง',
                    timer: 2000
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด!',
                    text: data.message || `ไม่สามารถ${action}แบนเนอร์ได้`,
                    icon: 'error',
                    confirmButtonText: 'ตกลง'
                });
            }
        }

        function handleError(error) {
            console.error('Error:', error);
            Swal.fire({
                title: 'เกิดข้อผิดพลาด!',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        }

        // Reset form when modal closes
        document.getElementById('bannerModal').addEventListener('hidden.bs.modal', function() {
            document.getElementById('bannerForm').reset();
            document.getElementById('imagePreview').style.display = 'none';
            document.querySelector('.file-upload-label span').textContent = 'คลิกเพื่อเลือกรูปภาพ หรือลากไฟล์มาวาง';
        });
    </script>
</body>
</html>