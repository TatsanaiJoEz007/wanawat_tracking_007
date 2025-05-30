<?php
        require_once('../../view/config/connect.php');

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $user_id = $_SESSION['user_id'];
    ?>

<!DOCTYPE html>
<html lang="th">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>ประวัติการจัดส่ง - Wanawat Tracking System</title>
        
        <!-- CSS Dependencies -->
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
        <script src="https://cdn.lordicon.com/lordicon.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        
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

            .container {
                position: relative;
                background: transparent;
                padding: 0;
                margin: 0;
                max-width: none;
            }

            /* Header Content - ใช้สไตล์เหมือน dashboard */
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

            .page-title {
                font-size: 2.5rem;
                font-weight: 700;
                color: white;
                margin-bottom: 10px;
                text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
            }

            .page-title i {
                color: white;
                font-size: 2.2rem;
            }

            .page-subtitle {
                color: rgba(255, 255, 255, 0.9);
                font-size: 1.1rem;
                font-weight: 400;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            /* Content Container */
            .content-container {
                width: 100%;
                max-width: 1400px;
                margin: 0 auto;
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

            /* Status Tabs */
            .status-tabs {
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

            /* Search Bar */
            .search-section {
                margin-bottom: 30px;
                padding: 25px;
                background: rgba(240, 89, 46, 0.05);
                border-radius: 15px;
                border: 1px solid rgba(240, 89, 46, 0.2);
            }

            .search-title {
                font-size: 1.3rem;
                font-weight: 600;
                color: #F0592E;
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .search-bar {
                display: flex;
                gap: 12px;
                align-items: center;
                flex-wrap: wrap;
            }

            .insearch {
                flex: 1;
                min-width: 250px;
                padding: 12px 20px;
                border: 2px solid rgba(240, 89, 46, 0.3);
                border-radius: 12px;
                font-size: 1rem;
                transition: all 0.3s ease;
                background: white;
            }

            .insearch:focus {
                outline: none;
                border-color: #F0592E;
                box-shadow: 0 0 0 3px rgba(240, 89, 46, 0.2);
                transform: translateY(-1px);
            }

            .insearch::placeholder {
                color: #adb5bd;
            }

            .search {
                padding: 12px 24px;
                background: linear-gradient(135deg, #F0592E, #FF8A65);
                color: white;
                border: none;
                border-radius: 12px;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
                box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
            }

            .search:hover {
                background: linear-gradient(135deg, #D84315, #F0592E);
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(240, 89, 46, 0.4);
            }

            /* Stats Cards */
            .stats-section {
                margin-bottom: 30px;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 20px;
            }

            .stat-card {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border-radius: 15px;
                padding: 25px;
                text-align: center;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.3);
                transition: all 0.3s ease;
                cursor: pointer;
            }

            .stat-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
                background: rgba(255, 255, 255, 1);
            }

            .stat-icon {
                font-size: 2.5rem;
                margin-bottom: 15px;
                color: #F0592E;
                display: block;
            }

            .stat-number {
                font-size: 2.2rem;
                font-weight: 700;
                color: #2d3748;
                margin-bottom: 8px;
                display: block;
            }

            .stat-title {
                font-size: 1rem;
                color: #718096;
                font-weight: 500;
            }

            /* Table Section */
            .table-section {
                margin-bottom: 30px;
            }

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

            .table-container {
                background: rgba(255, 255, 255, 0.98);
                border-radius: 18px;
                overflow: hidden;
                box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin: 0;
            }

            table thead th {
                background: linear-gradient(135deg, #F0592E, #FF8A65);
                color: white;
                border: none;
                padding: 20px 15px;
                font-weight: 600;
                text-align: center;
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                font-size: 1rem;
                position: relative;
            }

            table thead th:not(:last-child)::after {
                content: '';
                position: absolute;
                right: 0;
                top: 25%;
                height: 50%;
                width: 1px;
                background: rgba(255, 255, 255, 0.3);
            }

            table tbody td {
                padding: 18px 15px;
                vertical-align: middle;
                text-align: center;
                border-bottom: 1px solid rgba(240, 89, 46, 0.1);
                color: #2d3748;
                font-weight: 500;
            }

            table tbody tr:nth-child(even) {
                background-color: rgba(240, 89, 46, 0.03);
            }

            table tbody tr:hover {
                background-color: rgba(240, 89, 46, 0.08);
                transition: background-color 0.3s ease;
                transform: scale(1.002);
            }

            /* Status Badge Styles */
            .status-badge {
                padding: 8px 16px;
                border-radius: 20px;
                font-weight: 600;
                font-size: 0.85rem;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .status-blue {
                background: linear-gradient(135deg, #007bff, #0056b3);
                color: white;
                box-shadow: 0 3px 10px rgba(0, 123, 255, 0.3);
            }

            .status-yellow {
                background: linear-gradient(135deg, #ffc107, #e0a800);
                color: #212529;
                box-shadow: 0 3px 10px rgba(255, 193, 7, 0.3);
            }

            .status-grey {
                background: linear-gradient(135deg, #6c757d, #495057);
                color: white;
                box-shadow: 0 3px 10px rgba(108, 117, 125, 0.3);
            }

            .status-purple {
                background: linear-gradient(135deg, #6f42c1, #5a2a8a);
                color: white;
                box-shadow: 0 3px 10px rgba(111, 66, 193, 0.3);
            }

            .status-green {
                background: linear-gradient(135deg, #28a745, #20c997);
                color: white;
                box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
            }

            .status-red {
                background: linear-gradient(135deg, #dc3545, #c82333);
                color: white;
                box-shadow: 0 3px 10px rgba(220, 53, 69, 0.3);
            }

            /* Transfer Type Badge */
            .transfer-badge {
                padding: 6px 12px;
                border-radius: 15px;
                font-weight: 500;
                font-size: 0.85rem;
                background: rgba(240, 89, 46, 0.1);
                color: #F0592E;
                border: 1px solid rgba(240, 89, 46, 0.3);
            }

            /* Pagination */
            .pagination {
                display: flex;
                justify-content: center;
                align-items: center;
                gap: 8px;
                margin-top: 30px;
                flex-wrap: wrap;
            }

            .btn-custom {
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
            }

            .btn-custom:hover {
                background: rgba(240, 89, 46, 0.1);
                color: #D84315;
                text-decoration: none;
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(240, 89, 46, 0.2);
            }

            .btn-custom.active {
                background: linear-gradient(135deg, #F0592E, #FF8A65);
                color: white;
                border-color: #F0592E;
                box-shadow: 0 5px 15px rgba(240, 89, 46, 0.4);
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
                font-weight: 600;
            }

            .empty-state p {
                color: #718096;
                font-size: 1rem;
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
                    border-radius: 15px;
                }

                .status-tabs {
                    flex-direction: column;
                    align-items: stretch;
                }

                .tab-button {
                    justify-content: center;
                    text-align: center;
                }

                .search-bar {
                    flex-direction: column;
                    align-items: stretch;
                }

                .insearch {
                    min-width: auto;
                }

                .stats-grid {
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 15px;
                }

                .stat-card {
                    padding: 20px 15px;
                }

                .table-container {
                    overflow-x: auto;
                }

                table {
                    min-width: 700px;
                }

                table thead th,
                table tbody td {
                    padding: 12px 8px;
                    font-size: 0.9rem;
                }

                .pagination {
                    gap: 5px;
                }

                .btn-custom {
                    padding: 8px 12px;
                    font-size: 0.9rem;
                    min-width: 40px;
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

                .search-section {
                    padding: 20px;
                }

                .stat-card {
                    padding: 15px;
                }

                .stat-icon {
                    font-size: 2rem;
                }

                .stat-number {
                    font-size: 1.8rem;
                }
            }

            /* Loading Animation */
            .loading {
                text-align: center;
                padding: 40px;
                color: #718096;
            }

            .spinner {
                width: 40px;
                height: 40px;
                border: 4px solid rgba(240, 89, 46, 0.2);
                border-top: 4px solid #F0592E;
                border-radius: 50%;
                animation: spin 1s linear infinite;
                margin: 0 auto 20px;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
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
        </style>
    </head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>
    
    <section class="home-section">
        <!-- Header with menu button -->
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text">ประวัติการดำเนินขนส่ง</span>
        </div>
        
        <div class="container">
            <!-- Page Description -->
            <div class="page-description animate__fadeIn" style="margin-bottom: 25px; text-align: center;">
                <p style="color: rgba(255, 255, 255, 0.9); font-size: 1.1rem; font-weight: 400; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2); margin: 0;">
                    ระบบติดตามสถานะการจัดส่งทุกขั้นตอน
                </p>
            </div>

            <div class="content-container animate__fadeInUp">
                <!-- Back Button -->
                <a href="dashboard.php" class="back-button animate__fadeIn">
                    <i class="bi bi-arrow-left"></i> กลับไปหน้า Dashboard
                </a>

                <?php
                // Get status filter
                $status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
                $search_term = isset($_GET['search']) ? $_GET['search'] : '';
                ?>

                <!-- Status Tabs -->
                <div class="status-tabs">
                    <a href="?status=all<?php echo $search_term ? '&search=' . urlencode($search_term) : ''; ?>" 
                       class="tab-button <?php echo $status_filter == 'all' ? 'active' : ''; ?>">
                        <i class="bi bi-list-ul"></i>
                        ทั้งหมด
                    </a>
                    <a href="?status=preparing<?php echo $search_term ? '&search=' . urlencode($search_term) : ''; ?>" 
                       class="tab-button <?php echo $status_filter == 'preparing' ? 'active' : ''; ?>">
                        <i class="bi bi-box-seam"></i>
                        กำลังจัดเตรียม
                    </a>
                    <a href="?status=sending<?php echo $search_term ? '&search=' . urlencode($search_term) : ''; ?>" 
                       class="tab-button <?php echo $status_filter == 'sending' ? 'active' : ''; ?>">
                        <i class="bi bi-truck"></i>
                        อยู่ระหว่างขนส่ง
                    </a>
                    <a href="?status=completed<?php echo $search_term ? '&search=' . urlencode($search_term) : ''; ?>" 
                       class="tab-button <?php echo $status_filter == 'completed' ? 'active' : ''; ?>">
                        <i class="bi bi-check-circle"></i>
                        สำเร็จแล้ว
                    </a>
                    <a href="?status=problem<?php echo $search_term ? '&search=' . urlencode($search_term) : ''; ?>" 
                       class="tab-button <?php echo $status_filter == 'problem' ? 'active' : ''; ?>">
                        <i class="bi bi-exclamation-triangle"></i>
                        มีปัญหา
                    </a>
                </div>

                <!-- Search Section -->
                <div class="search-section">
                    <div class="search-title">
                        <i class="bi bi-search"></i>
                        ค้นหาข้อมูลการจัดส่ง
                    </div>
                    <div class="search-bar">
                        <form method="GET" action="" style="display: flex; gap: 12px; width: 100%; align-items: center; flex-wrap: wrap;">
                            <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
                            <input class="insearch" type="text" name="search" placeholder="ค้นหาด้วยเลขที่การจัดส่ง..." value="<?php echo htmlspecialchars($search_term); ?>">
                            <button type="submit" class="search">
                                <i class="bi bi-search"></i>
                                ค้นหา
                            </button>
                        </form>
                    </div>
                </div>

                <?php
                // Build status condition based on filter
                $status_condition = '';
                switch ($status_filter) {
                    case 'preparing':
                        $status_condition = "AND d.delivery_status = 1";
                        break;
                    case 'sending':
                        $status_condition = "AND d.delivery_status IN (2, 3, 4)";
                        break;
                    case 'completed':
                        $status_condition = "AND d.delivery_status = 5";
                        break;
                    case 'problem':
                        $status_condition = "AND d.delivery_status = 99";
                        break;
                    default:
                        $status_condition = ""; // Show all statuses
                        break;
                }

                // Query to get total number of items
                $total_items_query = "SELECT COUNT(DISTINCT d.delivery_id) as total 
                                    FROM tb_delivery d 
                                    INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
                                    WHERE d.created_by = $user_id $status_condition";

                // Append search term filter if provided
                if ($search_term) {
                    $search_term_escaped = mysqli_real_escape_string($conn, $search_term);
                    $total_items_query .= " AND d.delivery_number LIKE '%$search_term_escaped%'";
                }

                // Execute query to get total count
                $total_items_result = mysqli_query($conn, $total_items_query);

                if (!$total_items_result) {
                    echo "Error fetching total items: " . mysqli_error($conn);
                    exit;
                }

                $total_items = mysqli_fetch_assoc($total_items_result)['total'];

                $items_per_page = 20;
                $total_pages = ceil($total_items / $items_per_page);
                $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

                $offset = ($current_page - 1) * $items_per_page;

                $query = "SELECT d.delivery_id, d.delivery_number, d.delivery_date, COUNT(di.item_code) AS item_count, d.delivery_status, di.transfer_type 
                FROM tb_delivery d 
                INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
                WHERE d.created_by = $user_id $status_condition";

                // Append search term filter if provided
                if ($search_term) {
                    $search_term_escaped = mysqli_real_escape_string($conn, $search_term);
                    $query .= " AND d.delivery_number LIKE '%$search_term_escaped%'";
                }

                $query .= " GROUP BY d.delivery_id, d.delivery_number, d.delivery_date, d.delivery_status, di.transfer_type 
                          ORDER BY d.delivery_date DESC
                          LIMIT $items_per_page OFFSET $offset";

                // Execute query to fetch data
                $result = mysqli_query($conn, $query);

                if (!$result) {
                    echo "Error fetching data: " . mysqli_error($conn);
                    exit;
                }

                // Get status name for display
                $status_name = '';
                switch ($status_filter) {
                    case 'preparing':
                        $status_name = 'กำลังจัดเตรียม';
                        break;
                    case 'sending':
                        $status_name = 'อยู่ระหว่างขนส่ง';
                        break;
                    case 'completed':
                        $status_name = 'สำเร็จแล้ว';
                        break;
                    case 'problem':
                        $status_name = 'มีปัญหา';
                        break;
                    default:
                        $status_name = 'ทั้งหมด';
                        break;
                }
                ?>

                <!-- Stats Section -->
                <div class="stats-section">
                    <div class="stats-grid">
                        <div class="stat-card">
                            <i class="bi bi-list-check stat-icon"></i>
                            <span class="stat-number"><?php echo $total_items; ?></span>
                            <div class="stat-title">รายการ<?php echo $status_name; ?></div>
                        </div>
                        <div class="stat-card">
                            <i class="bi bi-calendar-check stat-icon"></i>
                            <span class="stat-number"><?php echo $total_pages; ?></span>
                            <div class="stat-title">หน้าทั้งหมด</div>
                        </div>
                        <div class="stat-card">
                            <i class="bi bi-list-ol stat-icon"></i>
                            <span class="stat-number"><?php echo $current_page; ?></span>
                            <div class="stat-title">หน้าปัจจุบัน</div>
                        </div>
                        <div class="stat-card">
                            <i class="bi bi-box stat-icon"></i>
                            <span class="stat-number"><?php echo $items_per_page; ?></span>
                            <div class="stat-title">รายการต่อหน้า</div>
                        </div>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="table-section">
                    <div class="section-title">
                        <i class="bi bi-table"></i>
                        รายละเอียดการจัดส่ง - <?php echo $status_name; ?>
                    </div>
                    
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th width="8%">#</th>
                                    <th width="20%">เลขบิล</th>
                                    <th width="12%">จำนวน</th>
                                    <th width="15%">สถานะ</th>
                                    <th width="20%">วันที่สร้างบิล</th>
                                    <th width="25%">ประเภทการขนย้าย</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (mysqli_num_rows($result) > 0) {
                                    $i = ($current_page - 1) * $items_per_page + 1;
                                    while ($row = mysqli_fetch_assoc($result)) {
                                        // Status mapping
                                        switch ($row['delivery_status']) {
                                            case 1:
                                                $status_text = 'กำลังจัดเตรียม';
                                                $status_class = 'status-badge status-blue';
                                                $status_icon = 'bi-box-seam';
                                                break;
                                            case 2:
                                                $status_text = 'กำลังจัดส่งไปศูนย์กระจาย';
                                                $status_class = 'status-badge status-yellow';
                                                $status_icon = 'bi-truck';
                                                break;
                                            case 3:
                                                $status_text = 'อยู่ที่ศูนย์กระจายสินค้า';
                                                $status_class = 'status-badge status-grey';
                                                $status_icon = 'bi-building';
                                                break;
                                            case 4:
                                                $status_text = 'กำลังนำส่งให้ลูกค้า';
                                                $status_class = 'status-badge status-purple';
                                                $status_icon = 'bi-truck';
                                                break;
                                            case 5:
                                                $status_text = 'จัดส่งสำเร็จ';
                                                $status_class = 'status-badge status-green';
                                                $status_icon = 'bi-check-circle';
                                                break;
                                            case 99:
                                                $status_text = 'สินค้าที่มีปัญหา';
                                                $status_class = 'status-badge status-red';
                                                $status_icon = 'bi-exclamation-triangle';
                                                break;
                                            default:
                                                $status_text = 'ไม่ทราบสถานะ';
                                                $status_class = 'status-badge status-grey';
                                                $status_icon = 'bi-question-circle';
                                                break;
                                        }

                                        echo "<tr>";
                                        echo "<td><strong>" . $i++ . "</strong></td>";
                                        echo "<td><strong>" . htmlspecialchars($row['delivery_number']) . "</strong></td>";
                                        echo "<td><span class='transfer-badge'>" . $row['item_count'] . " รายการ</span></td>";
                                        echo "<td><span class='$status_class'><i class='$status_icon'></i> $status_text</span></td>";
                                        echo "<td>" . date('d/m/Y H:i', strtotime($row['delivery_date'])) . "</td>";
                                        echo "<td><span class='transfer-badge'>" . htmlspecialchars($row['transfer_type']) . "</span></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr>";
                                    echo "<td colspan='6' class='empty-state'>";
                                    echo "<i class='bi bi-inbox'></i>";
                                    echo "<h3>ไม่พบข้อมูลการจัดส่ง</h3>";
                                    echo "<p>ยังไม่มีรายการ" . $status_name . "" . ($search_term ? " ที่ตรงกับการค้นหา" : "") . "</p>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" class="btn-custom">
                            <i class="bi bi-chevron-left"></i> ก่อนหน้า
                        </a>
                    <?php endif; ?>

                    <?php 
                    // Show page numbers with smart pagination
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);
                    
                    // Show first page if not in range
                    if ($start_page > 1) {
                        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '" class="btn-custom">1</a>';
                        if ($start_page > 2) {
                            echo '<span class="btn-custom" style="border: none; background: transparent; cursor: default;">...</span>';
                        }
                    }
                    
                    // Show page range
                    for ($i = $start_page; $i <= $end_page; $i++): 
                    ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                           class="btn-custom <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php
                    // Show last page if not in range
                    if ($end_page < $total_pages) {
                        if ($end_page < $total_pages - 1) {
                            echo '<span class="btn-custom" style="border: none; background: transparent; cursor: default;">...</span>';
                        }
                        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $total_pages])) . '" class="btn-custom">' . $total_pages . '</a>';
                    }
                    ?>

                    <?php if ($current_page < $total_pages): ?>
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" class="btn-custom">
                            ถัดไป <i class="bi bi-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Include SweetAlert for modal notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript section for enhanced interactions -->
    <script>
        $(document).ready(function() {
            // Add loading effect to search
            $('form').on('submit', function() {
                $('.search').html('<div class="spinner" style="width: 20px; height: 20px; border-width: 2px; margin: 0 5px 0 0;"></div>กำลังค้นหา...');
            });

            // Add hover effects to stat cards
            $('.stat-card').hover(
                function() {
                    $(this).find('.stat-icon').css('transform', 'scale(1.1)');
                },
                function() {
                    $(this).find('.stat-icon').css('transform', 'scale(1)');
                }
            );

            // Add smooth scrolling to pagination and tabs
            $('.pagination a, .tab-button').on('click', function(e) {
                $('html, body').animate({
                    scrollTop: $('.content-container').offset().top - 20
                }, 500);
            });

            // Add row click effect
            $('tbody tr').on('click', function() {
                if (!$(this).find('.empty-state').length) {
                    $(this).css('background-color', 'rgba(240, 89, 46, 0.15)');
                    setTimeout(() => {
                        $(this).css('background-color', '');
                    }, 200);
                }
            });

            // Animate elements on scroll
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            });

            document.querySelectorAll('.stat-card, .table-container, .status-tabs').forEach((el) => {
                el.style.opacity = '0';
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(el);
            });

            // Tab button hover effects
            $('.tab-button').hover(
                function() {
                    if (!$(this).hasClass('active')) {
                        $(this).find('i').css('transform', 'scale(1.1)');
                    }
                },
                function() {
                    $(this).find('i').css('transform', 'scale(1)');
                }
            );
        });

        // Auto-focus search input
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.insearch');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }
        });
    </script>
</body>

</html>