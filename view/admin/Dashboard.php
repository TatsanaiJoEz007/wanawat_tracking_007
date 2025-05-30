<?php 
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

// Get statistics data
try {
    // Total bills from tb_header
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_header");
    $stmt->execute();
    $total_bills = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Active bills (not canceled)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_header WHERE bill_isCanceled = 'N'");
    $stmt->execute();
    $active_bills = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Canceled bills
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_header WHERE bill_isCanceled = 'Y'");
    $stmt->execute();
    $canceled_bills = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total deliveries
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_delivery");
    $stmt->execute();
    $total_deliveries = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Completed deliveries (status = 5)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_delivery WHERE delivery_status = 5");
    $stmt->execute();
    $completed_deliveries = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // In progress deliveries (status = 1,2)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_delivery WHERE delivery_status IN (1, 2)");
    $stmt->execute();
    $inprogress_deliveries = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total users
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_user WHERE user_status = 1");
    $stmt->execute();
    $total_users = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Admin count (user_type = 999)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_user WHERE user_type = 999 AND user_status = 1");
    $stmt->execute();
    $admin_count = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Employee count (user_type = 1)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_user WHERE user_type = 1 AND user_status = 1");
    $stmt->execute();
    $employee_count = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Bills this month
    $currentMonth = date('m');
    $currentYear = date('Y');
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_header WHERE MONTH(create_at) = ? AND YEAR(create_at) = ?");
    $stmt->bind_param("ss", $currentMonth, $currentYear);
    $stmt->execute();
    $monthly_bills = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total delivery items
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_delivery_items");
    $stmt->execute();
    $total_delivery_items = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total line items
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_line");
    $stmt->execute();
    $total_line_items = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    $total_bills = $active_bills = $canceled_bills = $total_deliveries = 0;
    $completed_deliveries = $inprogress_deliveries = $total_users = $admin_count = 0;
    $employee_count = $monthly_bills = $total_delivery_items = $total_line_items = 0;
}

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
?>

<!DOCTYPE html>
<html lang="th" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Wanawat Tracking System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS Dependencies -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
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

        /* Main Content Styles */
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
        }

        .home-section .home-content .text {
            font-size: 26px;
            font-weight: 600;
        }

        /* Dashboard Content Styles */
        .dashboard-content {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .page-title {
            color: #fff;
            font-size: 2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .stats-row {
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
            height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #F0592E;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.25rem;
        }

        .stat-title {
            font-size: 0.9rem;
            color: #718096;
            font-weight: 500;
        }

        /* Chart Section */
        .chart-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 1.5rem;
        }

        .chart-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            text-align: center;
            margin-bottom: 1rem;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        /* Filter Buttons */
        .filter-section {
            text-align: center;
            margin-bottom: 1rem;
        }

        .filter-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 0.5rem 1rem;
            margin: 0 0.25rem;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: #F0592E;
            border-color: #F0592E;
            color: white;
        }

        /* Date Filter */
        .date-filter {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 1.5rem;
        }

        .date-filter-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
            text-align: center;
            margin-bottom: 1rem;
        }

        .date-controls {
            display: flex;
            gap: 1rem;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .date-select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.5rem;
            font-size: 0.9rem;
            background: white;
        }

        /* Loading States */
        .loading-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 250px;
            color: #718096;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #F0592E;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Quick Actions */
        .quick-actions {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .quick-actions-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            text-align: center;
            margin-bottom: 1rem;
        }

        .action-btn {
            display: block;
            width: 100%;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border: none;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            text-align: center;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .btn-primary-custom { background: linear-gradient(135deg, #F0592E, #FF8A65); }
        .btn-success-custom { background: linear-gradient(135deg, #48bb78, #38a169); }
        .btn-warning-custom { background: linear-gradient(135deg, #ed8936, #dd6b20); }
        .btn-danger-custom { background: linear-gradient(135deg, #f56565, #e53e3e); }

        /* Mobile Warning Modal */
        .mobile-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.8);
        }

        .mobile-modal-content {
            position: absolute;
            height: 50%;
            width: 90%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #fff;
            padding: 2rem;
            text-align: center;
            border-radius: 8px;
            max-width: 90%;
        }

        /* Error Message */
        .error-message {
            display: none;
            color: #dc3545;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 10px;
            margin: 10px 0;
            text-align: center;
        }

        /* Responsive Design */
        @media screen and (max-width: 1200px) {
            .stat-card {
                height: 130px;
                padding: 1rem;
            }

            .stat-number {
                font-size: 1.6rem;
            }

            .chart-container {
                height: 280px;
            }
        }

        @media screen and (max-width: 768px) {
            .home-section {
                left: 0;
                width: 100%;
                padding: 12px 8px;
            }

            .dashboard-content {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .stat-card {
                height: 120px;
                padding: 1rem;
                margin-bottom: 1rem;
            }

            .stat-number {
                font-size: 1.4rem;
            }

            .stat-title {
                font-size: 0.8rem;
            }

            .chart-container {
                height: 250px;
            }

            .date-controls {
                flex-direction: column;
                gap: 0.5rem;
            }

            .date-select {
                width: 100%;
                max-width: 200px;
            }

            .filter-btn {
                padding: 0.4rem 0.8rem;
                font-size: 0.8rem;
                margin: 0.1rem;
            }

            .home-content .text {
                font-size: 20px;
            }

            .home-content .bx-menu {
                font-size: 28px;
            }
        }

        @media screen and (max-width: 480px) {
            .dashboard-content {
                padding: 0.5rem;
            }

            .page-title {
                font-size: 1.2rem;
                margin-bottom: 1rem;
            }

            .stat-card {
                height: 100px;
                padding: 0.5rem;
            }

            .stat-number {
                font-size: 1.2rem;
            }

            .stat-icon {
                font-size: 1.5rem;
                margin-bottom: 0.25rem;
            }

            .chart-container {
                height: 200px;
            }

            .chart-title {
                font-size: 1rem;
            }

            .date-filter-title {
                font-size: 1rem;
            }

            .quick-actions-title {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <!-- Include Sidebar -->
    <?php include_once('function/sidebar.php'); ?>

    <!-- Mobile Warning Modal -->
    <div id="mobileWarningModal" class="mobile-modal">
        <div class="mobile-modal-content">
            <h2>กรุณาใช้ระบบนี้บนคอมพิวเตอร์</h2>
            <p>เพื่อให้รับประสบการณ์ในการทำงานที่ดีที่สุด <br>หน้าเพจนี้จำเป็นต้องใช้คอมพิวเตอร์</p>
            <img style="width:30%;" src="./assets/img/adminpic/wehome.png" class="wehome" alt="Warning Image">
            <br><br>
            <a href="#" id="mobile-logout-button" data-csrf-token="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>">
                <i class="fas fa-sign-out-alt" style="color:red;"></i>
                <span style="color:red;">ออกจากระบบ</span>
            </a>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <section class="home-section">
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text">Wanawat Tracking Dashboard</span>
        </div>

        <div class="dashboard-content">
            <h1 class="page-title animate__animated animate__fadeInDown">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard Overview - ระบบติดตามการขนส่ง
            </h1>

            <!-- Statistics Cards - บิลและการขนส่ง -->
            <div class="row stats-row">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-1s">
                        <div class="stat-icon">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_bills); ?></div>
                        <div class="stat-title">จำนวนบิลทั้งหมด</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-2s">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($active_bills); ?></div>
                        <div class="stat-title">บิลที่ใช้งานได้</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-3s">
                        <div class="stat-icon">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($canceled_bills); ?></div>
                        <div class="stat-title">บิลที่ถูกยกเลิก</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-4s">
                        <div class="stat-icon">
                            <i class="bi bi-calendar-month"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($monthly_bills); ?></div>
                        <div class="stat-title">บิลเดือนนี้</div>
                    </div>
                </div>
            </div>

            <!-- Additional Stats Row - การขนส่งและผู้ใช้งาน -->
            <div class="row stats-row">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-5s">
                        <div class="stat-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_deliveries); ?></div>
                        <div class="stat-title">เที่ยวขนส่งทั้งหมด</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-6s">
                        <div class="stat-icon">
                            <i class="bi bi-check2-square"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($completed_deliveries); ?></div>
                        <div class="stat-title">การขนส่งเสร็จสิ้น</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-7s">
                        <div class="stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($inprogress_deliveries); ?></div>
                        <div class="stat-title">การขนส่งดำเนินอยู่</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-8s">
                        <div class="stat-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_delivery_items); ?></div>
                        <div class="stat-title">รายการส่งทั้งหมด</div>
                    </div>
                </div>
            </div>

            <!-- User Stats Row -->
            <div class="row stats-row">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-9s">
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_users); ?></div>
                        <div class="stat-title">ผู้ใช้งานทั้งหมด</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-10s">
                        <div class="stat-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($admin_count); ?></div>
                        <div class="stat-title">ผู้ดูแลระบบ</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-11s">
                        <div class="stat-icon">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($employee_count); ?></div>
                        <div class="stat-title">พนักงาน</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card animate__animated animate__fadeInUp animate__delay-12s">
                        <div class="stat-icon">
                            <i class="bi bi-list-ul"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_line_items); ?></div>
                        <div class="stat-title">รายการสินค้าทั้งหมด</div>
                    </div>
                </div>
            </div>

            <!-- Date Filter Section -->
            <div class="date-filter animate__animated animate__fadeInUp animate__delay-13s">
                <h3 class="date-filter-title">
                    <i class="bi bi-calendar-alt me-2"></i>
                    เลือกวันที่ดูข้อมูลย้อนหลัง
                </h3>
                <div class="date-controls">
                    <select id="yearSelect" class="date-select">
                        <!-- จะเติมด้วย JavaScript -->
                    </select>
                    <select id="monthSelect" class="date-select" style="display: none;">
                        <!-- จะเติมด้วย JavaScript -->
                    </select>
                    <select id="daySelect" class="date-select" style="display: none;">
                        <!-- จะเติมด้วย JavaScript -->
                    </select>
                    <button id="searchBtn" class="btn btn-primary btn-sm">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="chart-section animate__animated animate__fadeInLeft animate__delay-14s">
                        <h3 class="chart-title">กราฟบิลและการขนส่งรายเดือน</h3>
                        
                        <div class="filter-section">
                            <button class="filter-btn" data-filter="day">วัน</button>
                            <button class="filter-btn active" data-filter="month">เดือน</button>
                            <button class="filter-btn" data-filter="year">ปี</button>
                        </div>

                        <div id="monthlyChartError" class="error-message"></div>
                        
                        <div class="chart-container">
                            <div id="monthlyChartPlaceholder" class="loading-placeholder">
                                <div class="loading-spinner"></div>
                                <span>กำลังโหลดข้อมูล...</span>
                            </div>
                            <canvas id="monthlyChart" style="display: none;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="chart-section animate__animated animate__fadeInRight animate__delay-15s">
                        <h3 class="chart-title">กราฟสถานะการขนส่ง</h3>
                        
                        <div id="statusChartTitle" class="text-center mb-3">
                            สถานะการขนส่งประจำปี <span id="statusChartYear"></span>
                        </div>

                        <div id="statusChartError" class="error-message"></div>
                        
                        <div class="chart-container">
                            <div id="statusChartPlaceholder" class="loading-placeholder">
                                <div class="loading-spinner"></div>
                                <span>กำลังโหลดข้อมูล...</span>
                            </div>
                            <canvas id="statusChart" style="display: none;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <?php if (isset($permissions['manage_permission']) && $permissions['manage_permission'] == 1): ?>
            <div class="quick-actions animate__animated animate__fadeInUp animate__delay-16s">
                <h3 class="quick-actions-title">
                    <i class="bi bi-lightning me-2"></i>
                    การจัดการด่วน
                </h3>
                <div class="row">
                    <div class="col-lg-3 col-md-6 mb-2">
                        <a href="../admin/permission_admin" class="action-btn btn-primary-custom">
                            <i class="bi bi-shield-check me-2"></i>
                            จัดการแอดมิน
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2">
                        <a href="../admin/permission_user" class="action-btn btn-success-custom">
                            <i class="bi bi-people me-2"></i>
                            จัดการผู้ใช้งาน
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2">
                        <a href="../admin/permission_employee" class="action-btn btn-warning-custom">
                            <i class="bi bi-person-badge me-2"></i>
                            จัดการพนักงาน
                        </a>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-2">
                        <a href="../admin/Manage.php" class="action-btn btn-danger-custom">
                            <i class="bi bi-gear me-2"></i>
                            ควบคุมสิทธิ์
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Variables
            let monthlyChart = null;
            let statusChart = null;
            let selectedYear = new Date().getFullYear();
            let selectedMonth = new Date().getMonth() + 1;
            let selectedDay = new Date().getDate();
            let currentFilter = 'month';

            // DOM Elements
            const monthlyChartError = document.getElementById('monthlyChartError');
            const statusChartError = document.getElementById('statusChartError');
            const monthlyChartCanvas = document.getElementById('monthlyChart');
            const statusChartCanvas = document.getElementById('statusChart');
            const monthlyChartPlaceholder = document.getElementById('monthlyChartPlaceholder');
            const statusChartPlaceholder = document.getElementById('statusChartPlaceholder');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const statusChartYear = document.getElementById('statusChartYear');

            // Initialize Year Select
            const yearSelect = document.getElementById('yearSelect');
            const currentYear = new Date().getFullYear();
            for (let year = currentYear; year >= currentYear - 5; year--) {
                const option = document.createElement('option');
                option.value = year;
                option.textContent = year + 543;
                yearSelect.appendChild(option);
            }

            // Initialize Month Select
            const monthSelect = document.getElementById('monthSelect');
            const thaiMonths = [
                'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];
            thaiMonths.forEach((month, index) => {
                const option = document.createElement('option');
                option.value = index + 1;
                option.textContent = month;
                monthSelect.appendChild(option);
            });
            monthSelect.value = new Date().getMonth() + 1;

            // Initialize Day Select
            const daySelect = document.getElementById('daySelect');
            function populateDays(year, month) {
                daySelect.innerHTML = '';
                const daysInMonth = new Date(year, month, 0).getDate();
                for (let day = 1; day <= daysInMonth; day++) {
                    const option = document.createElement('option');
                    option.value = day;
                    option.textContent = day;
                    daySelect.appendChild(option);
                }
                selectedDay = Math.min(selectedDay, daysInMonth);
                daySelect.value = selectedDay;
            }
            populateDays(selectedYear, selectedMonth);

            // Event Listeners
            yearSelect.addEventListener('change', function() {
                selectedYear = parseInt(this.value);
                populateDays(selectedYear, selectedMonth);
                fetchAndUpdateCharts();
            });

            monthSelect.addEventListener('change', function() {
                selectedMonth = parseInt(this.value);
                populateDays(selectedYear, selectedMonth);
                fetchAndUpdateCharts();
            });

            daySelect.addEventListener('change', function() {
                selectedDay = parseInt(this.value);
                fetchAndUpdateCharts();
            });

            // Filter Buttons
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = filter;

                    // Show/hide date selectors
                    if (filter === 'day') {
                        monthSelect.style.display = 'inline-block';
                        daySelect.style.display = 'inline-block';
                    } else if (filter === 'month') {
                        monthSelect.style.display = 'inline-block';
                        daySelect.style.display = 'none';
                    } else {
                        monthSelect.style.display = 'none';
                        daySelect.style.display = 'none';
                    }

                    fetchAndUpdateCharts();
                });
            });

            // Search Button
            document.getElementById('searchBtn').addEventListener('click', function() {
                fetchAndUpdateCharts();
            });

            // Chart Functions
            function showError(element, message) {
                if (element) {
                    element.textContent = message;
                    element.style.display = 'block';
                }
                console.error(message);
            }

            function hideError(element) {
                if (element) {
                    element.style.display = 'none';
                }
            }

            async function fetchAndUpdateCharts() {
                // Show placeholders
                if (monthlyChartPlaceholder) monthlyChartPlaceholder.style.display = 'flex';
                if (statusChartPlaceholder) statusChartPlaceholder.style.display = 'flex';
                if (monthlyChartCanvas) monthlyChartCanvas.style.display = 'none';
                if (statusChartCanvas) statusChartCanvas.style.display = 'none';

                hideError(monthlyChartError);
                hideError(statusChartError);

                try {
                    // Simulate data fetch
                    setTimeout(() => {
                        // Hide placeholders
                        if (monthlyChartPlaceholder) monthlyChartPlaceholder.style.display = 'none';
                        if (statusChartPlaceholder) statusChartPlaceholder.style.display = 'none';
                        if (monthlyChartCanvas) monthlyChartCanvas.style.display = 'block';
                        if (statusChartCanvas) statusChartCanvas.style.display = 'block';

                        // Create sample charts with delivery-related data
                        createSampleCharts();

                        if (statusChartYear) {
                            statusChartYear.textContent = selectedYear + 543;
                        }
                    }, 1500);

                } catch (error) {
                    console.error('Error fetching data:', error);
                    showError(monthlyChartError, `เกิดข้อผิดพลาด: ${error.message}`);
                    showError(statusChartError, `เกิดข้อผิดพลาด: ${error.message}`);
                }
            }

            function createSampleCharts() {
                // Monthly Bills & Deliveries Chart
                if (monthlyChartCanvas && !monthlyChart) {
                    const ctx = monthlyChartCanvas.getContext('2d');
                    monthlyChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: thaiMonths,
                            datasets: [{
                                label: 'จำนวนบิล',
                                data: [45, 52, 38, 67, 49, 73, 61, 84, 56, 71, 68, 42],
                                borderColor: '#F0592E',
                                backgroundColor: 'rgba(240, 89, 46, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true
                            }, {
                                label: 'การขนส่ง',
                                data: [12, 15, 10, 18, 14, 22, 19, 25, 16, 20, 18, 13],
                                borderColor: '#4CAF50',
                                backgroundColor: 'rgba(76, 175, 80, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }

                // Delivery Status Chart
                if (statusChartCanvas && !statusChart) {
                    const ctx = statusChartCanvas.getContext('2d');
                    statusChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['การขนส่งเสร็จสิ้น', 'กำลังดำเนินการ', 'รอการขนส่ง', 'ปัญหาการขนส่ง'],
                            datasets: [{
                                data: [<?php echo $completed_deliveries; ?>, <?php echo $inprogress_deliveries; ?>, <?php echo ($total_deliveries - $completed_deliveries - $inprogress_deliveries); ?>, 0],
                                backgroundColor: [
                                    '#4CAF50',
                                    '#2196F3', 
                                    '#FFD700',
                                    '#FF5252'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '60%'
                        }
                    });
                }
            }

            // Mobile Warning
            function checkScreenSize() {
                const mobileModal = document.getElementById('mobileWarningModal');
                if (window.innerWidth < 768) {
                    if (mobileModal) mobileModal.style.display = 'block';
                } else {
                    if (mobileModal) mobileModal.style.display = 'none';
                }
            }

            checkScreenSize();
            window.addEventListener('resize', checkScreenSize);

            // Initialize
            fetchAndUpdateCharts();
        });
    </script>
</body>
</html>