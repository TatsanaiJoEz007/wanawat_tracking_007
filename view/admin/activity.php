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

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];

// ตรวจสอบสิทธิ์ในการเข้าถึงหน้า activity logs
if (!isset($permissions['manage_logs']) || $permissions['manage_logs'] != 1) {
    echo '<script>alert("คุณไม่มีสิทธิ์เข้าถึงหน้านี้"); location.href="dashboard.php"</script>';
    exit;
}

// Get action filter and search term
$action_filter = isset($_GET['action']) ? $_GET['action'] : 'all';
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Determine sorting order
$sort_order = isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'asc' : 'desc';
$new_sort_order = $sort_order == 'asc' ? 'desc' : 'asc';
$icon = $sort_order == 'asc' ? 'bi-sort-up' : 'bi-sort-down';
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>Activity Logs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSS Dependencies -->
    <link rel="icon" type="image/x-icon" href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
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
            max-width: 1300px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #F0592E;
            margin-bottom: 20px;
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

        /* Action Tabs */
        .action-tabs {
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            background: rgba(240, 89, 46, 0.05);
            padding: 20px;
            border-radius: 15px;
            border: 1px solid rgba(240, 89, 46, 0.2);
        }

        .tab-button {
            padding: 12px 24px;
            border: 2px solid rgba(240, 89, 46, 0.3);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            color: #F0592E;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .tab-button:hover {
            background: rgba(240, 89, 46, 0.1);
            color: #D84315;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(240, 89, 46, 0.2);
        }

        .tab-button.active {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border-color: #F0592E;
            box-shadow: 0 5px 15px rgba(240, 89, 46, 0.4);
        }

        .tab-button i {
            font-size: 1.1rem;
        }

        /* ส่วนค้นหา */
        .search-section {
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(240, 89, 46, 0.2);
            box-shadow: 0 8px 32px rgba(240, 89, 46, 0.1);
        }

        .search-header {
            font-weight: 600;
            color: #F0592E;
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .search-header i {
            margin-right: 8px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .search-input {
            flex: 1;
            padding: 12px 16px;
            border: 2px solid rgba(240, 89, 46, 0.2);
            border-radius: 10px;
            outline: none;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .search-input:focus {
            border-color: #F0592E;
            box-shadow: 0 0 0 0.2rem rgba(240, 89, 46, 0.25);
            background: rgba(255, 255, 255, 1);
        }

        .search-btn {
            padding: 12px 24px;
            border: none;
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
        }

        .search-btn:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.4);
        }

        .clear-btn {
            padding: 12px 20px;
            border: 2px solid #FF8A65;
            background: rgba(255, 138, 101, 0.1);
            color: #F0592E;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
            text-decoration: none;
        }

        .clear-btn:hover {
            background: rgba(255, 138, 101, 0.2);
            color: #D84315;
            text-decoration: none;
            transform: translateY(-2px);
        }

        /* Table Styling */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(240, 89, 46, 0.1);
            overflow: hidden;
            border: 1px solid rgba(240, 89, 46, 0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            padding: 15px 12px;
            white-space: nowrap;
            font-weight: 600;
            position: relative;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .table tbody tr:nth-child(even) {
            background-color: rgba(240, 89, 46, 0.05);
        }

        .table tbody tr:hover {
            background-color: rgba(240, 89, 46, 0.1);
            transition: background-color 0.3s;
        }

        .table td {
            padding: 12px;
            vertical-align: middle;
            color: #2d3748;
        }

        /* Sortable column styling */
        .sortable-column a {
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .sortable-column a:hover {
            color: rgba(255, 255, 255, 0.8);
            text-shadow: 0 0 8px rgba(255, 255, 255, 0.5);
        }

        .sortable-column i {
            font-size: 0.9rem;
        }

        /* Status badges */
        .action-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            text-transform: uppercase;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .action-create {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: #fff;
        }

        .action-update {
            background: linear-gradient(135deg, #4299e1, #3182ce);
            color: #fff;
        }

        .action-delete {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: #fff;
        }

        .action-login {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: #fff;
        }

        .action-logout {
            background: linear-gradient(135deg, #718096, #4a5568);
            color: #fff;
        }

        .action-default {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
            color: #fff;
        }

        /* Entity badges */
        .entity-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.75rem;
            font-weight: 500;
            background: rgba(240, 89, 46, 0.1);
            color: #F0592E;
            border: 1px solid rgba(240, 89, 46, 0.2);
        }

        /* No data styling */
        .no-data {
            padding: 40px 20px;
            text-align: center;
            font-size: 1.1rem;
            color: #718096;
        }

        /* Responsive table */
        .table-wrapper {
            position: relative;
            overflow-x: auto;
        }

        /* Loading overlay */
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

        /* Additional info styling */
        .additional-info {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
            color: #2d3748;
        }

        .search-result-info {
            margin-bottom: 15px;
            padding: 12px 16px;
            background: rgba(240, 89, 46, 0.1);
            border-radius: 10px;
            border-left: 4px solid #F0592E;
            display: none;
            color: #2d3748;
            backdrop-filter: blur(10px);
        }

        .search-result-info.show {
            display: block;
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

        /* Responsive tabs */
        @media (max-width: 768px) {
            .action-tabs {
                flex-direction: column;
                align-items: stretch;
            }

            .tab-button {
                justify-content: center;
                text-align: center;
            }

            .search-form {
                flex-direction: column;
                align-items: stretch;
            }
        }

        /* Pagination styles */
        .pagination-link {
            padding: 10px 16px;
            border: 2px solid rgba(240, 89, 46, 0.3);
            border-radius: 10px;
            color: #F0592E;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            min-width: 45px;
            text-align: center;
            display: inline-block;
        }

        .pagination-link:hover {
            background: rgba(240, 89, 46, 0.1);
            color: #D84315;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(240, 89, 46, 0.2);
        }

        .pagination-link.active {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border-color: #F0592E;
            box-shadow: 0 5px 15px rgba(240, 89, 46, 0.4);
        }

        .pagination-info {
            margin: 15px 0;
            text-align: center;
            color: #6c757d;
            font-size: 0.9rem;
            padding: 10px;
            background: rgba(240, 89, 46, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(240, 89, 46, 0.1);
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .pagination-ellipsis {
            padding: 10px 16px;
            border: none;
            background: transparent;
            cursor: default;
            color: #6c757d;
        }
    </style>

</head>

<body>
    <?php include('function/sidebar.php'); ?>
    
    <div class="content">
        <!-- Header with menu button -->
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text">Admin Activity Log</span>
        </div>
        
        <div class="container">
            <a href="dashboard.php" class="back-button">
                <i class="bi bi-arrow-left"></i> กลับไปหน้า Dashboard
            </a>
            
            <div class="page-title">
                <i class="bi bi-activity"></i>Admin Activity Log
            </div>

            <?php
            // Get action name for display
            $action_name = '';
            switch ($action_filter) {
                case 'create':
                    $action_name = 'สร้าง';
                    break;
                case 'update':
                    $action_name = 'แก้ไข';
                    break;
                case 'delete':
                    $action_name = 'ลบ';
                    break;
                case 'login':
                    $action_name = 'เข้าสู่ระบบ';
                    break;
                case 'logout':
                    $action_name = 'ออกจากระบบ';
                    break;
                default:
                    $action_name = 'ทั้งหมด';
                    break;
            }
            ?>

            <!-- Action Tabs -->
            <div class="action-tabs">
                <a href="?action=all<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>" 
                   class="tab-button <?php echo $action_filter == 'all' ? 'active' : ''; ?>">
                    <i class="bi bi-list-ul"></i>
                    ทั้งหมด
                </a>
                <a href="?action=create<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>" 
                   class="tab-button <?php echo $action_filter == 'create' ? 'active' : ''; ?>">
                    <i class="bi bi-plus-circle"></i>
                    สร้าง
                </a>
                <a href="?action=update<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>" 
                   class="tab-button <?php echo $action_filter == 'update' ? 'active' : ''; ?>">
                    <i class="bi bi-pencil-square"></i>
                    แก้ไข
                </a>
                <a href="?action=delete<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>" 
                   class="tab-button <?php echo $action_filter == 'delete' ? 'active' : ''; ?>">
                    <i class="bi bi-trash"></i>
                    ลบ
                </a>
                <a href="?action=login<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>" 
                   class="tab-button <?php echo $action_filter == 'login' ? 'active' : ''; ?>">
                    <i class="bi bi-box-arrow-in-right"></i>
                    เข้าสู่ระบบ
                </a>
                <a href="?action=logout<?php echo $search ? '&search=' . urlencode($search) : ''; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>" 
                   class="tab-button <?php echo $action_filter == 'logout' ? 'active' : ''; ?>">
                    <i class="bi bi-box-arrow-right"></i>
                    ออกจากระบบ
                </a>
            </div>

            <!-- Search Section -->
            <div class="search-section">
                <div class="search-header">
                    <i class="bi bi-search"></i> ค้นหาข้อมูล Activity Log - <?php echo $action_name; ?>
                </div>
                
                <form method="GET" action="" class="search-form">
                    <input type="hidden" name="action" value="<?php echo htmlspecialchars($action_filter); ?>">
                    <?php if (isset($_GET['sort'])): ?>
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($_GET['sort']); ?>">
                    <?php endif; ?>
                    <input class="search-input" type="text" name="search" placeholder="ค้นหา รหัสผู้ใช้, ชื่อ-นามสกุล, การกระทำ, รูปแบบ, หรือข้อมูลเพิ่มเติม..." value="<?php echo $search; ?>">
                    <button type="submit" class="search-btn">
                        <i class="bi bi-search"></i> ค้นหา
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="?action=<?php echo $action_filter; ?><?php echo isset($_GET['sort']) ? '&sort=' . $_GET['sort'] : ''; ?>" class="clear-btn">
                            <i class="bi bi-x-circle"></i> ล้าง
                        </a>
                    <?php endif; ?>
                </form>

                <?php if (!empty($search)): ?>
                    <div class="search-result-info show">
                        <i class="bi bi-info-circle me-2"></i>
                        ผลลัพธ์การค้นหาสำหรับ: "<strong><?php echo htmlspecialchars($search); ?></strong>" ใน <?php echo $action_name; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Table for Activity Logs -->
            <div class="table-wrapper">
                <div class="loading-overlay" id="loadingOverlay">
                    <div class="spinner"></div>
                </div>
                
                <div class="table-container">
                    <table class="table table-hover" id="Tableall">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center; width: 5%;">#</th>
                                <th scope="col" style="text-align: center; width: 15%;">ชื่อ-นามสกุล</th>
                                <th scope="col" style="text-align: center; width: 15%;">การกระทำ</th>
                                <th scope="col" style="text-align: center; width: 12%;">รูปแบบ</th>
                                <th scope="col" style="text-align: center; width: 10%;">รหัสรูปแบบ</th>
                                <th scope="col" style="text-align: center; width: 18%;" class="sortable-column">
                                    <a href="?action=<?php echo $action_filter; ?>&sort=<?php echo $new_sort_order; ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>">
                                        กระทำเมื่อ <i class="bi <?php echo $icon; ?>"></i>
                                    </a>
                                </th>
                                <th scope="col" style="text-align: center; width: 25%;">ข้อมูลเพิ่มเติม</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <?php
                            // Pagination settings
                            $items_per_page = 20;
                            $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                            $offset = ($current_page - 1) * $items_per_page;

                            // Build action condition based on filter
                            $action_condition = '';
                            switch ($action_filter) {
                                case 'create':
                                    $action_condition = " AND (aal.action LIKE '%create%' OR aal.action LIKE '%add%')";
                                    break;
                                case 'update':
                                    $action_condition = " AND (aal.action LIKE '%update%' OR aal.action LIKE '%edit%' OR aal.action LIKE '%modify%')";
                                    break;
                                case 'delete':
                                    $action_condition = " AND (aal.action LIKE '%delete%' OR aal.action LIKE '%remove%')";
                                    break;
                                case 'login':
                                    $action_condition = " AND aal.action LIKE '%login%'";
                                    break;
                                case 'logout':
                                    $action_condition = " AND aal.action LIKE '%logout%'";
                                    break;
                                default:
                                    $action_condition = ""; // Show all actions
                                    break;
                            }

                            // SQL query with action filter and search functionality
                            try {
                                // Count total records for pagination
                                $count_sql = "SELECT COUNT(*) as total 
                                             FROM admin_activity_log aal
                                             LEFT JOIN tb_user u ON aal.userId = u.user_id
                                             WHERE (aal.userId LIKE ? 
                                                OR u.user_firstname LIKE ?
                                                OR u.user_lastname LIKE ?
                                                OR CONCAT(COALESCE(u.user_firstname, ''), ' ', COALESCE(u.user_lastname, '')) LIKE ?
                                                OR aal.action LIKE ? 
                                                OR aal.entity LIKE ? 
                                                OR aal.entity_id LIKE ? 
                                                OR aal.create_at LIKE ? 
                                                OR aal.additional_info LIKE ?)
                                             $action_condition";
                                
                                $searchTerm = '%' . $search . '%';
                                $count_stmt = $conn->prepare($count_sql);
                                $count_stmt->bind_param("sssssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
                                $count_stmt->execute();
                                $count_result = $count_stmt->get_result();
                                $total_records = $count_result->fetch_assoc()['total'];
                                $total_pages = ceil($total_records / $items_per_page);

                                // Main query with JOIN and pagination
                                $sql = "SELECT aal.*, 
                                               CONCAT(COALESCE(u.user_firstname, ''), 
                                                     CASE 
                                                         WHEN u.user_firstname IS NOT NULL AND u.user_lastname IS NOT NULL 
                                                         THEN ' ' 
                                                         ELSE '' 
                                                     END, 
                                                     COALESCE(u.user_lastname, '')) as full_name,
                                               u.user_firstname,
                                               u.user_lastname
                                        FROM admin_activity_log aal
                                        LEFT JOIN tb_user u ON aal.userId = u.user_id
                                        WHERE (aal.userId LIKE ? 
                                           OR u.user_firstname LIKE ?
                                           OR u.user_lastname LIKE ?
                                           OR CONCAT(COALESCE(u.user_firstname, ''), ' ', COALESCE(u.user_lastname, '')) LIKE ?
                                           OR aal.action LIKE ? 
                                           OR aal.entity LIKE ? 
                                           OR aal.entity_id LIKE ? 
                                           OR aal.create_at LIKE ? 
                                           OR aal.additional_info LIKE ?)
                                        $action_condition
                                        ORDER BY aal.create_at $sort_order
                                        LIMIT $items_per_page OFFSET $offset";
                                
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("sssssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                // Calculate pagination info
                                $start_record = $offset + 1;
                                $end_record = min($offset + $items_per_page, $total_records);
                                $i = $start_record;
                                
                                if ($result && $result->num_rows > 0):
                                    while ($row = $result->fetch_assoc()) :
                                        // Determine action badge class
                                        $action_class = 'action-default';
                                        $action_lower = strtolower($row['action']);
                                        if (strpos($action_lower, 'create') !== false || strpos($action_lower, 'add') !== false) {
                                            $action_class = 'action-create';
                                        } elseif (strpos($action_lower, 'update') !== false || strpos($action_lower, 'edit') !== false) {
                                            $action_class = 'action-update';
                                        } elseif (strpos($action_lower, 'delete') !== false || strpos($action_lower, 'remove') !== false) {
                                            $action_class = 'action-delete';
                                        } elseif (strpos($action_lower, 'login') !== false) {
                                            $action_class = 'action-login';
                                        } elseif (strpos($action_lower, 'logout') !== false) {
                                            $action_class = 'action-logout';
                                        }
                            ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td class="align-middle">
                                        <strong>
                                            <?php 
                                            // แสดงชื่อ-นามสกุล หากไม่มีจะแสดงรหัสผู้ใช้
                                            if (!empty(trim($row['full_name']))) {
                                                echo htmlspecialchars($row['full_name']);
                                            } else {
                                                echo htmlspecialchars($row['userId']);
                                            }
                                            ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted"><?php echo htmlspecialchars($row['userId']); ?></small>
                                    </td>
                                    <td class="align-middle">
                                        <span class="action-badge <?php echo $action_class; ?>">
                                            <?php echo htmlspecialchars($row['action']); ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="entity-badge">
                                            <?php echo htmlspecialchars($row['entity']); ?>
                                        </span>
                                    </td>
                                    <td class="align-middle"><?php echo htmlspecialchars($row['entity_id']); ?></td>
                                    <td class="align-middle">
                                        <small class="text-muted">
                                            <?php 
                                            $date = new DateTime($row['create_at']);
                                            echo $date->format('d/m/Y H:i:s'); 
                                            ?>
                                        </small>
                                    </td>
                                    <td class="align-middle">
                                        <span class="additional-info" title="<?php echo htmlspecialchars($row['additional_info']); ?>">
                                            <?php echo htmlspecialchars($row['additional_info']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php 
                                    endwhile;
                                else:
                            ?>
                                <tr>
                                    <td colspan="7" class="no-data">
                                        <i class="bi bi-inbox mb-2" style="font-size: 2rem; display: block; color: #adb5bd;"></i>
                                        <?php if (!empty($search)): ?>
                                            ไม่พบข้อมูล Activity Log ที่ตรงกับการค้นหา "<?php echo htmlspecialchars($search); ?>" ใน <?php echo $action_name; ?>
                                        <?php else: ?>
                                            ไม่พบข้อมูล Activity Log ใน <?php echo $action_name; ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php 
                                endif;
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination Info -->
            <?php if ($total_records > 0): ?>
            <div class="pagination-info">
                <i class="bi bi-info-circle me-1"></i>
                แสดงรายการที่ <?php echo number_format($start_record); ?> - <?php echo number_format($end_record); ?> 
                จากทั้งหมด <?php echo number_format($total_records); ?> รายการ
                <?php if ($search): ?>
                    <span style="color: #F0592E; font-weight: 600;">
                        (ผลการค้นหา: "<?php echo htmlspecialchars($search); ?>")
                    </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination-container">
                <?php if ($current_page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" class="pagination-link">
                        <i class="bi bi-chevron-left"></i> ก่อนหน้า
                    </a>
                <?php endif; ?>

                <?php 
                // Show page numbers with smart pagination
                $start_page = max(1, $current_page - 2);
                $end_page = min($total_pages, $current_page + 2);
                
                // Show first page if not in range
                if ($start_page > 1) {
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="pagination-link">1</a>';
                    if ($start_page > 2) {
                        echo '<span class="pagination-ellipsis">...</span>';
                    }
                }
                
                // Show page range
                for ($i = $start_page; $i <= $end_page; $i++): 
                    $active_class = ($i == $current_page) ? ' active' : '';
                ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                       class="pagination-link<?php echo $active_class; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php
                // Show last page if not in range
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<span class="pagination-ellipsis">...</span>';
                    }
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $total_pages])) . '" class="pagination-link">' . $total_pages . '</a>';
                }
                ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" class="pagination-link">
                        ถัดไป <i class="bi bi-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php
                $stmt->close();
                $count_stmt->close();
            } catch (Exception $e) {
                error_log("Error in activity.php: " . $e->getMessage());
            ?>
                <div class="table-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col" style="text-align: center; width: 5%;">#</th>
                                <th scope="col" style="text-align: center; width: 15%;">ชื่อ-นามสกุล</th>
                                <th scope="col" style="text-align: center; width: 15%;">การกระทำ</th>
                                <th scope="col" style="text-align: center; width: 12%;">รูปแบบ</th>
                                <th scope="col" style="text-align: center; width: 10%;">รหัสรูปแบบ</th>
                                <th scope="col" style="text-align: center; width: 18%;">กระทำเมื่อ</th>
                                <th scope="col" style="text-align: center; width: 25%;">ข้อมูลเพิ่มเติม</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            <tr>
                                <td colspan="7" class="no-data">
                                    <i class="bi bi-exclamation-triangle mb-2" style="font-size: 2rem; display: block; color: #dc3545;"></i>
                                    เกิดข้อผิดพลาดในการดึงข้อมูล: <?php echo htmlspecialchars($e->getMessage()); ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips for truncated text
        const tooltipTriggerList = document.querySelectorAll('[title]');
        if (tooltipTriggerList.length > 0) {
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl, {
                    html: true
                });
            });
        }

        // Auto-focus search input when page loads
        const searchInput = document.querySelector('.search-input');
        if (searchInput && !searchInput.value) {
            setTimeout(() => {
                searchInput.focus();
            }, 500);
        }

        // Add loading effect to search form and tabs
        const searchForm = document.querySelector('.search-form');
        const loadingOverlay = document.getElementById('loadingOverlay');
        const tabButtons = document.querySelectorAll('.tab-button');

        if (searchForm) {
            searchForm.addEventListener('submit', function() {
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'flex';
                }
                
                // Add pulsing effect to search button
                const searchBtn = document.querySelector('.search-btn');
                if (searchBtn) {
                    searchBtn.style.animation = 'pulse 1s infinite';
                }
            });
        }

        // Add loading effect to tab clicks
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'flex';
                }
            });
        });

        // Add smooth scrolling to pagination, tabs, and sortable columns
        const paginationLinks = document.querySelectorAll('.pagination-link');
        const sortableLinks = document.querySelectorAll('.sortable-column a');
        
        [...paginationLinks, ...sortableLinks].forEach(link => {
            link.addEventListener('click', function() {
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'flex';
                }
                
                // Smooth scroll to top of content
                setTimeout(() => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                }, 100);
            });
        });

        // Enhanced search functionality with Enter key
        if (searchInput) {
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    searchForm.submit();
                }
            });
        }

        // Add animation to table rows
        const tableRows = document.querySelectorAll('#Tableall tbody tr');
        tableRows.forEach((row, index) => {
            row.style.animationDelay = `${index * 0.05}s`;
            row.classList.add('fade-in');
        });

        // Add hover effect to action badges
        const actionBadges = document.querySelectorAll('.action-badge');
        actionBadges.forEach(badge => {
            badge.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
                this.style.transition = 'all 0.3s ease';
            });
            
            badge.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });

        // Add click effect to entity badges
        const entityBadges = document.querySelectorAll('.entity-badge');
        entityBadges.forEach(badge => {
            badge.addEventListener('click', function() {
                // Add ripple effect
                const ripple = document.createElement('span');
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.background = 'rgba(240, 89, 46, 0.6)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'ripple 0.6s linear';
                ripple.style.left = '50%';
                ripple.style.top = '50%';
                ripple.style.width = '20px';
                ripple.style.height = '20px';
                ripple.style.marginLeft = '-10px';
                ripple.style.marginTop = '-10px';
                
                this.style.position = 'relative';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Tab button hover effects
        const allTabButtons = document.querySelectorAll('.tab-button');
        allTabButtons.forEach(button => {
            button.addEventListener('mouseenter', function() {
                if (!this.classList.contains('active')) {
                    this.querySelector('i').style.transform = 'scale(1.1)';
                }
            });
            
            button.addEventListener('mouseleave', function() {
                this.querySelector('i').style.transform = 'scale(1)';
            });
        });

        // Smooth scroll to top functionality
        function addScrollToTop() {
            const scrollBtn = document.createElement('div');
            scrollBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';
            scrollBtn.style.cssText = `
                position: fixed;
                bottom: 30px;
                right: 30px;
                width: 50px;
                height: 50px;
                background: linear-gradient(135deg, #F0592E, #FF8A65);
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                z-index: 1000;
                opacity: 0;
                transform: translateY(100px);
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
            `;
            
            document.body.appendChild(scrollBtn);
            
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    scrollBtn.style.opacity = '1';
                    scrollBtn.style.transform = 'translateY(0)';
                } else {
                    scrollBtn.style.opacity = '0';
                    scrollBtn.style.transform = 'translateY(100px)';
                }
            });
            
            scrollBtn.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
        
        addScrollToTop();
    });

    // Add CSS for animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(240, 89, 46, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(240, 89, 46, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(240, 89, 46, 0);
            }
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out both;
        }

        .container {
            animation: fadeIn 0.8s ease-out;
        }

        .action-tabs {
            animation: fadeIn 1s ease-out 0.2s both;
        }

        .search-section {
            animation: fadeIn 1s ease-out 0.3s both;
        }

        .table-container {
            animation: fadeIn 1.2s ease-out 0.4s both;
        }

        /* Improved hover effects */
        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.1);
        }

        .back-button, .search-btn, .clear-btn, .tab-button {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .search-input {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .search-input:focus {
            transform: translateY(-2px);
        }

        .tab-button i {
            transition: all 0.3s ease;
        }
    `;
    document.head.appendChild(style);
    </script>
</body>

</html>