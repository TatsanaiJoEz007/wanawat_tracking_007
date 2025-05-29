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

// Handle AJAX requests for chart data
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    if ($_GET['ajax'] === 'monthly_data') {
        $year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
        
        try {
            // Get monthly bills data
            $stmt = $conn->prepare("
                SELECT MONTH(create_at) as month, COUNT(*) as count 
                FROM tb_header 
                WHERE YEAR(create_at) = ? 
                GROUP BY MONTH(create_at) 
                ORDER BY MONTH(create_at)
            ");
            $stmt->bind_param("i", $year);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $monthlyBills = array_fill(1, 12, 0);
            while ($row = $result->fetch_assoc()) {
                $monthlyBills[(int)$row['month']] = (int)$row['count'];
            }
            $stmt->close();
            
            // Get monthly deliveries data
            $stmt = $conn->prepare("
                SELECT MONTH(delivery_date) as month, COUNT(*) as count 
                FROM tb_delivery 
                WHERE YEAR(delivery_date) = ? 
                GROUP BY MONTH(delivery_date) 
                ORDER BY MONTH(delivery_date)
            ");
            $stmt->bind_param("i", $year);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $monthlyDeliveries = array_fill(1, 12, 0);
            while ($row = $result->fetch_assoc()) {
                $monthlyDeliveries[(int)$row['month']] = (int)$row['count'];
            }
            $stmt->close();
            
            echo json_encode([
                'success' => true,
                'bills' => array_values($monthlyBills),
                'deliveries' => array_values($monthlyDeliveries)
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
    
    if ($_GET['ajax'] === 'status_data') {
        try {
            // Get delivery status data
            $stmt = $conn->prepare("
                SELECT delivery_status, COUNT(*) as count 
                FROM tb_delivery 
                GROUP BY delivery_status
            ");
            $stmt->execute();
            $result = $stmt->get_result();
            
            $statusData = [
                'completed' => 0,    // status 5
                'inprogress' => 0,   // status 1,2
                'pending' => 0,      // other statuses
                'problem' => 0       // if any
            ];
            
            while ($row = $result->fetch_assoc()) {
                $status = (int)$row['delivery_status'];
                $count = (int)$row['count'];
                
                if ($status == 5) {
                    $statusData['completed'] = $count;
                } elseif ($status == 1 || $status == 2) {
                    $statusData['inprogress'] = $count;
                } else {
                    $statusData['pending'] += $count;
                }
            }
            $stmt->close();
            
            echo json_encode([
                'success' => true,
                'data' => $statusData
            ]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }
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
            height: 100vh;
            overflow: hidden;
        }

        /* Main Content Styles */
        .home-section {
            position: relative;
            background: transparent;
            height: 100vh;
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
            margin-bottom: 15px;
        }

        .home-section .home-content .bx-menu,
        .home-section .home-content .text {
            color: #fff;
            font-size: 28px;
        }

        .home-section .home-content .bx-menu {
            cursor: pointer;
            margin-right: 10px;
        }

        .home-section .home-content .text {
            font-size: 24px;
            font-weight: 600;
        }

        /* Dashboard Content Styles */
        .dashboard-content {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
            height: calc(100vh - 80px);
            overflow-y: auto;
        }

        .page-title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .stats-row {
            margin-bottom: 1rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            cursor: pointer;
            height: 110px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            margin-bottom: 0.75rem;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-icon {
            font-size: 1.5rem;
            margin-bottom: 0.4rem;
            color: #F0592E;
        }

        .stat-number {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 0.2rem;
        }

        .stat-title {
            font-size: 0.75rem;
            color: #718096;
            font-weight: 500;
        }

        /* Date Filter */
        .date-filter {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 1rem;
        }

        .date-filter-title {
            font-size: 1rem;
            font-weight: 600;
            color: #2d3748;
            text-align: center;
            margin-bottom: 0.75rem;
        }

        .date-controls {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
        }

        .date-select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.4rem;
            font-size: 0.8rem;
            background: white;
        }

        /* Chart Section */
        .chart-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 1rem;
        }

        .chart-title {
            font-size: 1rem;
            font-weight: 600;
            color: #2d3748;
            text-align: center;
            margin-bottom: 0.75rem;
        }

        .chart-container {
            height: 250px;
            position: relative;
        }

        /* Filter Buttons */
        .filter-section {
            text-align: center;
            margin-bottom: 0.75rem;
        }

        .filter-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 0.3rem 0.75rem;
            margin: 0 0.2rem;
            border-radius: 6px;
            font-size: 0.8rem;
            transition: all 0.3s ease;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: #F0592E;
            border-color: #F0592E;
            color: white;
        }

        /* Quick Actions */
        .quick-actions {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .quick-actions-title {
            font-size: 1rem;
            font-weight: 600;
            color: #2d3748;
            text-align: center;
            margin-bottom: 0.75rem;
        }

        .action-btn {
            display: block;
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 0.4rem;
            border: none;
            border-radius: 8px;
            color: white;
            text-decoration: none;
            text-align: center;
            font-weight: 500;
            font-size: 0.85rem;
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

        /* Loading States */
        .loading-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 200px;
            color: #718096;
        }

        .loading-spinner {
            width: 30px;
            height: 30px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #F0592E;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 0.75rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .home-section {
                left: 0;
                width: 100%;
                padding: 8px;
            }

            .dashboard-content {
                padding: 0.75rem;
            }

            .page-title {
                font-size: 1.2rem;
            }

            .stat-card {
                height: 90px;
                padding: 0.75rem;
            }

            .stat-number {
                font-size: 1.2rem;
            }

            .stat-title {
                font-size: 0.7rem;
            }

            .chart-container {
                height: 200px;
            }

            .home-content .text {
                font-size: 18px;
            }

            .home-content .bx-menu {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <!-- Include Sidebar -->
    <?php include_once('function/sidebar.php'); ?>

    <!-- Main Dashboard Content -->
    <section class="home-section">
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text">Wanawat Tracking Dashboard</span>
        </div>

        <div class="dashboard-content">
            <h1 class="page-title">
                <i class="bi bi-speedometer2 me-2"></i>
                Dashboard Overview - ระบบติดตามการขนส่ง
            </h1>

            <!-- Statistics Cards Row 1 -->
            <div class="row stats-row">
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_bills); ?></div>
                        <div class="stat-title">จำนวนบิลทั้งหมด</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($active_bills); ?></div>
                        <div class="stat-title">บิลที่ใช้งานได้</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-x-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($canceled_bills); ?></div>
                        <div class="stat-title">บิลที่ถูกยกเลิก</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-calendar-month"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($monthly_bills); ?></div>
                        <div class="stat-title">บิลเดือนนี้</div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards Row 2 -->
            <div class="row stats-row">
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_deliveries); ?></div>
                        <div class="stat-title">เที่ยวขนส่งทั้งหมด</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-check2-square"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($completed_deliveries); ?></div>
                        <div class="stat-title">การขนส่งเสร็จสิ้น</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($inprogress_deliveries); ?></div>
                        <div class="stat-title">การขนส่งดำเนินอยู่</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_delivery_items); ?></div>
                        <div class="stat-title">รายการส่งทั้งหมด</div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards Row 3 -->
            <div class="row stats-row">
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_users); ?></div>
                        <div class="stat-title">ผู้ใช้งาน</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($admin_count); ?></div>
                        <div class="stat-title">ผู้ดูแลระบบ</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($employee_count); ?></div>
                        <div class="stat-title">พนักงาน</div>
                    </div>
                </div>
                <div class="col-3">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="bi bi-list-ul"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_line_items); ?></div>
                        <div class="stat-title">รายการสินค้าทั้งหมด</div>
                    </div>
                </div>
            </div>

            <!-- Date Filter Section -->
            <div class="date-filter">
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
                <div class="col-8">
                    <div class="chart-section">
                        <h3 class="chart-title">กราฟบิลและการขนส่งรายเดือน</h3>
                        
                        <div class="filter-section">
                            <button class="filter-btn" data-filter="day">วัน</button>
                            <button class="filter-btn active" data-filter="month">เดือน</button>
                            <button class="filter-btn" data-filter="year">ปี</button>
                        </div>
                        
                        <div class="chart-container">
                            <div id="monthlyChartPlaceholder" class="loading-placeholder">
                                <div class="loading-spinner"></div>
                                <span>กำลังโหลดข้อมูล...</span>
                            </div>
                            <canvas id="monthlyChart" style="display: none;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-4">
                    <div class="chart-section">
                        <h3 class="chart-title">กราฟสถานะการขนส่ง</h3>
                        
                        <div id="statusChartTitle" class="text-center mb-2" style="font-size: 0.8rem;">
                            สถานะการขนส่งประจำปี <span id="statusChartYear"></span>
                        </div>
                        
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
            <div class="quick-actions">
                <h3 class="quick-actions-title">
                    <i class="bi bi-lightning me-2"></i>
                    การจัดการด่วน
                </h3>
                <div class="row">
                    <div class="col-3">
                        <a href="../admin/permission_admin" class="action-btn btn-primary-custom">
                            <i class="bi bi-shield-check me-2"></i>
                            จัดการแอดมิน
                        </a>
                    </div>
                    <div class="col-3">
                        <a href="../admin/permission_user" class="action-btn btn-success-custom">
                            <i class="bi bi-people me-2"></i>
                            จัดการผู้ใช้งาน
                        </a>
                    </div>
                    <div class="col-3">
                        <a href="../admin/permission_employee" class="action-btn btn-warning-custom">
                            <i class="bi bi-person-badge me-2"></i>
                            จัดการพนักงาน
                        </a>
                    </div>
                    <div class="col-3">
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
            async function fetchAndUpdateCharts() {
                // Show placeholders
                if (monthlyChartPlaceholder) monthlyChartPlaceholder.style.display = 'flex';
                if (statusChartPlaceholder) statusChartPlaceholder.style.display = 'flex';
                if (monthlyChartCanvas) monthlyChartCanvas.style.display = 'none';
                if (statusChartCanvas) statusChartCanvas.style.display = 'none';

                try {
                    // Fetch monthly data
                    const monthlyResponse = await fetch(`?ajax=monthly_data&year=${selectedYear}`);
                    const monthlyData = await monthlyResponse.json();
                    
                    // Fetch status data
                    const statusResponse = await fetch(`?ajax=status_data`);
                    const statusData = await statusResponse.json();

                    if (monthlyData.success && statusData.success) {
                        createCharts(monthlyData, statusData.data);
                        
                        // Hide placeholders
                        if (monthlyChartPlaceholder) monthlyChartPlaceholder.style.display = 'none';
                        if (statusChartPlaceholder) statusChartPlaceholder.style.display = 'none';
                        if (monthlyChartCanvas) monthlyChartCanvas.style.display = 'block';
                        if (statusChartCanvas) statusChartCanvas.style.display = 'block';

                        if (statusChartYear) {
                            statusChartYear.textContent = selectedYear + 543;
                        }
                    } else {
                        throw new Error('Failed to fetch chart data');
                    }

                } catch (error) {
                    console.error('Error fetching data:', error);
                    // Hide placeholders and show error
                    if (monthlyChartPlaceholder) monthlyChartPlaceholder.style.display = 'none';
                    if (statusChartPlaceholder) statusChartPlaceholder.style.display = 'none';
                }
            }

            function createCharts(monthlyData, statusData) {
                // Destroy existing charts
                if (monthlyChart) {
                    monthlyChart.destroy();
                    monthlyChart = null;
                }
                if (statusChart) {
                    statusChart.destroy();
                    statusChart = null;
                }

                // Monthly Bills & Deliveries Chart
                if (monthlyChartCanvas) {
                    const ctx = monthlyChartCanvas.getContext('2d');
                    monthlyChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: thaiMonths,
                            datasets: [{
                                label: 'จำนวนบิล',
                                data: monthlyData.bills,
                                borderColor: '#F0592E',
                                backgroundColor: 'rgba(240, 89, 46, 0.1)',
                                borderWidth: 2,
                                tension: 0.4,
                                fill: true
                            }, {
                                label: 'การขนส่ง',
                                data: monthlyData.deliveries,
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
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            }
                        }
                    });
                }

                // Delivery Status Chart
                if (statusChartCanvas) {
                    const ctx = statusChartCanvas.getContext('2d');
                    statusChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ['การขนส่งเสร็จสิ้น', 'กำลังดำเนินการ', 'รอการขนส่ง', 'ปัญหาการขนส่ง'],
                            datasets: [{
                                data: [
                                    statusData.completed,
                                    statusData.inprogress,
                                    statusData.pending,
                                    statusData.problem
                                ],
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
                            cutout: '60%',
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom',
                                    labels: {
                                        font: {
                                            size: 10
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            }

            // Initialize
            fetchAndUpdateCharts();
        });
    </script>
</body>
</html>