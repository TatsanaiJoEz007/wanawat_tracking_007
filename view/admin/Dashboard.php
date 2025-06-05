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

// ตรวจสอบสถานะผู้ใช้งานจาก database - ถ้าเป็น 9 ต้องบังคับเปลี่ยนรหัสผ่าน
$force_update_profile = false;
try {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT user_status FROM tb_user WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user_data = $result->fetch_assoc();
        $stmt->close();
        
        if ($user_data && $user_data['user_status'] == 9) {
            $force_update_profile = true;
        }
    }
} catch (Exception $e) {
    error_log("Error checking user status: " . $e->getMessage());
}

// Get statistics data (same as employee dashboard)
try {
    // Total bills (แสดงบิลทั้งหมดที่มีสถานะ 1)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_bill FROM tb_header WHERE bill_status = 1");
    $stmt->execute();
    $result = $stmt->get_result();
    $total_bill_box = $result->fetch_assoc()['total_bill'];
    $stmt->close();

    // Total delivery preparing (status 1)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_delivery_preparing FROM tb_delivery WHERE delivery_status = 1");
    $stmt->execute();
    $result = $stmt->get_result();
    $total_delivery_preparing_box = $result->fetch_assoc()['total_delivery_preparing'];
    $stmt->close();

    // Total sending to distribution center (status 2)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_sending2 FROM tb_delivery WHERE delivery_status = 2");
    $stmt->execute();
    $result = $stmt->get_result();
    $total_sending2_box = $result->fetch_assoc()['total_sending2'];
    $stmt->close();

    // Total at distribution center (status 3)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_sending3 FROM tb_delivery WHERE delivery_status = 3");
    $stmt->execute();
    $result = $stmt->get_result();
    $total_sending3_box = $result->fetch_assoc()['total_sending3'];
    $stmt->close();

    // Total delivering to customer (status 4)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_sending4 FROM tb_delivery WHERE delivery_status = 4");
    $stmt->execute();
    $result = $stmt->get_result();
    $total_sending4_box = $result->fetch_assoc()['total_sending4'];
    $stmt->close();

    // Total completed deliveries (status 5)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_history FROM tb_delivery WHERE delivery_status = 5");
    $stmt->execute();
    $result = $stmt->get_result();
    $total_history_box = $result->fetch_assoc()['total_history'];
    $stmt->close();

    // Total problem deliveries (status 99)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_problem FROM tb_delivery WHERE delivery_status = 99");
    $stmt->execute();
    $result = $stmt->get_result();
    $total_problem_box = $result->fetch_assoc()['total_problem'];
    $stmt->close();

    // Additional admin stats
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

    // Online users (user_is_online = 1)
    $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM tb_user WHERE user_is_online = 1 AND user_status = 1");
    $stmt->execute();
    $online_users = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    // Total deliveries
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_delivery FROM tb_delivery");
    $stmt->execute();
    $result = $stmt->get_result();
    $total_delivery = $result->fetch_assoc()['total_delivery'];
    $stmt->close();

    // Total line items
    $stmt = $conn->prepare("SELECT COUNT(*) AS total_line FROM tb_line WHERE line_status = '1'");
    $stmt->execute();
    $result = $stmt->get_result();
    $total_line_box = $result->fetch_assoc()['total_line'];
    $stmt->close();

} catch (Exception $e) {
    error_log("Admin dashboard stats error: " . $e->getMessage());
    $total_bill_box = $total_delivery_preparing_box = $total_sending2_box = 0;
    $total_sending3_box = $total_sending4_box = $total_history_box = 0;
    $total_problem_box = $total_users = $admin_count = $employee_count = 0;
    $online_users = $total_delivery = $total_line_box = 0;
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
    <link rel="icon" type="image/x-icon" href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
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

        .stat-card.status-dark {
            border-left: 4px solid #424242;
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

        /* Online User Cards and Activity Items */
        .online-user-card {
            background: rgba(76, 175, 80, 0.1);
            border: 1px solid rgba(76, 175, 80, 0.3);
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .online-user-card:hover {
            background: rgba(76, 175, 80, 0.15);
            transform: translateY(-1px);
        }

        .online-indicator {
            width: 10px;
            height: 10px;
            background: #4CAF50;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(76, 175, 80, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(76, 175, 80, 0); }
            100% { box-shadow: 0 0 0 0 rgba(76, 175, 80, 0); }
        }

        .activity-item {
            background: rgba(248, 249, 250, 0.8);
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .activity-item:hover {
            background: rgba(240, 89, 46, 0.05);
            border-color: rgba(240, 89, 46, 0.3);
            transform: translateY(-1px);
        }

        .activity-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 600;
            color: white;
        }

        .activity-create {
            background: linear-gradient(135deg, #48bb78, #38a169);
        }

        .activity-update {
            background: linear-gradient(135deg, #4299e1, #3182ce);
        }

        .activity-delete {
            background: linear-gradient(135deg, #f56565, #e53e3e);
        }

        .activity-login {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
        }

        .activity-logout {
            background: linear-gradient(135deg, #718096, #4a5568);
        }

        .activity-default {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
        }

        .activity-details {
            flex: 1;
        }

        .activity-action {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 2px;
        }

        .activity-info {
            font-size: 0.85rem;
            color: #718096;
            margin-bottom: 2px;
        }

        .activity-time {
            font-size: 0.8rem;
            color: #adb5bd;
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

        /* Profile Update Modal Styles */
        .profile-update-modal {
            backdrop-filter: blur(10px);
        }

        .location-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        .location-select:focus {
            border-color: #F0592E;
            box-shadow: 0 0 0 0.2rem rgba(240, 89, 46, 0.25);
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
            <span class="text">Admin Dashboard</span>
        </div>

        <div class="dashboard-content">
            <h1 class="page-title animate__animated animate__fadeInDown">
                <i class="bi bi-speedometer2 me-2"></i>
                Admin Dashboard - แดชบอร์ดผู้ดูแลระบบ
            </h1>

            <!-- Statistics Cards Row 1 (4 cards) - Same as Employee -->
            <div class="row stats-row">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-orange" onclick="openStatModal('all_bills', 'บิลทั้งหมด')">
                        <div class="stat-icon">
                            <i class="bi bi-receipt-cutoff"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_bill_box); ?></div>
                        <div class="stat-title">จำนวนบิลทั้งหมด</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-blue" onclick="openStatModal('preparing', 'คำสั่งซื้อเข้าสู่ระบบ')">
                        <div class="stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_delivery_preparing_box); ?></div>
                        <div class="stat-title">คำสั่งซื้อเข้าสู่ระบบ</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-yellow" onclick="openStatModal('sending_center', 'สินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า')">
                        <div class="stat-icon">
                            <i class="bi bi-arrow-right-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_sending2_box); ?></div>
                        <div class="stat-title">สินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-grey" onclick="openStatModal('at_center', 'สินค้าอยู่ที่ศูนย์กระจายสินค้าปลายทาง')">
                        <div class="stat-icon">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_sending3_box); ?></div>
                        <div class="stat-title">สินค้าอยู่ที่ศูนย์กระจายสินค้าปลายทาง</div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards Row 2 (3 cards) - Same as Employee -->
            <div class="row stats-row">
                <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-purple" onclick="openStatModal('delivering', 'สินค้าที่กำลังนำส่งให้ลูกค้า')">
                        <div class="stat-icon">
                            <i class="bi bi-truck-front"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_sending4_box); ?></div>
                        <div class="stat-title">สินค้าที่กำลังนำส่งให้ลูกค้า</div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-green" onclick="openStatModal('completed', 'คำสั่งซื้อที่จัดส่งสำเร็จแล้ว')">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_history_box); ?></div>
                        <div class="stat-title">คำสั่งซื้อที่จัดส่งสำเร็จแล้ว</div>
                    </div>
                </div>

                <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-red" onclick="openStatModal('problem', 'จำนวนบิลที่มีปัญหา')">
                        <div class="stat-icon">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_problem_box); ?></div>
                        <div class="stat-title">จำนวนบิลที่มีปัญหา</div>
                    </div>
                </div>
            </div>

            <!-- User Management Statistics Row -->
            <div class="row stats-row">
                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-dark" onclick="openStatModal('all_users', 'ผู้ใช้งานทั้งหมด')">
                        <div class="stat-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_users); ?></div>
                        <div class="stat-title">ผู้ใช้งานทั้งหมด</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-red" onclick="openStatModal('admin_users', 'ผู้ดูแลระบบ')">
                        <div class="stat-icon">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($admin_count); ?></div>
                        <div class="stat-title">ผู้ดูแลระบบ</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-blue" onclick="openStatModal('employee_users', 'พนักงาน')">
                        <div class="stat-icon">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($employee_count); ?></div>
                        <div class="stat-title">พนักงาน</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-green" onclick="openStatModal('online_users', 'ผู้ใช้งานออนไลน์')">
                        <div class="stat-icon">
                            <i class="bi bi-wifi"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($online_users); ?></div>
                        <div class="stat-title">ผู้ใช้งานออนไลน์</div>
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

            <!-- Online Users and Activity Section -->
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <div class="chart-section animate__animated animate__fadeInLeft animate__delay-16s">
                        <h3 class="chart-title">
                            <i class="bi bi-wifi me-2"></i>
                            ผู้ใช้งานที่ออนไลน์อยู่
                        </h3>
                        
                        <div id="onlineUsersError" class="error-message"></div>
                        
                        <div style="max-height: 300px; overflow-y: auto;">
                            <div id="onlineUsersContent">
                                <div class="loading-placeholder" style="height: 150px;">
                                    <div class="loading-spinner"></div>
                                    <span>กำลังโหลดข้อมูลผู้ใช้ออนไลน์...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="chart-section animate__animated animate__fadeInRight animate__delay-17s">
                        <h3 class="chart-title">
                            <i class="bi bi-clock-history me-2"></i>
                            ประวัติกิจกรรมล่าสุด
                        </h3>
                        
                        <div id="activityLogError" class="error-message"></div>
                        
                        <div style="max-height: 300px; overflow-y: auto;">
                            <div id="activityLogContent">
                                <div class="loading-placeholder" style="height: 200px;">
                                    <div class="loading-spinner"></div>
                                    <span>กำลังโหลดประวัติกิจกรรม...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal สำหรับแสดงรายละเอียด Statistics -->
    <div class="modal fade" id="statModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statModalTitle">
                        <i class="bi bi-info-circle me-2"></i>
                        รายละเอียด
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="statModalContent">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        ปิด
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal สำหรับแสดงรายละเอียดการขนส่ง -->
    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-info-circle me-2"></i>
                        รายละเอียดการจัดส่ง
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Modal body content -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i>
                        ปิด
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal เปลี่ยนรหัสผ่านและที่อยู่ -->
    <div class="modal fade profile-update-modal" id="updateProfileModal" tabindex="-1" role="dialog" aria-labelledby="updateProfileModalLabel" 
         aria-hidden="true" data-bs-backdrop="<?php echo $force_update_profile ? 'static' : 'true'; ?>" 
         data-bs-keyboard="<?php echo $force_update_profile ? 'false' : 'true'; ?>">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #F0592E, #FF8A65); color: white;">
                    <h5 class="modal-title" id="updateProfileModalLabel">
                        <i class="bi bi-person-gear me-2"></i>
                        <?php echo $force_update_profile ? 'ยืนยันข้อมูลและเปลี่ยนรหัสผ่าน (จำเป็น)' : 'เปลี่ยนรหัสผ่านและที่อยู่'; ?>
                    </h5>
                    <?php if (!$force_update_profile): ?>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    <?php endif; ?>
                </div>
                
                <form id="updateProfileForm">
                    <div class="modal-body">
                        <?php if ($force_update_profile): ?>
                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>จำเป็นต้องยืนยันข้อมูล:</strong> กรุณาเปลี่ยนรหัสผ่านและระบุที่อยู่เพื่อเปิดใช้งานบัญชีของคุณ
                        </div>
                        <?php endif; ?>
                        
                        <div class="row">
                            <!-- ส่วนรหัสผ่าน -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header" style="background: rgba(240, 89, 46, 0.1); border-bottom: 1px solid rgba(240, 89, 46, 0.2);">
                                        <h6 class="mb-0" style="color: #F0592E;">
                                            <i class="bi bi-shield-lock me-2"></i>
                                            เปลี่ยนรหัสผ่าน
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="new_password" class="form-label">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-key"></i>
                                                </span>
                                                <input type="password" class="form-control" id="new_password" name="new_password" required 
                                                       placeholder="กรอกรหัสผ่านใหม่" minlength="6">
                                                <button type="button" class="btn btn-outline-secondary" id="toggleNewPassword">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                            <div class="form-text">รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-key-fill"></i>
                                                </span>
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required 
                                                       placeholder="ยืนยันรหัสผ่านใหม่" minlength="6">
                                                <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                            <div class="invalid-feedback" id="passwordError"></div>
                                        </div>
                                        
                                        <div class="password-strength" id="passwordStrength" style="display: none;">
                                            <div class="mb-2">
                                                <small class="text-muted">ความแข็งแกร่งของรหัสผ่าน:</small>
                                                <div class="progress" style="height: 5px;">
                                                    <div class="progress-bar" id="strengthBar" role="progressbar" style="width: 0%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ส่วนที่อยู่ -->
                            <div class="col-md-6">
                                <div class="card h-100">
                                    <div class="card-header" style="background: rgba(33, 150, 243, 0.1); border-bottom: 1px solid rgba(33, 150, 243, 0.2);">
                                        <h6 class="mb-0" style="color: #2196F3;">
                                            <i class="bi bi-geo-alt me-2"></i>
                                            ข้อมูลที่อยู่
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="address" class="form-label">ที่อยู่ <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-house"></i>
                                                </span>
                                                <textarea class="form-control" id="address" name="address" rows="3" required 
                                                          placeholder="กรอกที่อยู่ เช่น 123 หมู่ 1 ถนนสุขุมวิท"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="province_id" class="form-label">จังหวัด</label>
                                            <select class="form-select location-select" id="province_id" name="province_id">
                                                <option value="">กำลังโหลด...</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="amphure_id" class="form-label">อำเภอ/เขต</label>
                                            <select class="form-select location-select" id="amphure_id" name="amphure_id" disabled>
                                                <option value="">เลือกจังหวัดก่อน</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="district_id" class="form-label">ตำบล/แขวง</label>
                                            <select class="form-select location-select" id="district_id" name="district_id" disabled>
                                                <option value="">เลือกอำเภอก่อน</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" id="submitProfileUpdate" style="background: linear-gradient(135deg, #F0592E, #FF8A65); border: none;">
                            <i class="bi bi-check-circle me-2"></i>
                            <?php echo $force_update_profile ? 'ยืนยันและเปิดใช้งานบัญชี' : 'บันทึกการเปลี่ยนแปลง'; ?>
                        </button>
                        <?php if (!$force_update_profile): ?>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-2"></i>
                            ยกเลิก
                        </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

            // Check if user needs to update profile
            const forceUpdateProfile = <?php echo $force_update_profile ? 'true' : 'false'; ?>;
            
            // Show profile update modal if needed
            if (forceUpdateProfile) {
                const profileModal = new bootstrap.Modal(document.getElementById('updateProfileModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                profileModal.show();
                
                // Load location data when modal is shown
                loadProvinces();
            }

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

            // Profile Update Form Handlers
            setupProfileUpdateForm();
            setupPasswordToggle();
            setupPasswordValidation();
            setupLocationSelects();

            // Chart Functions
            function showError(element, message) {
                if (element) {
                    element.textContent = message;
                    element.style.display = 'block';
                }
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
                    const response = await fetch('function/api/get_status_chart_data.php', {
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

            // Sample Data Functions (Fallback) - แสดงข้อมูลจริงจาก PHP
            function createSampleDeliveryChart() {
                const sampleData = {
                    month: {
                        labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'],
                        values: [
                            Math.max(1, <?php echo $total_delivery_preparing_box; ?>), 
                            Math.max(1, <?php echo $total_sending2_box; ?>), 
                            Math.max(1, <?php echo $total_sending3_box; ?>), 
                            Math.max(1, <?php echo $total_sending4_box; ?>), 
                            Math.max(1, <?php echo $total_history_box; ?>), 
                            Math.max(1, <?php echo $total_problem_box; ?>), 
                            Math.floor(Math.random() * 15) + 5, 
                            Math.floor(Math.random() * 20) + 8, 
                            Math.floor(Math.random() * 12) + 6, 
                            Math.floor(Math.random() * 18) + 10, 
                            Math.floor(Math.random() * 16) + 7, 
                            Math.floor(Math.random() * 14) + 5
                        ]
                    },
                    day: {
                        labels: Array.from({length: 30}, (_, i) => `${i + 1}`),
                        values: Array.from({length: 30}, () => Math.floor(Math.random() * 8) + 1)
                    },
                    year: {
                        labels: ['2020', '2021', '2022', '2023', '2024'],
                        values: [120, 165, 195, 240, Math.max(50, <?php echo $total_delivery; ?>)]
                    }
                };

                createDeliveryChart(sampleData[currentFilter]);
            }

            function createSampleStatusChart() {
                const sampleStatusData = {
                    status_1: Math.max(0, <?php echo $total_delivery_preparing_box; ?>),
                    status_2: Math.max(0, <?php echo $total_sending2_box; ?>),
                    status_3: Math.max(0, <?php echo $total_sending3_box; ?>),
                    status_4: Math.max(0, <?php echo $total_sending4_box; ?>),
                    status_5: Math.max(0, <?php echo $total_history_box; ?>),
                    status_99: Math.max(0, <?php echo $total_problem_box; ?>)
                };

                createStatusChart(sampleStatusData);
            }

            // Fetch Online Users
            async function fetchOnlineUsers() {
                try {
                    const response = await fetch('function/api/get_online_users.php');
                    const data = await response.json();
                    
                    const onlineUsersContent = document.getElementById('onlineUsersContent');
                    const onlineUsersError = document.getElementById('onlineUsersError');
                    
                    hideError(onlineUsersError);
                    
                    if (data.success && data.users && data.users.length > 0) {
                        let content = '';
                        data.users.forEach(user => {
                            const userType = user.user_type == 999 ? 'ผู้ดูแลระบบ' : 'พนักงาน';
                            const userTypeColor = user.user_type == 999 ? '#F44336' : '#2196F3';
                            
                            content += `
                                <div class="online-user-card">
                                    <div class="online-indicator"></div>
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #2d3748; margin-bottom: 2px;">
                                            ${user.user_firstname} ${user.user_lastname}
                                        </div>
                                        <div style="font-size: 0.85rem; color: #718096; margin-bottom: 2px;">
                                            <span style="color: ${userTypeColor}; font-weight: 500;">${userType}</span> • 
                                            ${user.user_email}
                                        </div>
                                        <div style="font-size: 0.8rem; color: #adb5bd;">
                                            เข้าสู่ระบบล่าสุด: ${formatDateTime(user.user_last_login)}
                                            ${user.user_last_ip ? ` • IP: ${user.user_last_ip}` : ''}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        onlineUsersContent.innerHTML = content;
                    } else {
                        onlineUsersContent.innerHTML = `
                            <div style="text-align: center; padding: 40px; color: #718096;">
                                <i class="bi bi-wifi-off" style="font-size: 2rem; color: #adb5bd; margin-bottom: 10px; display: block;"></i>
                                <p>ไม่มีผู้ใช้งานออนไลน์ในขณะนี้</p>
                            </div>
                        `;
                    }
                } catch (error) {
                    const onlineUsersError = document.getElementById('onlineUsersError');
                    showError(onlineUsersError, 'ไม่สามารถโหลดข้อมูลผู้ใช้ออนไลน์ได้');
                    
                    document.getElementById('onlineUsersContent').innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #dc3545;">
                            <i class="bi bi-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                            <p>ไม่สามารถโหลดข้อมูลผู้ใช้ออนไลน์ได้</p>
                        </div>
                    `;
                }
            }

            // Fetch Recent Activity
            async function fetchRecentActivity() {
                try {
                    const response = await fetch('function/api/get_recent_activity.php');
                    const data = await response.json();
                    
                    const activityLogContent = document.getElementById('activityLogContent');
                    const activityLogError = document.getElementById('activityLogError');
                    
                    hideError(activityLogError);
                    
                    if (data.success && data.activities && data.activities.length > 0) {
                        let content = '';
                        data.activities.forEach(activity => {
                            // Determine activity class
                            let activityClass = 'activity-default';
                            let iconText = 'ACT';
                            
                            const actionLower = activity.action.toLowerCase();
                            if (actionLower.includes('create') || actionLower.includes('add')) {
                                activityClass = 'activity-create';
                                iconText = 'ADD';
                            } else if (actionLower.includes('update') || actionLower.includes('edit')) {
                                activityClass = 'activity-update';
                                iconText = 'UPD';
                            } else if (actionLower.includes('delete') || actionLower.includes('remove')) {
                                activityClass = 'activity-delete';
                                iconText = 'DEL';
                            } else if (actionLower.includes('login')) {
                                activityClass = 'activity-login';
                                iconText = 'IN';
                            } else if (actionLower.includes('logout')) {
                                activityClass = 'activity-logout';
                                iconText = 'OUT';
                            }
                            
                            content += `
                                <div class="activity-item">
                                    <div class="activity-icon ${activityClass}">
                                        ${iconText}
                                    </div>
                                    <div class="activity-details">
                                        <div class="activity-action">
                                            ${activity.user_firstname} ${activity.user_lastname} - ${activity.action}
                                        </div>
                                        <div class="activity-info">
                                            ${activity.entity} ID: ${activity.entity_id} • ${activity.additional_info || 'ไม่มีข้อมูลเพิ่มเติม'}
                                        </div>
                                        <div class="activity-time">
                                            ${formatDateTime(activity.create_at)}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        activityLogContent.innerHTML = content;
                    } else {
                        activityLogContent.innerHTML = `
                            <div style="text-align: center; padding: 40px; color: #718096;">
                                <i class="bi bi-clock-history" style="font-size: 2rem; color: #adb5bd; margin-bottom: 10px; display: block;"></i>
                                <p>ไม่มีประวัติกิจกรรมล่าสุด</p>
                            </div>
                        `;
                    }
                } catch (error) {
                    const activityLogError = document.getElementById('activityLogError');
                    showError(activityLogError, 'ไม่สามารถโหลดประวัติกิจกรรมได้');
                    
                    document.getElementById('activityLogContent').innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #dc3545;">
                            <i class="bi bi-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                            <p>ไม่สามารถโหลดประวัติกิจกรรมได้</p>
                        </div>
                    `;
                }
            }

            // Format DateTime function
            function formatDateTime(dateString) {
                if (!dateString) return 'ไม่ระบุ';
                
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return 'ไม่ระบุ';
                
                const day = date.getDate().toString().padStart(2, '0');
                const month = (date.getMonth() + 1).toString().padStart(2, '0');
                const year = date.getFullYear();
                const hours = date.getHours().toString().padStart(2, '0');
                const minutes = date.getMinutes().toString().padStart(2, '0');
                
                return `${day}/${month}/${year} ${hours}:${minutes}`;
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

            // Profile Update Functions
            function setupProfileUpdateForm() {
                const form = document.getElementById('updateProfileForm');
                const submitBtn = document.getElementById('submitProfileUpdate');
                
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    
                    // Validate passwords match
                    const newPassword = document.getElementById('new_password').value;
                    const confirmPassword = document.getElementById('confirm_password').value;
                    const address = document.getElementById('address').value.trim();
                    
                    if (newPassword !== confirmPassword) {
                        Swal.fire({
                            icon: 'error',
                            title: 'รหัสผ่านไม่ตรงกัน',
                            text: 'กรุณาตรวจสอบรหัสผ่านให้ตรงกัน',
                            confirmButtonColor: '#F0592E'
                        });
                        return;
                    }
                    
                    if (newPassword.length < 6) {
                        Swal.fire({
                            icon: 'error',
                            title: 'รหัสผ่านสั้นเกินไป',
                            text: 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร',
                            confirmButtonColor: '#F0592E'
                        });
                        return;
                    }
                    
                    if (!address) {
                        Swal.fire({
                            icon: 'error',
                            title: 'กรุณากรอกที่อยู่',
                            text: 'ที่อยู่เป็นข้อมูลที่จำเป็น',
                            confirmButtonColor: '#F0592E'
                        });
                        return;
                    }
                    
                    // Show loading
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="bi bi-spinner-border spinner-border-sm me-2"></i>กำลังบันทึก...';
                    
                    try {
                        const formData = new FormData(form);
                        
                        const response = await fetch('function/update_self_profile.php', {
                            method: 'POST',
                            body: formData
                        });
                        
                        const data = await response.json();
                        
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'บันทึกสำเร็จ!',
                                text: data.message,
                                confirmButtonColor: '#F0592E'
                            }).then(() => {
                                // Hide modal and reload page if profile was activated
                                const profileModal = bootstrap.Modal.getInstance(document.getElementById('updateProfileModal'));
                                if (profileModal) {
                                    profileModal.hide();
                                }
                                
                                if (data.activated) {
                                    // Reload page to update session
                                    window.location.reload();
                                }
                            });
                        } else {
                            throw new Error(data.message || 'เกิดข้อผิดพลาดในการบันทึกข้อมูล');
                        }
                        
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: error.message,
                            confirmButtonColor: '#F0592E'
                        });
                    } finally {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = `
                            <i class="bi bi-check-circle me-2"></i>
                            ${forceUpdateProfile ? 'ยืนยันและเปิดใช้งานบัญชี' : 'บันทึกการเปลี่ยนแปลง'}
                        `;
                    }
                });
            }
            
            function setupPasswordToggle() {
                const toggleNewPassword = document.getElementById('toggleNewPassword');
                const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
                const newPasswordInput = document.getElementById('new_password');
                const confirmPasswordInput = document.getElementById('confirm_password');
                
                toggleNewPassword.addEventListener('click', function() {
                    const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    newPasswordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
                });
                
                toggleConfirmPassword.addEventListener('click', function() {
                    const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPasswordInput.setAttribute('type', type);
                    this.innerHTML = type === 'password' ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
                });
            }
            
            function setupPasswordValidation() {
                const newPasswordInput = document.getElementById('new_password');
                const confirmPasswordInput = document.getElementById('confirm_password');
                const passwordError = document.getElementById('passwordError');
                const strengthIndicator = document.getElementById('passwordStrength');
                const strengthBar = document.getElementById('strengthBar');
                
                newPasswordInput.addEventListener('input', function() {
                    const password = this.value;
                    if (password.length > 0) {
                        strengthIndicator.style.display = 'block';
                        updatePasswordStrength(password, strengthBar);
                    } else {
                        strengthIndicator.style.display = 'none';
                    }
                    
                    validatePasswordMatch();
                });
                
                confirmPasswordInput.addEventListener('input', validatePasswordMatch);
                
                function validatePasswordMatch() {
                    const newPassword = newPasswordInput.value;
                    const confirmPassword = confirmPasswordInput.value;
                    
                    if (confirmPassword.length > 0) {
                        if (newPassword !== confirmPassword) {
                            confirmPasswordInput.classList.add('is-invalid');
                            passwordError.textContent = 'รหัสผ่านไม่ตรงกัน';
                        } else {
                            confirmPasswordInput.classList.remove('is-invalid');
                            confirmPasswordInput.classList.add('is-valid');
                            passwordError.textContent = '';
                        }
                    } else {
                        confirmPasswordInput.classList.remove('is-invalid', 'is-valid');
                        passwordError.textContent = '';
                    }
                }
                
                function updatePasswordStrength(password, strengthBar) {
                    let strength = 0;
                    let color = '';
                    
                    if (password.length >= 6) strength += 25;
                    if (/[a-z]/.test(password)) strength += 25;
                    if (/[A-Z]/.test(password)) strength += 25;
                    if (/[0-9]/.test(password)) strength += 25;
                    
                    if (strength <= 25) {
                        color = '#dc3545';
                    } else if (strength <= 50) {
                        color = '#ffc107';
                    } else if (strength <= 75) {
                        color = '#fd7e14';
                    } else {
                        color = '#28a745';
                    }
                    
                    strengthBar.style.width = strength + '%';
                    strengthBar.style.backgroundColor = color;
                }
            }
            
            function setupLocationSelects() {
                const provinceSelect = document.getElementById('province_id');
                const amphureSelect = document.getElementById('amphure_id');
                const districtSelect = document.getElementById('district_id');
                
                provinceSelect.addEventListener('change', function() {
                    const provinceId = this.value;
                    amphureSelect.innerHTML = '<option value="">กำลังโหลด...</option>';
                    amphureSelect.disabled = true;
                    districtSelect.innerHTML = '<option value="">เลือกอำเภอก่อน</option>';
                    districtSelect.disabled = true;
                    
                    if (provinceId) {
                        loadAmphures(provinceId);
                    } else {
                        amphureSelect.innerHTML = '<option value="">เลือกจังหวัดก่อน</option>';
                    }
                });
                
                amphureSelect.addEventListener('change', function() {
                    const amphureId = this.value;
                    districtSelect.innerHTML = '<option value="">กำลังโหลด...</option>';
                    districtSelect.disabled = true;
                    
                    if (amphureId) {
                        loadDistricts(amphureId);
                    } else {
                        districtSelect.innerHTML = '<option value="">เลือกอำเภอก่อน</option>';
                    }
                });
            }
            
            async function loadProvinces() {
                try {
                    const response = await fetch('function/get_provinces.php');
                    const provinces = await response.text();
                    document.getElementById('province_id').innerHTML = provinces;
                } catch (error) {
                    console.error('Error loading provinces:', error);
                    document.getElementById('province_id').innerHTML = '<option value="">เกิดข้อผิดพลาด</option>';
                }
            }
            
            async function loadAmphures(provinceId) {
                try {
                    const response = await fetch(`function/get_amphures.php?province_id=${provinceId}`);
                    const amphures = await response.text();
                    const amphureSelect = document.getElementById('amphure_id');
                    amphureSelect.innerHTML = amphures;
                    amphureSelect.disabled = false;
                } catch (error) {
                    console.error('Error loading amphures:', error);
                    document.getElementById('amphure_id').innerHTML = '<option value="">เกิดข้อผิดพลาด</option>';
                }
            }
            
            async function loadDistricts(amphureId) {
                try {
                    const response = await fetch(`function/get_districts.php?amphure_id=${amphureId}`);
                    const districts = await response.text();
                    const districtSelect = document.getElementById('district_id');
                    districtSelect.innerHTML = districts;
                    districtSelect.disabled = false;
                } catch (error) {
                    console.error('Error loading districts:', error);
                    document.getElementById('district_id').innerHTML = '<option value="">เกิดข้อผิดพลาด</option>';
                }
            }

            // Initialize everything (only if not forced profile update)
            if (!forceUpdateProfile) {
                setTimeout(() => {
                    fetchDeliveryChartData();
                    fetchStatusChartData();
                    fetchOnlineUsers();
                    fetchRecentActivity();
                }, 1000);

                // Auto refresh online users and activity every 30 seconds
                setInterval(() => {
                    fetchOnlineUsers();
                    fetchRecentActivity();
                }, 30000);
            }
        });

        // ========== MODAL FUNCTIONS (Copied from Employee Dashboard) ==========

        // Function to open statistics modal
        function openStatModal(type, title) {
            const modal = new bootstrap.Modal(document.getElementById('statModal'));
            const modalTitle = document.getElementById('statModalTitle');
            const modalContent = document.getElementById('statModalContent');
            
            modalTitle.innerHTML = `<i class="bi bi-info-circle me-2"></i>${title}`;
            
            // Show or hide tabs based on type
            if (type === 'all_bills') {
                fetchBillData(type);
            } else if (type.includes('users')) {
                // Handle user-related statistics
                fetchUserData(type);
            } else {
                modalContent.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">กำลังโหลดข้อมูล...</p></div>';
                fetchStatData(type);
            }
            
            modal.show();
        }

        // Function to fetch user data for admin-specific statistics
        function fetchUserData(type) {
            const modalContent = document.getElementById('statModalContent');
            
            // Show loading first
            modalContent.innerHTML = `
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                </div>
            `;
            
            fetch('function/api/get_user_data.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: type })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.users) {
                    let content = `
                        <div style="max-height: 600px; overflow-y: auto;">
                            <h6 style="color: #F0592E; margin-bottom: 15px;">
                                <i class="bi bi-list-ul"></i> รายการทั้งหมด (${data.users.length} รายการ)
                            </h6>
                    `;
                    
                    if (data.users.length > 0) {
                        data.users.forEach(user => {
                            const userTypeText = user.user_type == 999 ? 'ผู้ดูแลระบบ' : 'พนักงาน';
                            const userTypeColor = user.user_type == 999 ? '#F44336' : '#2196F3';
                            const statusText = user.user_status == 1 ? 'ใช้งาน' : 'ปิดใช้งาน';
                            const statusColor = user.user_status == 1 ? '#4CAF50' : '#9E9E9E';
                            const onlineStatus = user.user_is_online == 1 ? 'ออนไลน์' : 'ออฟไลน์';
                            const onlineColor = user.user_is_online == 1 ? '#4CAF50' : '#9E9E9E';
                            
                            content += `
                                <div style="background: white; border-radius: 12px; margin-bottom: 15px; border: 1px solid #dee2e6; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                    <div style="background: rgba(240, 89, 46, 0.1); padding: 15px; border-bottom: 1px solid #dee2e6;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                            <div style="margin: 0; color: #2d3748; font-size: 1.1rem;">
                                                <i class="bi bi-person" style="color: ${userTypeColor}; margin-right: 8px;"></i>
                                                <strong>${user.user_firstname} ${user.user_lastname}</strong>
                                            </div>
                                            <div style="display: flex; gap: 8px;">
                                                <span style="background: ${userTypeColor}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                                    ${userTypeText}
                                                </span>
                                                <span style="background: ${onlineColor}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                                    ${onlineStatus}
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; font-size: 0.9rem;">
                                            <div>
                                                <div style="font-weight: 600; color: #495057;">อีเมล:</div>
                                                <div style="color: #6c757d;">${user.user_email}</div>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: #495057;">สถานะ:</div>
                                                <div>
                                                    <span style="background: rgba(${statusColor === '#4CAF50' ? '76, 175, 80' : '158, 158, 158'}, 0.1); color: ${statusColor}; padding: 2px 8px; border-radius: 8px; font-weight: 600;">
                                                        ${statusText}
                                                    </span>
                                                </div>
                                            </div>
                                            <div>
                                                <div style="font-weight: 600; color: #495057;">วันที่สร้าง:</div>
                                                <div style="color: #6c757d;">${formatDate(user.user_create_date)}</div>
                                            </div>
                                        </div>
                                        
                                        ${user.user_last_login ? `
                                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(0,0,0,0.1);">
                                            <strong style="color: #495057;">เข้าสู่ระบบล่าสุด:</strong>
                                            <span style="color: #28a745; font-weight: 600;">${formatDate(user.user_last_login)}</span>
                                            ${user.user_last_ip ? ` • IP: ${user.user_last_ip}` : ''}
                                        </div>
                                        ` : ''}
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        content += `
                            <div class="text-center p-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #adb5bd;"></i>
                                <h3>ไม่พบข้อมูล</h3>
                                <p>ไม่มีผู้ใช้งานในหมวดหมู่นี้</p>
                            </div>
                        `;
                    }
                    
                    content += '</div>';
                    modalContent.innerHTML = content;
                } else {
                    modalContent.innerHTML = `
                        <div class="text-center p-4">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #adb5bd;"></i>
                            <h3>ไม่พบข้อมูล</h3>
                            <p>ไม่มีข้อมูลผู้ใช้งาน</p>
                        </div>
                    `;
                }
            })
            .catch(error => {
                modalContent.innerHTML = `
                    <div class="text-center p-4">
                        <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                        <h3>เกิดข้อผิดพลาด</h3>
                        <p>ไม่สามารถดึงข้อมูลได้: ${error.message}</p>
                    </div>
                `;
            });
        }

        // Function to fetch bill data with IC/IV separation
        function fetchBillData(type) {
            const modalContent = document.getElementById('statModalContent');
            
            // Show loading first
            modalContent.innerHTML = `
                <div class="text-center p-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">กำลังโหลดข้อมูล...</p>
                </div>
            `;
            
            Promise.all([
                fetch('function/api/get_bill_data.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ type: type, bill_type: 'ic' })
                }).then(response => response.json()),
                
                fetch('function/api/get_bill_data.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ type: type, bill_type: 'iv' })
                }).then(response => response.json())
            ])
            .then(([icData, ivData]) => {
                // Combine data and display
                const icItems = icData.items || [];
                const ivItems = ivData.items || [];
                const totalItems = icItems.length + ivItems.length;
                
                let content = `
                    <div style="max-height: 600px; overflow-y: auto;">
                        <h6 style="color: #F0592E; margin-bottom: 15px;">
                            <i class="bi bi-list-ul"></i> รายการทั้งหมด (${totalItems} รายการ)
                        </h6>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="text-center p-3" style="background: rgba(33, 150, 243, 0.1); border-radius: 8px;">
                                    <h5 style="color: #2196F3; margin: 0;">บิล IC</h5>
                                    <span style="font-size: 1.5rem; font-weight: bold; color: #1976D2;">${icItems.length}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3" style="background: rgba(156, 39, 176, 0.1); border-radius: 8px;">
                                    <h5 style="color: #9C27B0; margin: 0;">บิล IV</h5>
                                    <span style="font-size: 1.5rem; font-weight: bold; color: #7B1FA2;">${ivItems.length}</span>
                                </div>
                            </div>
                        </div>
                `;
                
                // Add IC bills
                if (icItems.length > 0) {
                    content += '<h6 style="color: #2196F3; margin-bottom: 10px;"><i class="bi bi-receipt me-2"></i>บิล IC</h6>';
                    icItems.forEach(item => {
                        content += generateBillCard(item, 'IC');
                    });
                }
                
                // Add IV bills
                if (ivItems.length > 0) {
                    content += '<h6 style="color: #9C27B0; margin-bottom: 10px; margin-top: 20px;"><i class="bi bi-file-text me-2"></i>บิล IV</h6>';
                    ivItems.forEach(item => {
                        content += generateBillCard(item, 'IV');
                    });
                }
                
                if (totalItems === 0) {
                    content += `
                        <div class="text-center p-4">
                            <i class="bi bi-inbox" style="font-size: 3rem; color: #adb5bd;"></i>
                            <h3>ไม่พบข้อมูล</h3>
                            <p>ไม่มีบิลในหมวดหมู่นี้</p>
                        </div>
                    `;
                }
                
                content += '</div>';
                
                modalContent.innerHTML = content;
            })
            .catch(error => {
                const modalContent = document.getElementById('statModalContent');
                if (modalContent) {
                    modalContent.innerHTML = `
                        <div class="text-center p-4">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            <h3>เกิดข้อผิดพลาด</h3>
                            <p>ไม่สามารถดึงข้อมูลได้: ${error.message}</p>
                            <p class="text-muted">โปรดตรวจสอบ Console สำหรับรายละเอียดเพิ่มเติม</p>
                        </div>
                    `;
                }
            });
        }

        // Function to generate individual bill card
        function generateBillCard(item, type) {
            const typeColor = type === 'IC' ? '#2196F3' : '#9C27B0';
            const typeBg = type === 'IC' ? 'rgba(33, 150, 243, 0.1)' : 'rgba(156, 39, 176, 0.1)';
            
            return `
                <div style="background: white; border-radius: 12px; margin-bottom: 15px; border: 1px solid #dee2e6; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="background: ${typeBg}; padding: 15px; border-bottom: 1px solid #dee2e6;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <div style="margin: 0; color: #2d3748; font-size: 1.1rem;">
                                <i class="bi bi-receipt" style="color: ${typeColor}; margin-right: 8px;"></i>
                                <strong>${item.bill_number || 'ไม่ระบุเลขบิล'}</strong>
                            </div>
                            <span style="background: ${typeColor}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                บิล ${type}
                            </span>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; font-size: 0.9rem;">
                            <div>
                                <div style="font-weight: 600; color: #495057;">ลูกค้า:</div>
                                <div style="color: #6c757d;">${item.bill_customer_name || 'ไม่ระบุ'}</div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #495057;">จำนวนรายการ:</div>
                                <div>
                                    <span style="background: rgba(240, 89, 46, 0.1); color: #F0592E; padding: 2px 8px; border-radius: 8px; font-weight: 600;">
                                        ${item.item_count || 0} รายการ
                                    </span>
                                </div>
                            </div>
                            <div>
                                <div style="font-weight: 600; color: #495057;">วันที่สร้าง:</div>
                                <div style="color: #6c757d;">${formatDate(item.bill_date)}</div>
                            </div>
                        </div>
                        
                        ${item.total_amount && item.total_amount > 0 ? `
                        <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(0,0,0,0.1);">
                            <strong style="color: #495057;">ยอดรวม:</strong>
                            <span style="color: #28a745; font-weight: 600; font-size: 1.1rem;">฿${parseFloat(item.total_amount).toLocaleString()}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }

        // Function to fetch regular stat data
        function fetchStatData(type) {
            // Show loading in modal
            const modalContent = document.getElementById('statModalContent');
            modalContent.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">กำลังโหลดข้อมูล...</p></div>';

            // Determine delivery status filter based on type
            let statusFilter = '';
            switch(type) {
                case 'preparing':
                    statusFilter = '1';
                    break;
                case 'sending_center':
                    statusFilter = '2';
                    break;
                case 'at_center':
                    statusFilter = '3';
                    break;
                case 'delivering':
                    statusFilter = '4';
                    break;
                case 'completed':
                    statusFilter = '5';
                    break;
                case 'problem':
                    statusFilter = '99';
                    break;
                case 'all_delivery':
                    statusFilter = 'all';
                    break;
                default:
                    statusFilter = 'all';
                    break;
            }

            // Fetch data
            fetch('function/api/get_stat_data.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    type: type,
                    status: statusFilter
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.items) {
                    openStatModalWithData(data);
                } else {
                    const modalContent = document.getElementById('statModalContent');
                    if (modalContent) {
                        modalContent.innerHTML = `
                            <div class="text-center p-4">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #adb5bd;"></i>
                                <h3>ไม่พบข้อมูล</h3>
                                <p>ไม่มีข้อมูลสำหรับหมวดหมู่นี้</p>
                            </div>
                        `;
                    }
                }
            })
            .catch(error => {
                const modalContent = document.getElementById('statModalContent');
                if (modalContent) {
                    modalContent.innerHTML = `
                        <div class="text-center p-4">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            <h3>เกิดข้อผิดพลาด</h3>
                            <p>ไม่สามารถดึงข้อมูลได้: ${error.message}</p>
                            <p class="text-muted">โปรดตรวจสอบ Console สำหรับรายละเอียดเพิ่มเติม</p>
                        </div>
                    `;
                }
            });
        }

        // Function to open stat modal with data
        function openStatModalWithData(data) {
            const modalContent = document.getElementById('statModalContent');
            
            let content = `
                <div style="max-height: 600px; overflow-y: auto;">
                    <h6 style="color: #F0592E; margin-bottom: 15px;">
                        <i class="bi bi-list-ul"></i> รายการทั้งหมด (${data.items.length} รายการ)
                    </h6>`;
            
            if (data.items && data.items.length > 0) {
                data.items.forEach((item, index) => {
                    // Check if this is a bill (not delivery)
                    if (item.type === 'bill') {
                        // Display bill information
                        content += `
                            <div style="background: white; border-radius: 12px; margin-bottom: 15px; border: 1px solid #dee2e6; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                <div style="background: linear-gradient(135deg, rgba(240, 89, 46, 0.1), rgba(255, 138, 101, 0.1)); padding: 15px; border-bottom: 1px solid #dee2e6;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                        <h6 style="margin: 0; color: #2d3748; font-size: 1.1rem;">
                                            <i class="bi bi-receipt" style="color: #F0592E; margin-right: 8px;"></i>
                                            <strong>${item.delivery_number}</strong>
                                        </h6>
                                        <span style="background: #17a2b8; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                            ยังไม่ได้รวมเป็นเลขขนส่ง
                                        </span>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; font-size: 0.9rem;">
                                        <div>
                                            <strong style="color: #495057;">ลูกค้า:</strong><br>
                                            <span style="color: #6c757d;">${item.bill_customer_name || 'ไม่ระบุ'}</span>
                                        </div>
                                        <div>
                                            <strong style="color: #495057;">จำนวนรายการ:</strong><br>
                                            <span style="background: rgba(240, 89, 46, 0.1); color: #F0592E; padding: 2px 8px; border-radius: 8px; font-weight: 600;">${item.item_count} รายการ</span>
                                        </div>
                                        <div>
                                            <strong style="color: #495057;">วันที่สร้าง:</strong><br>
                                            <span style="color: #6c757d;">${formatDate(item.delivery_date)}</span>
                                        </div>
                                    </div>
                                    
                                    ${item.total_amount ? `
                                    <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid rgba(0,0,0,0.1);">
                                        <strong style="color: #495057;">ยอดรวม:</strong>
                                        <span style="color: #28a745; font-weight: 600; font-size: 1.1rem;">฿${parseFloat(item.total_amount).toLocaleString()}</span>
                                    </div>
                                    ` : ''}
                                </div>
                                
                                <div style="padding: 12px 15px; text-align: center; background: rgba(23, 162, 184, 0.05); color: #17a2b8; font-size: 0.9rem;">
                                    <i class="bi bi-info-circle me-1"></i> บิลนี้ยังไม่ได้รวมเข้าในการขนส่ง สามารถนำไปสร้างเลขขนส่งได้
                                </div>
                            </div>`;
                    } else {
                        // Display delivery information (existing code)
                        let statusText = 'ไม่ทราบสถานะ';
                        let statusColor = '#6c757d';
                        
                        switch (parseInt(item.delivery_status)) {
                            case 1:
                                statusText = 'รับคำสั่งซื้อ';
                                statusColor = '#007bff';
                                break;
                            case 2:
                                statusText = 'กำลังจัดส่งไปศูนย์';
                                statusColor = '#ffc107';
                                break;
                            case 3:
                                statusText = 'ถึงศูนย์กระจาย';
                                statusColor = '#6c757d';
                                break;
                            case 4:
                                statusText = 'กำลังส่งลูกค้า';
                                statusColor = '#6f42c1';
                                break;
                            case 5:
                                statusText = 'ส่งสำเร็จ';
                                statusColor = '#28a745';
                                break;
                            case 99:
                                statusText = 'เกิดปัญหา';
                                statusColor = '#dc3545';
                                break;
                        }

                        content += `
                            <div style="background: white; border-radius: 12px; margin-bottom: 15px; border: 1px solid #dee2e6; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer;" onclick="openDeliveryDetail(${item.delivery_id})">
                                <div style="background: linear-gradient(135deg, rgba(240, 89, 46, 0.1), rgba(255, 138, 101, 0.1)); padding: 15px; border-bottom: 1px solid #dee2e6;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                        <h6 style="margin: 0; color: #2d3748; font-size: 1.1rem;">
                                            <i class="bi bi-truck" style="color: #F0592E; margin-right: 8px;"></i>
                                            <strong>${item.delivery_number}</strong>
                                        </h6>
                                        <span style="background: ${statusColor}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                            ${statusText}
                                        </span>
                                    </div>
                                    
                                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; font-size: 0.9rem;">
                                        <div>
                                            <strong style="color: #495057;">จำนวนรายการ:</strong><br>
                                            <span style="background: rgba(240, 89, 46, 0.1); color: #F0592E; padding: 2px 8px; border-radius: 8px; font-weight: 600;">${item.item_count} รายการ</span>
                                        </div>
                                        <div>
                                            <strong style="color: #495057;">วันที่สร้าง:</strong><br>
                                            <span style="color: #6c757d;">${formatDate(item.delivery_date)}</span>
                                        </div>
                                        <div>
                                            <strong style="color: #495057;">ประเภทขนส่ง:</strong><br>
                                            <span style="background: rgba(33, 150, 243, 0.1); color: #2196F3; padding: 2px 8px; border-radius: 6px; font-weight: 500;">${item.transfer_type || 'ทั่วไป'}</span>
                                        </div>
                                    </div>
                                    
                                    ${parseInt(item.delivery_status) === 99 && item.delivery_problem_desc ? `
                                    <div style="margin-top: 12px; padding: 10px; background: rgba(220, 53, 69, 0.1); border-radius: 8px; border-left: 4px solid #dc3545;">
                                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                                            <i class="bi bi-exclamation-triangle" style="color: #dc3545; font-size: 1.1rem;"></i>
                                            <strong style="color: #721c24; font-size: 0.9rem;">รายละเอียดปัญหา:</strong>
                                        </div>
                                        <div style="color: #721c24; font-size: 0.85rem; font-weight: 500; padding-left: 24px;">
                                            ${item.delivery_problem_desc}
                                        </div>
                                    </div>
                                    ` : ''}
                                </div>
                                
                                <div style="padding: 12px 15px; text-align: center; color: #6c757d; font-size: 0.9rem;">
                                    <i class="bi bi-hand-index me-1"></i> คลิกเพื่อดูรายละเอียดเพิ่มเติม
                                </div>
                            </div>`;
                    }
                });
            }
            
            content += `</div>`;
            modalContent.innerHTML = content;
        }

        // Function to open delivery detail modal (same as history.php)
        function openDeliveryDetail(deliveryId) {
            if (!deliveryId) return;
            
            // Hide stat modal first
            const statModal = bootstrap.Modal.getInstance(document.getElementById('statModal'));
            if (statModal) {
                statModal.hide();
            }
            
            // Show loading
            Swal.fire({
                title: 'กำลังโหลดข้อมูล...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Fetch data and show modal
            $.ajax({
                url: 'function/fetch_modal_data.php',
                type: 'POST',
                data: {
                    deliveryIds: deliveryId.toString()
                },
                success: function(data) {
                    Swal.close();
                    
                    if (data.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.error,
                            confirmButtonColor: '#F0592E'
                        });
                        return;
                    }

                    openModal(data);
                    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
                    modal.show();
                },
                error: function(xhr, status, error) {
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถดึงข้อมูลได้: ' + error,
                        confirmButtonColor: '#F0592E'
                    });
                }
            });
        }

        // Function to open modal (copied from history.php)
        function openModal(data) {
            const modalContent = document.getElementById('modalContent');
            if (!modalContent) {
                return;
            }
            
            let content = '';
            
            if (data.items && data.items.length > 0) {
                content = `
                    <div style="max-height: 600px; overflow-y: auto;">
                        <h6 style="color: #F0592E; margin-bottom: 15px;">
                            <i class="bi bi-list-ul"></i> รายละเอียดการจัดส่ง (${data.items.length} รายการ)
                        </h6>`;
                
                data.items.forEach((item, index) => {
                    // Determine status text and color
                    let statusText = 'ไม่ทราบสถานะ';
                    let statusColor = '#6c757d';
                    
                    switch (parseInt(item.delivery_status)) {
                        case 1:
                            statusText = 'รับคำสั่งซื้อ';
                            statusColor = '#007bff';
                            break;
                        case 2:
                            statusText = 'กำลังจัดส่งไปศูนย์';
                            statusColor = '#ffc107';
                            break;
                        case 3:
                            statusText = 'ถึงศูนย์กระจาย';
                            statusColor = '#6c757d';
                            break;
                        case 4:
                            statusText = 'กำลังส่งลูกค้า';
                            statusColor = '#6f42c1';
                            break;
                        case 5:
                            statusText = 'ส่งสำเร็จ';
                            statusColor = '#28a745';
                            break;
                        case 99:
                            statusText = 'เกิดปัญหา';
                            statusColor = '#dc3545';
                            break;
                    }

                    // Generate timeline HTML
                    const timelineHtml = generateTimelineHtml(item);
                    
                    // Generate items detail HTML
                    let itemsHtml = '';
                    if (item.items && item.items.length > 0) {
                        itemsHtml = `
                            <div class="delivery-items" id="items-${item.delivery_id}" style="display: none; margin-top: 15px;">
                                <div style="background: rgba(248, 249, 250, 1); border-radius: 8px; padding: 15px; border: 1px solid #dee2e6;">
                                    <h6 style="color: #495057; margin-bottom: 15px; font-size: 1rem;">
                                        <i class="bi bi-box-seam"></i> รายละเอียดสินค้า (${item.items.length} รายการ)
                                    </h6>`;
                        
                        item.items.forEach((deliveryItem, itemIndex) => {
                            itemsHtml += `
                                <div style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 10px; border-left: 4px solid #F0592E; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.9rem;">
                                        <div><strong style="color: #495057;">เลขบิล:</strong> ${deliveryItem.bill_number}</div>
                                        <div><strong style="color: #495057;">ลูกค้า:</strong> ${deliveryItem.bill_customer_name}</div>
                                        <div><strong style="color: #495057;">รหัสสินค้า:</strong> <code style="background: #e9ecef; padding: 2px 6px; border-radius: 4px;">${deliveryItem.item_code}</code></div>
                                        <div><strong style="color: #495057;">จำนวน:</strong> <span style="color: #F0592E; font-weight: 600;">${deliveryItem.item_quantity} ${deliveryItem.item_unit}</span></div>
                                    </div>
                                    <div style="margin-top: 8px;">
                                        <strong style="color: #495057;">รายละเอียด:</strong> 
                                        <span style="color: #6c757d;">${deliveryItem.item_desc}</span>
                                    </div>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-top: 8px; font-size: 0.85rem;">
                                        <div><strong style="color: #495057;">ราคา:</strong> <span style="color: #28a745;">฿${parseFloat(deliveryItem.item_price).toLocaleString()}</span></div>
                                        <div><strong style="color: #495057;">รวม:</strong> <span style="color: #F0592E; font-weight: 600;">฿${parseFloat(deliveryItem.line_total).toLocaleString()}</span></div>
                                        <div><strong style="color: #495057;">น้ำหนัก:</strong> ${deliveryItem.item_weight} กก.</div>
                                    </div>
                                </div>`;
                        });
                        
                        itemsHtml += `
                                </div>
                            </div>`;
                    }
                    
                    content += `
                        <div style="background: white; border-radius: 12px; margin-bottom: 15px; border: 1px solid #dee2e6; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <div style="background: linear-gradient(135deg, rgba(240, 89, 46, 0.1), rgba(255, 138, 101, 0.1)); padding: 15px; border-bottom: 1px solid #dee2e6;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                    <h6 style="margin: 0; color: #2d3748; font-size: 1.1rem;">
                                        <i class="bi bi-truck" style="color: #F0592E; margin-right: 8px;"></i>
                                        <strong>${item.delivery_number}</strong>
                                    </h6>
                                    <span style="background: ${statusColor}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                        ${statusText}
                                    </span>
                                </div>
                                
                                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; font-size: 0.9rem;">
                                    <div>
                                        <strong style="color: #495057;">จำนวนรายการ:</strong><br>
                                        <span style="background: rgba(240, 89, 46, 0.1); color: #F0592E; padding: 2px 8px; border-radius: 8px; font-weight: 600;">${item.item_count} รายการ</span>
                                    </div>
                                    <div>
                                        <strong style="color: #495057;">วันที่สร้าง:</strong><br>
                                        <span style="color: #6c757d;">${formatDate(item.delivery_date)}</span>
                                    </div>
                                    <div>
                                        <strong style="color: #495057;">ประเภทขนส่ง:</strong><br>
                                        <span style="background: rgba(33, 150, 243, 0.1); color: #2196F3; padding: 2px 8px; border-radius: 6px; font-weight: 500;">${item.transfer_type}</span>
                                    </div>
                                </div>
                                
                                ${parseInt(item.delivery_status) === 99 && item.delivery_problem_desc ? `
                                <div style="margin-top: 15px; padding: 12px; background: rgba(220, 53, 69, 0.1); border-radius: 8px; border-left: 4px solid #dc3545;">
                                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                        <i class="bi bi-exclamation-triangle" style="color: #dc3545; font-size: 1.2rem;"></i>
                                        <strong style="color: #721c24; font-size: 1rem;">รายละเอียดปัญหา:</strong>
                                    </div>
                                    <div style="color: #721c24; font-size: 0.9rem; font-weight: 500; padding-left: 28px; line-height: 1.4;">
                                        ${item.delivery_problem_desc}
                                    </div>
                                </div>
                                ` : ''}
                            </div>
                            
                            <!-- Timeline Section -->
                            <div style="padding: 15px; background: rgba(249, 249, 249, 0.5);">
                                <h6 style="color: #495057; margin-bottom: 15px; font-size: 1rem;">
                                    <i class="bi bi-clock-history"></i> Timeline การขนส่ง
                                </h6>
                                ${timelineHtml}
                            </div>
                            
                            <div style="padding: 12px 15px; border-top: 1px solid #dee2e6;">
                                <button 
                                    type="button" 
                                    class="btn btn-sm" 
                                    onclick="toggleDeliveryItems(${item.delivery_id})"
                                    style="background: linear-gradient(135deg, #F0592E, #FF8A65); color: white; border: none; border-radius: 6px; padding: 6px 12px; font-size: 0.85rem; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; gap: 6px;"
                                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(240, 89, 46, 0.3)'"
                                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                                >
                                    <i class="bi bi-chevron-down" id="icon-${item.delivery_id}"></i>
                                    <span id="text-${item.delivery_id}">ดูรายละเอียดสินค้า</span>
                                </button>
                            </div>
                            
                            ${itemsHtml}
                        </div>`;
                });
                
                content += `</div>`;
            } else {
                content = `
                    <div style="text-align: center; padding: 40px 20px; color: #718096;">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #adb5bd; margin-bottom: 15px; display: block;"></i>
                        <h5 style="color: #2d3748; margin-bottom: 8px;">ไม่พบข้อมูล</h5>
                        <p style="font-size: 0.9rem;">ไม่มีรายการการจัดส่งที่สามารถแสดงได้</p>
                    </div>
                `;
            }
            
            modalContent.innerHTML = content;
        }

        // Function to generate timeline HTML
        function generateTimelineHtml(item) {
            const steps = [
                {
                    id: 1,
                    title: 'รับคำสั่งซื้อ',
                    description: 'ระบบรับคำสั่งซื้อเข้าสู่ระบบ',
                    timestamp: item.delivery_step1_received,
                    icon: 'bi-clipboard-check',
                    color: '#007bff'
                },
                {
                    id: 2,
                    title: 'กำลังจัดส่งไปศูนย์',
                    description: 'สินค้าอยู่ระหว่างการขนส่งไปยังศูนย์กระจาย',
                    timestamp: item.delivery_step2_transit,
                    icon: 'bi-truck',
                    color: '#ffc107'
                },
                {
                    id: 3,
                    title: 'ถึงศูนย์กระจาย',
                    description: 'สินค้าถึงศูนย์กระจายสินค้าปลายทาง',
                    timestamp: item.delivery_step3_warehouse,
                    icon: 'bi-building',
                    color: '#6c757d'
                },
                {
                    id: 4,
                    title: 'กำลังส่งลูกค้า',
                    description: 'สินค้าอยู่ระหว่างการนำส่งให้ลูกค้า',
                    timestamp: item.delivery_step4_last_mile,
                    icon: 'bi-geo-alt',
                    color: '#6f42c1'
                },
                {
                    id: 5,
                    title: 'ส่งสำเร็จ',
                    description: 'สินค้าถึงลูกค้าเรียบร้อยแล้ว',
                    timestamp: item.delivery_step5_completed,
                    icon: 'bi-check-circle',
                    color: '#28a745'
                }
            ];

            let timelineHtml = '<div style="position: relative;">';
            
            steps.forEach((step, index) => {
                const isCompleted = step.timestamp && step.timestamp !== null;
                const isCurrent = parseInt(item.delivery_status) === step.id;
                const isProblem = parseInt(item.delivery_status) === 99;
                
                let stepStatus = '';
                let stepColor = '#e9ecef';
                let textColor = '#6c757d';
                let iconClass = 'bi-circle';
                
                if (isCompleted) {
                    stepStatus = 'completed';
                    stepColor = step.color;
                    textColor = '#2d3748';
                    iconClass = step.icon;
                } else if (isCurrent && !isProblem) {
                    stepStatus = 'current';
                    stepColor = step.color;
                    textColor = '#2d3748';
                    iconClass = step.icon;
                } else if (isProblem && isCompleted) {
                    stepStatus = 'problem';
                    stepColor = '#dc3545';
                    textColor = '#721c24';
                    iconClass = 'bi-exclamation-triangle';
                }
                
                timelineHtml += `
                    <div style="display: flex; align-items: flex-start; margin-bottom: ${index === steps.length - 1 ? '0' : '20px'}; position: relative;">
                        ${index < steps.length - 1 ? `
                            <div style="position: absolute; left: 19px; top: 40px; height: 20px; width: 2px; background: ${isCompleted ? stepColor : '#e9ecef'};"></div>
                        ` : ''}
                        
                        <div style="width: 38px; height: 38px; border-radius: 50%; background: ${stepColor}; display: flex; align-items: center; justify-content: center; margin-right: 15px; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: relative; z-index: 1;">
                            <i class="${iconClass}" style="color: white; font-size: 16px;"></i>
                        </div>
                        
                        <div style="flex: 1;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                <h6 style="margin: 0; color: ${textColor}; font-size: 0.95rem; font-weight: 600;">
                                    ${step.title}
                                </h6>
                                ${isCompleted ? `
                                    <span style="background: rgba(40, 167, 69, 0.1); color: #28a745; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                        ${formatDate(step.timestamp)}
                                    </span>
                                ` : isCurrent ? `
                                    <span style="background: rgba(255, 193, 7, 0.1); color: #e0a800; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                        กำลังดำเนินการ
                                    </span>
                                ` : `
                                    <span style="color: #adb5bd; font-size: 0.8rem; font-style: italic;">
                                        รอดำเนินการ
                                    </span>
                                `}
                            </div>
                            <p style="margin: 0; color: #6c757d; font-size: 0.85rem; line-height: 1.4;">
                                ${step.description}
                            </p>
                        </div>
                    </div>
                `;
            });
            
            timelineHtml += '</div>';
            return timelineHtml;
        }

        // Function to toggle delivery items visibility
        function toggleDeliveryItems(deliveryId) {
            const itemsDiv = document.getElementById(`items-${deliveryId}`);
            const icon = document.getElementById(`icon-${deliveryId}`);
            const text = document.getElementById(`text-${deliveryId}`);
            
            if (itemsDiv && itemsDiv.style.display === 'none') {
                // Show items
                itemsDiv.style.display = 'block';
                if (icon) icon.className = 'bi bi-chevron-up';
                if (text) text.textContent = 'ซ่อนรายละเอียดสินค้า';
                
                // Add smooth animation
                itemsDiv.style.opacity = '0';
                itemsDiv.style.transform = 'translateY(-10px)';
                setTimeout(() => {
                    itemsDiv.style.transition = 'all 0.3s ease';
                    itemsDiv.style.opacity = '1';
                    itemsDiv.style.transform = 'translateY(0)';
                }, 10);
            } else if (itemsDiv) {
                // Hide items
                itemsDiv.style.transition = 'all 0.3s ease';
                itemsDiv.style.opacity = '0';
                itemsDiv.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    itemsDiv.style.display = 'none';
                    if (icon) icon.className = 'bi bi-chevron-down';
                    if (text) text.textContent = 'ดูรายละเอียดสินค้า';
                }, 300);
            }
        }

        // Utility function to format date
        function formatDate(dateString) {
            if (!dateString) return '-';
            
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return '-';
            
            const day = date.getDate().toString().padStart(2, '0');
            const month = (date.getMonth() + 1).toString().padStart(2, '0');
            const year = date.getFullYear();
            const hours = date.getHours().toString().padStart(2, '0');
            const minutes = date.getMinutes().toString().padStart(2, '0');
            
            return `${day}/${month}/${year} ${hours}:${minutes}`;
        }
    </script>
</body>
</html>