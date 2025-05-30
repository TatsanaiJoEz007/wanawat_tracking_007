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

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Get statistics data for employee dashboard
try {
    // Total bills
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_bill FROM tb_header WHERE bill_status = 1");
    $stmt->execute();
    $total_bill_box = $stmt->get_result()->fetch_assoc()['total_bill'];
    $stmt->close();

    // Total delivery preparing (status 1)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_delivery_preparing FROM tb_delivery WHERE delivery_status = 1");
    $stmt->execute();
    $total_delivery_preparing_box = $stmt->get_result()->fetch_assoc()['total_delivery_preparing'];
    $stmt->close();

    // Total sending to distribution center (status 2)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_sending2 FROM tb_delivery WHERE delivery_status = 2");
    $stmt->execute();
    $total_sending2_box = $stmt->get_result()->fetch_assoc()['total_sending2'];
    $stmt->close();

    // Total at distribution center (status 3)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_sending3 FROM tb_delivery WHERE delivery_status = 3");
    $stmt->execute();
    $total_sending3_box = $stmt->get_result()->fetch_assoc()['total_sending3'];
    $stmt->close();

    // Total delivering to customer (status 4)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_sending4 FROM tb_delivery WHERE delivery_status = 4");
    $stmt->execute();
    $total_sending4_box = $stmt->get_result()->fetch_assoc()['total_sending4'];
    $stmt->close();

    // Total completed deliveries (status 5)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_history FROM tb_delivery WHERE delivery_status = 5");
    $stmt->execute();
    $total_history_box = $stmt->get_result()->fetch_assoc()['total_history'];
    $stmt->close();

    // Total problem deliveries (status 99)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_problem FROM tb_delivery WHERE delivery_status = 99");
    $stmt->execute();
    $total_problem_box = $stmt->get_result()->fetch_assoc()['total_problem'];
    $stmt->close();

    // Total deliveries
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_delivery FROM tb_delivery");
    $stmt->execute();
    $total_delivery = $stmt->get_result()->fetch_assoc()['total_delivery'];
    $stmt->close();

    // Total line items
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_line FROM tb_line WHERE line_status = 1");
    $stmt->execute();
    $total_line_box = $stmt->get_result()->fetch_assoc()['total_line'];
    $stmt->close();

    // Fetch distinct delivery dates for date picker
    $stmt = $conn->prepare("SELECT DISTINCT DATE(delivery_date) as delivery_date FROM tb_delivery ORDER BY delivery_date DESC");
    $stmt->execute();
    $result = $stmt->get_result();
    $dates = array();
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row["delivery_date"];
    }
    $dates_json = json_encode($dates);
    $stmt->close();

} catch (Exception $e) {
    error_log("Employee dashboard stats error: " . $e->getMessage());
    $total_bill_box = $total_delivery_preparing_box = $total_sending2_box = 0;
    $total_sending3_box = $total_sending4_box = $total_history_box = 0;
    $total_problem_box = $total_delivery = $total_line_box = 0;
    $dates = array();
    $dates_json = json_encode($dates);
}

// Handle date selection for historical data
$selected_date = null;
$data = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_date'])) {
    $selected_date = $_POST['selected_date'];
    $user_id = $_SESSION['user_id'];
    
    try {
        $stmt = $conn->prepare("SELECT * FROM tb_delivery WHERE DATE(delivery_date) = ? AND created_by = ?");
        $stmt->bind_param("si", $selected_date, $user_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error fetching historical data: " . $e->getMessage());
        $data = array();
    }
}

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
?>

<!DOCTYPE html>
<html lang="th" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Employee Dashboard - Wanawat Tracking System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS Dependencies -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" rel="stylesheet">
    
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
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }

        .stat-card.status-blue {
            border-left: 4px solid #2196F3;
        }

        .stat-card.status-yellow {
            border-left: 4px solid #FFD700;
        }

        .stat-card.status-grey {
            border-left: 4px solid #9E9E9E;
        }

        .stat-card.status-purple {
            border-left: 4px solid #9C27B0;
        }

        .stat-card.status-green {
            border-left: 4px solid #4CAF50;
        }

        .stat-card.status-red {
            border-left: 4px solid #F44336;
        }

        .stat-card.status-orange {
            border-left: 4px solid #F0592E;
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
            line-height: 1.2;
        }

        .stat-link {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(240, 89, 46, 0.1);
            padding: 0.5rem;
            text-decoration: none;
            color: #F0592E;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .stat-link:hover {
            background: rgba(240, 89, 46, 0.2);
            color: #D84315;
            text-decoration: none;
        }

        /* Date Filter Section */
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
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            text-align: center;
            margin-bottom: 1rem;
        }

        .form-container {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: center;
        }

        .date-input-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: 100%;
            max-width: 400px;
        }

        .form-control {
            border: 2px solid rgba(240, 89, 46, 0.2);
            border-radius: 8px;
            padding: 0.75rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #F0592E;
            box-shadow: 0 0 0 0.2rem rgba(240, 89, 46, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
            transform: translateY(-1px);
        }

        /* Table Section */
        .table-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            margin-top: 1.5rem;
        }

        .table-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2d3748;
            text-align: center;
            margin-bottom: 1rem;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 8px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            padding: 1rem 0.75rem;
            font-weight: 600;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            font-size: 0.9rem;
        }

        .table tbody td {
            padding: 1rem 0.75rem;
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid rgba(240, 89, 46, 0.1);
            color: #2d3748;
            font-size: 0.9rem;
        }

        .table tbody tr:nth-child(even) {
            background-color: rgba(240, 89, 46, 0.05);
        }

        .table tbody tr:hover {
            background-color: rgba(240, 89, 46, 0.1);
            transition: background-color 0.3s;
        }

        /* Status Colors for Table Rows */
        .table tbody tr.status-blue td {
            background-color: rgba(33, 150, 243, 0.1);
        }

        .table tbody tr.status-yellow td {
            background-color: rgba(255, 215, 0, 0.1);
        }

        .table tbody tr.status-grey td {
            background-color: rgba(158, 158, 158, 0.1);
        }

        .table tbody tr.status-purple td {
            background-color: rgba(156, 39, 176, 0.1);
        }

        .table tbody tr.status-green td {
            background-color: rgba(76, 175, 80, 0.1);
        }

        .table tbody tr.status-red td {
            background-color: rgba(244, 67, 54, 0.1);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #718096;
        }

        .empty-state i {
            font-size: 4rem;
            color: #adb5bd;
            margin-bottom: 1rem;
            display: block;
        }

        .empty-state h3 {
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 1.5rem;
        }

        .empty-state p {
            font-size: 1rem;
        }

        /* Datepicker Customization */
        .datepicker table tr td.enabled-date {
            background-color: #dff0d8;
            color: #3c763d;
            cursor: pointer;
        }

        .datepicker table tr td.disabled-date {
            background-color: #f2dede;
            color: #a94442;
            cursor: not-allowed;
        }

        .datepicker .dow,
        .datepicker .month {
            color: #F0592E;
            font-weight: 600;
        }

        .datepicker table tr td.active {
            background-color: #F0592E;
            color: white;
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
            cursor: pointer;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: #F0592E;
            border-color: #F0592E;
            color: white;
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

            .date-input-group {
                flex-direction: column;
                width: 100%;
            }

            .form-control {
                width: 100%;
            }

            .home-content .text {
                font-size: 20px;
            }

            .home-content .bx-menu {
                font-size: 28px;
            }

            .table-container {
                font-size: 0.8rem;
            }

            .table thead th,
            .table tbody td {
                padding: 0.5rem 0.25rem;
                font-size: 0.8rem;
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

            .date-filter-title {
                font-size: 1rem;
            }

            .table-title {
                font-size: 1rem;
            }
        }

        /* Loading Animation */
        .loading-card {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }
    </style>
</head>

<body>
    <!-- Include Sidebar -->
    <?php include_once('function/sidebar_employee.php'); ?>

    <!-- Mobile Warning Modal -->
    <div id="mobileWarningModal" class="mobile-modal">
        <div class="mobile-modal-content">
            <h2>กรุณาใช้ระบบนี้บนคอมพิวเตอร์</h2>
            <p>เพื่อให้รับประสบการณ์ในการทำงานที่ดีที่สุด <br>หน้าเพจนี้จำเป็นต้องใช้คอมพิวเตอร์</p>
            <img style="width:30%;" src="./assets/img/wehome.png" class="wehome" alt="Warning Image">
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
            <span class="text">Employee Dashboard</span>
        </div>

        <div class="dashboard-content">
            <h1 class="page-title animate__animated animate__fadeInDown">
                <i class="bi bi-person-workspace me-2"></i>
                Employee Dashboard - แดชบอร์ดพนักงาน
            </h1>

            <!-- Statistics Cards Row 1 -->
            <div class="row stats-row">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-orange">
                        <div class="stat-icon">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_bill_box); ?></div>
                        <div class="stat-title">จำนวนบิลทั้งหมด</div>
                        <a href="../../view/Employee/delivery_bill" class="stat-link">

                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-orange">
                        <div class="stat-icon">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_delivery); ?></div>
                        <div class="stat-title">จำนวนบิลขนส่งทั้งหมด</div>
                        <a href="../../view/Employee/statusbill" class="stat-link">

                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-blue">
                        <div class="stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_delivery_preparing_box); ?></div>
                        <div class="stat-title">คำสั่งซื้อที่กำลังจัดเตรียม</div>
                        <a href="../../view/Employee/preparing" class="stat-link">

                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-yellow">
                        <div class="stat-icon">
                            <i class="bi bi-arrow-right-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_sending2_box); ?></div>
                        <div class="stat-title">สินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า</div>
                        <a href="../../view/Employee/sending" class="stat-link">

                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards Row 2 -->
            <div class="row stats-row">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-grey">
                        <div class="stat-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_sending3_box); ?></div>
                        <div class="stat-title">สินค้าอยู่ที่ศูนย์กระจายสินค้าปลายทาง</div>
                        <a href="../../view/Employee/sending" class="stat-link">

                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-purple">
                        <div class="stat-icon">
                            <i class="bi bi-truck-front"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_sending4_box); ?></div>
                        <div class="stat-title">สินค้าที่กำลังนำส่งให้ลูกค้า</div>
                        <a href="../../view/Employee/sending" class="stat-link">

                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-green">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_history_box); ?></div>
                        <div class="stat-title">คำสั่งซื้อที่จัดส่งสำเร็จแล้ว</div>
                        <a href="../../view/Employee/history" class="stat-link">

                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-red">
                        <div class="stat-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_problem_box); ?></div>
                        <div class="stat-title">จำนวนบิลที่มีปัญหา</div>
                        <a href="../../view/Employee/problem" class="stat-link">

                        </a>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="chart-section animate__animated animate__fadeInLeft animate__delay-14s">
                        <h3 class="chart-title">กราฟการขนส่งตามช่วงเวลา</h3>
                        
                        <div class="filter-section">
                            <button class="filter-btn" data-filter="day">วัน</button>
                            <button class="filter-btn active" data-filter="month">เดือน</button>
                            <button class="filter-btn" data-filter="year">ปี</button>
                        </div>

                        <div id="deliveryChartError" class="error-message"></div>
                        
                        <div class="chart-container">
                            <div id="deliveryChartPlaceholder" class="loading-placeholder">
                                <div class="loading-spinner"></div>
                                <span>กำลังโหลดข้อมูล...</span>
                            </div>
                            <canvas id="deliveryChart" style="display: none;"></canvas>
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

            <!-- Date Filter Section -->
            <div class="date-filter animate__animated animate__fadeInUp animate__delay-9s">
                <h3 class="date-filter-title">
                    <i class="bi bi-calendar-alt me-2"></i>
                    ดูข้อมูลย้อนหลัง
                </h3>
                <div class="form-container">
                    <form method="POST" action="" class="w-100">
                        <div class="date-input-group">
                            <div class="input-group">
                                <input id="datepicker" name="selected_date" class="form-control" 
                                       placeholder="เลือกวันที่" autocomplete="off" 
                                       value="<?php echo htmlspecialchars($selected_date ?? ''); ?>">
                                <span class="input-group-text">
                                    <i class="bi bi-calendar"></i>
                                </span>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>ดูข้อมูล
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Historical Data Table -->
            <?php if ($data !== null): ?>
            <div class="table-section animate__animated animate__fadeInUp animate__delay-10s">
                <?php if (!empty($data)): ?>
                    <h3 class="table-title">
                        <i class="bi bi-table me-2"></i>
                        ข้อมูลการขนส่งวันที่: <?php echo htmlspecialchars($selected_date); ?>
                    </h3>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="8%">#</th>
                                    <th width="20%">เลขบิล</th>
                                    <th width="15%">จำนวน</th>
                                    <th width="35%">สถานะ</th>
                                    <th width="15%">วันที่สร้างบิล</th>
                                    <th width="7%">ประเภท</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data as $index => $row): ?>
                                    <?php
                                    switch ($row['delivery_status']) {
                                        case 1:
                                            $status_text = 'สถานะสินค้าที่คำสั่งซื้อเข้าสู่ระบบ';
                                            $status_class = 'status-blue';
                                            break;
                                        case 2:
                                            $status_text = 'สถานะสินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า';
                                            $status_class = 'status-yellow';
                                            break;
                                        case 3:
                                            $status_text = 'สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลาย';
                                            $status_class = 'status-grey';
                                            break;
                                        case 4:
                                            $status_text = 'สถานะสินค้าที่กำลังนำส่งให้ลูกค้า';
                                            $status_class = 'status-purple';
                                            break;
                                        case 5:
                                            $status_text = 'สถานะสินค้าที่ถึงนำส่งให้ลูกค้าสำเร็จ';
                                            $status_class = 'status-green';
                                            break;
                                        case 99:
                                            $status_text = 'สถานะสินค้าที่เกิดปัญหา';
                                            $status_class = 'status-red';
                                            break;
                                        default:
                                            $status_text = 'ไม่ทราบสถานะ';
                                            $status_class = '';
                                            break;
                                    }
                                    ?>
                                    <tr class="<?php echo $status_class; ?>">
                                        <td><strong><?php echo $index + 1; ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['delivery_number']); ?></td>
                                        <td><?php echo htmlspecialchars($row['delivery_weight_total']); ?></td>
                                        <td><?php echo $status_text; ?></td>
                                        <td><?php echo htmlspecialchars($row['delivery_date']); ?></td>
                                        <td><?php echo htmlspecialchars($row['created_by']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h3>ไม่มีข้อมูล</h3>
                        <p>ไม่มีข้อมูลสำหรับวันที่: <?php echo htmlspecialchars($selected_date); ?></p>
                    </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.th.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Chart Variables
            let deliveryChart = null;
            let statusChart = null;
            let selectedYear = new Date().getFullYear();
            let selectedMonth = new Date().getMonth() + 1;
            let selectedDay = new Date().getDate();
            let currentFilter = 'month';

            // DOM Elements for Charts
            const deliveryChartError = document.getElementById('deliveryChartError');
            const statusChartError = document.getElementById('statusChartError');
            const deliveryChartCanvas = document.getElementById('deliveryChart');
            const statusChartCanvas = document.getElementById('statusChart');
            const deliveryChartPlaceholder = document.getElementById('deliveryChartPlaceholder');
            const statusChartPlaceholder = document.getElementById('statusChartPlaceholder');
            const filterButtons = document.querySelectorAll('.filter-btn');
            const statusChartYear = document.getElementById('statusChartYear');

            // Available dates from PHP
            var enabledDates = <?php echo $dates_json; ?>;

            // Filter Buttons Event Listeners
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    currentFilter = filter;
                    fetchDeliveryChartData();
                });
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

            // Fetch Delivery Chart Data
            async function fetchDeliveryChartData() {
                // Show loading
                if (deliveryChartPlaceholder) deliveryChartPlaceholder.style.display = 'flex';
                if (deliveryChartCanvas) deliveryChartCanvas.style.display = 'none';
                hideError(deliveryChartError);

                try {
                    const response = await fetch('function/api/get_delivery_chart_data.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            filter: currentFilter,
                            year: selectedYear,
                            month: selectedMonth,
                            day: selectedDay
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    
                    if (data.success) {
                        createDeliveryChart(data.data);
                    } else {
                        throw new Error(data.message || 'เกิดข้อผิดพลาดในการดึงข้อมูล');
                    }

                } catch (error) {
                    console.error('Error fetching delivery chart data:', error);
                    showError(deliveryChartError, `เกิดข้อผิดพลาด: ${error.message}`);
                    
                    // Fallback to sample data
                    setTimeout(() => {
                        hideError(deliveryChartError);
                        createSampleDeliveryChart();
                    }, 2000);
                }
            }

            // Fetch Status Chart Data
            async function fetchStatusChartData() {
                // Show loading
                if (statusChartPlaceholder) statusChartPlaceholder.style.display = 'flex';
                if (statusChartCanvas) statusChartCanvas.style.display = 'none';
                hideError(statusChartError);

                try {
                    const response = await fetch('api/get_status_chart_data.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            year: selectedYear
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const data = await response.json();
                    
                    if (data.success) {
                        createStatusChart(data.data);
                    } else {
                        throw new Error(data.message || 'เกิดข้อผิดพลาดในการดึงข้อมูล');
                    }

                } catch (error) {
                    console.error('Error fetching status chart data:', error);
                    showError(statusChartError, `เกิดข้อผิดพลาด: ${error.message}`);
                    
                    // Fallback to sample data
                    setTimeout(() => {
                        hideError(statusChartError);
                        createSampleStatusChart();
                    }, 2000);
                }
            }

            // Create Delivery Chart
            function createDeliveryChart(data) {
                if (deliveryChartPlaceholder) deliveryChartPlaceholder.style.display = 'none';
                if (deliveryChartCanvas) deliveryChartCanvas.style.display = 'block';

                if (deliveryChart) {
                    deliveryChart.destroy();
                }

                const ctx = deliveryChartCanvas.getContext('2d');
                deliveryChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'จำนวนการขนส่ง',
                            data: data.values,
                            borderColor: '#F0592E',
                            backgroundColor: 'rgba(240, 89, 46, 0.1)',
                            borderWidth: 3,
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: '#F0592E',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top'
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: '#F0592E',
                                borderWidth: 1
                            }
                        },
                        interaction: {
                            mode: 'nearest',
                            axis: 'x',
                            intersect: false
                        }
                    }
                });
            }

            // Create Status Chart  
            function createStatusChart(data) {
                if (statusChartPlaceholder) statusChartPlaceholder.style.display = 'none';
                if (statusChartCanvas) statusChartCanvas.style.display = 'block';

                if (statusChart) {
                    statusChart.destroy();
                }

                const ctx = statusChartCanvas.getContext('2d');
                statusChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'เตรียมสินค้า (1)', 
                            'ส่งไปศูนย์กระจาย (2)', 
                            'อยู่ที่ศูนย์ปลายทาง (3)', 
                            'ส่งให้ลูกค้า (4)', 
                            'ส่งสำเร็จ (5)', 
                            'มีปัญหา (99)'
                        ],
                        datasets: [{
                            data: [
                                data.status_1 || 0,
                                data.status_2 || 0,
                                data.status_3 || 0,
                                data.status_4 || 0,
                                data.status_5 || 0,
                                data.status_99 || 0
                            ],
                            backgroundColor: [
                                '#2196F3',  // Blue
                                '#FFD700',  // Yellow
                                '#9E9E9E',  // Grey
                                '#9C27B0',  // Purple
                                '#4CAF50',  // Green
                                '#F44336'   // Red
                            ],
                            borderColor: '#fff',
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                titleColor: '#fff',
                                bodyColor: '#fff'
                            }
                        }
                    }
                });

                // Update year display
                if (statusChartYear) {
                    statusChartYear.textContent = selectedYear + 543;
                }
            }

            // Sample Data Functions (Fallback)
            function createSampleDeliveryChart() {
                const sampleData = {
                    month: {
                        labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
                        values: [<?php echo $total_delivery_preparing_box; ?>, <?php echo $total_sending2_box; ?>, <?php echo $total_sending3_box; ?>, <?php echo $total_sending4_box; ?>, <?php echo $total_history_box; ?>, <?php echo $total_problem_box; ?>, 15, 18, 12, 20, 16, 14]
                    },
                    day: {
                        labels: Array.from({length: 30}, (_, i) => `${i + 1}`),
                        values: Array.from({length: 30}, () => Math.floor(Math.random() * 10) + 1)
                    },
                    year: {
                        labels: ['2020', '2021', '2022', '2023', '2024'],
                        values: [150, 180, 220, 280, <?php echo $total_delivery; ?>]
                    }
                };

                createDeliveryChart(sampleData[currentFilter]);
            }

            function createSampleStatusChart() {
                const sampleStatusData = {
                    status_1: <?php echo $total_delivery_preparing_box; ?>,
                    status_2: <?php echo $total_sending2_box; ?>,
                    status_3: <?php echo $total_sending3_box; ?>,
                    status_4: <?php echo $total_sending4_box; ?>,
                    status_5: <?php echo $total_history_box; ?>,
                    status_99: <?php echo $total_problem_box; ?>
                };

                createStatusChart(sampleStatusData);
            }

            // Initialize Thai datepicker
            $('#datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd',
                language: 'th',
                beforeShowDay: function(date) {
                    var d = date.getFullYear() + "-" + 
                           ('0' + (date.getMonth() + 1)).slice(-2) + "-" + 
                           ('0' + date.getDate()).slice(-2);
                    
                    if (enabledDates.includes(d)) {
                        return {
                            classes: 'enabled-date',
                            tooltip: 'มีข้อมูลในวันนี้'
                        };
                    } else {
                        return {
                            classes: 'disabled-date',
                            tooltip: 'ไม่มีข้อมูลในวันนี้'
                        };
                    }
                }
            });

            // Stat cards hover effects
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Add loading animation to form submission
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin me-1"></i>กำลังโหลด...';
                        submitBtn.disabled = true;
                    }
                });
            }

            // Add CSS for spin animation
            const style = document.createElement('style');
            style.textContent = `
                .spin {
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
            `;
            document.head.appendChild(style);

            // Smooth scroll to table when data is loaded
            <?php if ($data !== null): ?>
            setTimeout(() => {
                const tableSection = document.querySelector('.table-section');
                if (tableSection) {
                    tableSection.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                }
            }, 500);
            <?php endif; ?>

            // Add success message if data was found
            <?php if (!empty($data)): ?>
            setTimeout(() => {
                const toast = document.createElement('div');
                toast.className = 'alert alert-success alert-dismissible fade show position-fixed';
                toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                toast.innerHTML = `
                    <i class="bi bi-check-circle me-2"></i>
                    พบข้อมูล <?php echo count($data); ?> รายการ
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 3000);
            }, 1000);
            <?php endif; ?>

            // Initialize Charts
            setTimeout(() => {
                fetchDeliveryChartData();
                fetchStatusChartData();
            }, 1000);
        });


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
    </script>
</body>
</html>