<?php
// เริ่ม output buffering
ob_start();

// เริ่ม session (แต่ไม่บังคับต้อง login)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// กำหนดภาษาเริ่มต้น
$lang = 'th'; // ภาษาเริ่มต้น

// ตรวจสอบการตั้งค่าภาษาจาก session หรือ cookie
if (isset($_SESSION['language'])) {
    $lang = $_SESSION['language'];
} elseif (isset($_COOKIE['language'])) {
    $lang = $_COOKIE['language'];
} elseif (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['language'] = $lang;
    setcookie('language', $lang, time() + (86400 * 30), '/'); // 30 days
}

// ตรวจสอบว่าภาษาที่เลือกถูกต้อง
if (!in_array($lang, ['th', 'en'])) {
    $lang = 'th';
}

// เชื่อมต่อฐานข้อมูล
require_once('config/connect.php');

// รับ tracking ID จาก URL
$trackingId = isset($_GET['trackingId']) ? trim($_GET['trackingId']) : '';
$showTrackingForm = empty($trackingId);
$deliveryData = null;
$errorMessage = '';

// ถ้ามี tracking ID ให้ค้นหาข้อมูล
if (!empty($trackingId)) {
    try {
        // Query ข้อมูลการจัดส่ง
        $query = "SELECT d.*, di.bill_number, di.bill_customer_name, di.item_desc 
                  FROM tb_delivery d 
                  LEFT JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
                  WHERE d.delivery_number = ?";
        $stmt = $conn->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("s", $trackingId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $deliveryData = $result->fetch_assoc();
            } else {
                $errorMessage = $lang == 'th' 
                    ? 'ไม่พบข้อมูลการจัดส่งสำหรับหมายเลข: ' . htmlspecialchars($trackingId)
                    : 'No delivery information found for tracking number: ' . htmlspecialchars($trackingId);
            }
            $stmt->close();
        } else {
            $errorMessage = $lang == 'th' 
                ? 'เกิดข้อผิดพลาดในการค้นหาข้อมูล'
                : 'Error occurred while searching for data';
        }
    } catch (Exception $e) {
        $errorMessage = $lang == 'th' 
            ? 'เกิดข้อผิดพลาดในระบบ'
            : 'System error occurred';
    }
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'th' ? 'ติดตามสถานะการจัดส่ง - Wanawat Tracking' : 'Track Delivery Status - Wanawat Tracking'; ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #F0592E 0%, #FF8A65 100%);
            min-height: 100vh;
            color: #2d3748;
        }

        /* Header Styles */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 20px 0;
            margin-bottom: 30px;
        }

        .logo {
            display: flex;
            align-items: center;
            color: #F0592E;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.5rem;
        }

        .logo:hover {
            color: #D84315;
            text-decoration: none;
        }

        .logo i {
            margin-right: 10px;
            font-size: 2rem;
        }

        /* Language Switcher */
        .language-switcher {
            display: flex;
            gap: 10px;
        }

        .lang-btn {
            padding: 8px 16px;
            border: 2px solid #F0592E;
            background: transparent;
            color: #F0592E;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .lang-btn:hover, .lang-btn.active {
            background: #F0592E;
            color: white;
            text-decoration: none;
        }

        /* Container Styles */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Search Form Styles */
        .search-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .search-title {
            color: #F0592E;
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-subtitle {
            color: #718096;
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        .search-form {
            max-width: 600px;
            margin: 0 auto;
        }

        .search-input-group {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .search-input {
            flex: 1;
            padding: 15px 20px;
            border: 2px solid rgba(240, 89, 46, 0.3);
            border-radius: 15px;
            font-size: 1.1rem;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: #F0592E;
            box-shadow: 0 0 0 0.2rem rgba(240, 89, 46, 0.25);
            background: white;
        }

        .search-btn {
            padding: 15px 30px;
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            border: none;
            border-radius: 15px;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 10px;
            white-space: nowrap;
        }

        .search-btn:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(240, 89, 46, 0.4);
        }

        /* Alert Styles */
        .alert {
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
            border: none;
            font-weight: 500;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #dc2626;
            border-left: 5px solid #dc2626;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            color: #d97706;
            border-left: 5px solid #d97706;
        }

        /* Back Button */
        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 12px 25px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(240, 89, 46, 0.3);
            border-radius: 15px;
            color: #F0592E;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 20px;
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.2);
        }

        .back-btn:hover {
            background: rgba(240, 89, 46, 0.1);
            color: #D84315;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(240, 89, 46, 0.3);
        }

        .back-btn i {
            margin-right: 8px;
        }

        /* Include the tracking styles from stepstatus.php */
        .tracking-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: transparent;
        }

        .tracking-header {
            background: linear-gradient(45deg, #F0592E, #FF4B2B);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(240, 89, 46, 0.3);
            text-align: center;
        }

        .tracking-header h1 {
            font-size: 1.7rem;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .tracking-number {
            font-size: 0.9rem;
            font-weight: 600;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 20px;
            border-radius: 25px;
            display: inline-block;
            margin-top: 10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(240, 89, 46, 0.1);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .info-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }

        .info-label {
            font-weight: 600;
            color: #F0592E;
        }

        .info-value {
            color: #2d3748;
            font-weight: 500;
        }

        /* Timeline styles */
        .timeline-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(45deg, #F0592E, #FF4B2B);
        }

        .timeline-title {
            text-align: center;
            font-size: 1.2rem;
            font-weight: bold;
            color: #F0592E;
            margin-bottom: 40px;
            position: relative;
        }

        .timeline-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(45deg, #F0592E, #FF4B2B);
            border-radius: 2px;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(to bottom, #e2e8f0, #cbd5e0);
            transform: translateX(-50%);
            border-radius: 2px;
        }

        /* Timeline items */
        .timeline-item {
            position: relative;
            margin-bottom: 60px;
            width: 100%;
            display: flex;
            align-items: center;
        }

        .timeline-item:last-child {
            margin-bottom: 0;
        }

        .timeline-item.left {
            flex-direction: row;
        }

        .timeline-item.right {
            flex-direction: row-reverse;
        }

        .timeline-content {
            width: 45%;
            padding: 25px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border: 2px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
        }

        .timeline-content:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .timeline-content.completed {
            border-color: #10b981;
            background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
        }

        .timeline-content.current {
            border-color: #10b981;
            background: linear-gradient(135deg, #fff7ed, #fef3c7);
            animation: glow 2s infinite;
        }

        .timeline-content.problem {
            border-color: #ef4444;
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            animation: problemGlow 2s infinite;
        }

        @keyframes glow {
            0%, 100% {
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1), 0 0 0 0 #10b981;
            }
            50% {
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1), 0 0 20px 5px #10b981;
            }
        }

        @keyframes problemGlow {
            0%, 100% {
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1), 0 0 0 0 #ef4444;
            }
            50% {
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1), 0 0 20px 5px rgba(239, 68, 68, 0.2);
            }
        }

        .timeline-icon {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            z-index: 10;
            border: 4px solid white;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .timeline-icon i {
            font-size: 24px;
            color: white;
            font-weight: bold;
            display: inline-block !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Ensure Font Awesome icons are visible */
        .fas, .fa-solid, .fa {
            font-family: "Font Awesome 6 Free", "Font Awesome 5 Free", FontAwesome !important;
            font-weight: 900 !important;
            font-style: normal !important;
            font-variant: normal !important;
            text-rendering: auto !important;
            line-height: 1 !important;
            display: inline-block !important;
            visibility: visible !important;
        }

        .status-blue {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .status-blue.completed {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .status-yellow {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .status-yellow.completed {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .status-grey {
            background: linear-gradient(135deg, #6b7280, #4b5563);
            color: white;
        }

        .status-grey.completed {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .status-purple {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
        }

        .status-purple.completed {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .status-green {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .status-red {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .status-pending {
            background: linear-gradient(135deg, #9ca3af, #6b7280);
            color: white;
        }

        .timeline-icon.current {
            animation: pulse 2s infinite;
        }

        .timeline-icon.problem {
            animation: problemPulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2), 0 0 0 0 #10b981;
            }
            50% {
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2), 0 0 0 15px rgba(240, 89, 46, 0);
            }
        }

        @keyframes problemPulse {
            0%, 100% {
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            50% {
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2), 0 0 0 15px rgba(239, 68, 68, 0);
            }
        }

        .step-title {
            font-size: 1.3rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1a202c;
        }

        .step-desc {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 10px;
        }

        .step-time {
            font-size: 0.9rem;
            color: #718096;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .search-input-group {
                flex-direction: column;
            }

            .search-title {
                font-size: 1.8rem;
            }

            .timeline::before {
                left: 30px;
            }

            .timeline-item {
                flex-direction: column !important;
                align-items: flex-start;
            }

            .timeline-content {
                width: 100%;
                margin-left: 60px;
            }

            .timeline-icon {
                left: 30px;
                width: 50px;
                height: 50px;
                font-size: 18px;
            }

            .main-container {
                padding: 0 15px;
            }

            .search-container {
                padding: 30px 20px;
            }
        }

        @media (max-width: 576px) {
            .header {
                padding: 15px 0;
            }

            .logo {
                font-size: 1.2rem;
            }

            .logo i {
                font-size: 1.5rem;
            }

            .search-title {
                font-size: 1.5rem;
            }

            .search-subtitle {
                font-size: 1rem;
            }

            .language-switcher {
                flex-direction: column;
                gap: 5px;
            }

            .lang-btn {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <?php 
    // Include navbar if file exists
    if (file_exists('function/navbar.php')) {
        require_once('function/navbar.php'); 
    } else {
        // Fallback header if navbar doesn't exist
    ?>
    <div class="header">
        <div class="main-container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="index.php" class="logo">
                    <i class="fas fa-truck"></i>
                    Wanawat Tracking
                </a>
                
                <div class="language-switcher">
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'th'])); ?>" 
                       class="lang-btn <?php echo $lang == 'th' ? 'active' : ''; ?>">
                        ไทย
                    </a>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['lang' => 'en'])); ?>" 
                       class="lang-btn <?php echo $lang == 'en' ? 'active' : ''; ?>">
                        English
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    <!-- Main Content -->
    <div class="main-container">
        <?php if ($showTrackingForm): ?>
            <!-- Search Form -->
            <div class="search-container">
                <h1 class="search-title">
                    <i class="fas fa-search-location"></i><br>
                    <?php echo $lang == 'th' ? 'ติดตามสถานะการจัดส่ง' : 'Track Your Delivery'; ?>
                </h1>
                <p class="search-subtitle">
                    <?php echo $lang == 'th' 
                        ? 'กรอกหมายเลขติดตามเพื่อดูสถานะการจัดส่งของคุณ' 
                        : 'Enter your tracking number to check delivery status'; ?>
                </p>
                
                <form method="GET" class="search-form">
                    <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">
                    <div class="search-input-group">
                        <input 
                            type="text" 
                            name="trackingId" 
                            class="search-input" 
                            placeholder="<?php echo $lang == 'th' ? 'ใส่หมายเลขติดตาม...' : 'Enter tracking number...'; ?>"
                            value="<?php echo htmlspecialchars($trackingId); ?>"
                            required
                        >
                        <button type="submit" class="search-btn">
                            <i class="fas fa-search"></i>
                            <?php echo $lang == 'th' ? 'ติดตาม' : 'Track'; ?>
                        </button>
                    </div>
                </form>
                
                <div class="mt-4">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        <?php echo $lang == 'th' 
                            ? 'หมายเลขติดตามจะอยู่ในใบเสร็จหรืออีเมลยืนยันของคุณ' 
                            : 'Tracking number can be found on your receipt or confirmation email'; ?>
                    </small>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMessage)): ?>
            <!-- Error Message -->
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $errorMessage; ?>
            </div>
            
            <div class="text-center">
                <a href="javascript:history.back()" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    <?php echo $lang == 'th' ? 'กลับ' : 'Back'; ?>
                </a>
            </div>
        <?php endif; ?>

        <?php if ($deliveryData): ?>
            <!-- Tracking Results -->
            <div class="mb-3">
                <a href="../index" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    <?php echo $lang == 'th' ? 'กลับ' : 'Back'; ?>
                </a>
            </div>

            <?php
            // Include tracking display logic
            $current_status = (int)$deliveryData['delivery_status'];
            
            // กำหนดขั้นตอนการจัดส่ง
            $steps = [
                1 => [
                    'title' => $lang == 'th' ? 'คำสั่งซื้อเข้าสู่ระบบ' : 'Order Received',
                    'desc' => $lang == 'th' ? 'คำสั่งซื้อของคุณได้รับการยืนยันแล้ว' : 'Your order has been confirmed',
                    'icon' => 'fas fa-clipboard-check',
                    'color' => 'blue'
                ],
                2 => [
                    'title' => $lang == 'th' ? 'กำลังจัดส่งไปศูนย์กระจาย' : 'Shipping to Distribution Center',
                    'desc' => $lang == 'th' ? 'สินค้ากำลังเดินทางไปยังศูนย์กระจายสินค้า' : 'Items are being shipped to distribution center',
                    'icon' => 'fas fa-shipping-fast',
                    'color' => 'yellow'
                ],
                3 => [
                    'title' => $lang == 'th' ? 'อยู่ที่ศูนย์กระจายสินค้า' : 'At Distribution Center',
                    'desc' => $lang == 'th' ? 'สินค้าถึงศูนย์กระจายสินค้าปลายทางแล้ว' : 'Items have arrived at destination center',
                    'icon' => 'fas fa-warehouse',
                    'color' => 'grey'
                ],
                4 => [
                    'title' => $lang == 'th' ? 'กำลังนำส่งให้ลูกค้า' : 'Out for Delivery',
                    'desc' => $lang == 'th' ? 'สินค้ากำลังเดินทางมาหาคุณ' : 'Items are on the way to you',
                    'icon' => 'fas fa-truck',
                    'color' => 'purple'
                ],
                5 => [
                    'title' => $current_status == 5 
                        ? ($lang == 'th' ? 'จัดส่งสำเร็จ' : 'Delivered Successfully')
                        : ($lang == 'th' ? 'ยังจัดส่งไม่สำเร็จ' : 'Not Yet Delivered'),
                    'desc' => $current_status == 5 
                        ? ($lang == 'th' ? 'สินค้าถึงมือคุณเรียบร้อยแล้ว' : 'Items have been successfully delivered')
                        : ($lang == 'th' ? 'สินค้ายังไม่ถึงมือคุณ' : 'Items have not been delivered yet'),
                    'icon' => $current_status == 5 ? 'fas fa-check-circle' : 'fas fa-clock',
                    'color' => $current_status == 5 ? 'green' : 'pending'
                ]
            ];

            // จัดการสถานะพิเศษ
            if ($current_status == 99) {
                $problem_step = [
                    'title' => $lang == 'th' ? 'เกิดปัญหา' : 'Issue Detected',
                    'desc' => $lang == 'th' ? 'เกิดปัญหาในการจัดส่ง กรุณาติดต่อเจ้าหน้าที่' : 'There is an issue with delivery, please contact support',
                    'icon' => 'fas fa-exclamation-triangle',
                    'color' => 'red'
                ];
            }
            ?>

            <div class="tracking-container">
                <!-- Header -->
                <div class="tracking-header">
                    <h1>
                        <i class="fas fa-search-location"></i>
                        <?php echo $lang == 'th' ? 'ติดตามสถานะการจัดส่ง' : 'Track Delivery Status'; ?>
                    </h1>
                    <div class="tracking-number">
                        <?php echo $lang == 'th' ? 'หมายเลขติดตาม:' : 'Tracking Number:'; ?> 
                        <strong><?php echo htmlspecialchars($trackingId); ?></strong>
                    </div>
                </div>

                <!-- Delivery Information -->
                <div class="info-card">
                    <h3 style="color: #F0592E; margin-bottom: 20px; font-size: 1.4rem;">
                        <i class="fas fa-info-circle"></i> 
                        <?php echo $lang == 'th' ? 'ข้อมูลการจัดส่ง' : 'Delivery Information'; ?>
                    </h3>
                    <div class="info-row">
                        <span class="info-label"><?php echo $lang == 'th' ? 'หมายเลขบิล:' : 'Bill Number:'; ?></span>
                        <span class="info-value"><?php echo htmlspecialchars($deliveryData['bill_number'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo $lang == 'th' ? 'ลูกค้า:' : 'Customer:'; ?></span>
                        <span class="info-value"><?php echo htmlspecialchars($deliveryData['bill_customer_name'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo $lang == 'th' ? 'รายการสินค้า:' : 'Items:'; ?></span>
                        <span class="info-value"><?php echo htmlspecialchars($deliveryData['item_desc'] ?? 'N/A'); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><?php echo $lang == 'th' ? 'วันที่สร้าง:' : 'Created Date:'; ?></span>
                        <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($deliveryData['delivery_date'])); ?></span>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="timeline-container">
                    <h2 class="timeline-title">
                        <i class="fas fa-route"></i>
                        <?php echo $lang == 'th' ? 'สถานะการจัดส่ง' : 'Delivery Status'; ?>
                    </h2>

                    <div class="timeline">
                        <?php if ($current_status == 99): ?>
                            <!-- Problem Status -->
                            <div class="timeline-item">
                                <div class="timeline-content problem">
                                    <h3 class="step-title"><?php echo $problem_step['title']; ?></h3>
                                    <p class="step-desc"><?php echo $problem_step['desc']; ?></p>
                                    <p class="step-time">
                                        <i class="fas fa-clock"></i>
                                        <?php echo date('d/m/Y H:i', strtotime($deliveryData['delivery_date'])); ?>
                                    </p>
                                </div>
                                <div class="timeline-icon status-red problem">
                                    <i class="<?php echo $problem_step['icon']; ?>"></i>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($steps as $step_num => $step): ?>
                                <?php
                                $is_completed = $step_num < $current_status;
                                $is_current = $step_num == $current_status;
                                $is_pending = $step_num > $current_status;
                                $position = ($step_num % 2 == 1) ? 'left' : 'right';
                                
                                $content_class = '';
                                $icon_class = '';
                                if ($is_completed) {
                                    $content_class = 'completed';
                                    $icon_class = 'completed';
                                } elseif ($is_current) {
                                    $content_class = 'current';
                                    $icon_class = 'current';
                                } elseif ($is_pending) {
                                    $content_class = 'pending';
                                    $icon_class = '';
                                }
                                ?>
                                
                                <div class="timeline-item <?php echo $position; ?>">
                                    <div class="timeline-content <?php echo $content_class; ?>">
                                        <h3 class="step-title"><?php echo $step['title']; ?></h3>
                                        <p class="step-desc"><?php echo $step['desc']; ?></p>
                                        <?php if ($is_completed || $is_current): ?>
                                            <p class="step-time">
                                                <i class="fas fa-clock"></i>
                                                <?php echo date('d/m/Y H:i', strtotime($deliveryData['delivery_date'])); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="timeline-icon status-<?php echo $step['color']; ?> <?php echo $icon_class; ?>">
                                        <?php if ($is_completed): ?>
                                            <i class="fas fa-check"></i>
                                        <?php elseif ($is_current): ?>
                                            <i class="<?php echo $step['icon']; ?>"></i>
                                        <?php else: ?>
                                            <i class="<?php echo $step['icon']; ?>"></i>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/js/all.min.js" integrity="sha512-GWzVrcGlo0TxTRvz9ttioyYJ+Wwk9Ck0G81D+eO63BaqHaJ3YZX9wuqjwgfcV/MrB2PhaVX9DkYVhbFpStnqpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    
    <script>
        // Add smooth animations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure Font Awesome icons are loaded
            if (window.FontAwesome) {
                window.FontAwesome.dom.watch();
            }

            // Add entrance animation to timeline items
            const timelineItems = document.querySelectorAll('.timeline-item');
            timelineItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, index * 200);
            });

            // Ensure icons are visible
            setTimeout(() => {
                const icons = document.querySelectorAll('.timeline-icon i');
                icons.forEach(icon => {
                    if (icon.style.display === 'none' || !icon.offsetParent) {
                        icon.style.display = 'inline-block';
                        icon.style.visibility = 'visible';
                    }
                });
            }, 100);

            // Add hover effects
            const timelineContents = document.querySelectorAll('.timeline-content');
            timelineContents.forEach(content => {
                content.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-5px) scale(1.02)';
                });
                
                content.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0) scale(1)';
                });
            });

            // Auto-focus on search input if no tracking ID
            const searchInput = document.querySelector('.search-input');
            if (searchInput && !searchInput.value) {
                searchInput.focus();
            }
        });

        // Form validation
        document.querySelector('.search-form')?.addEventListener('submit', function(e) {
            const trackingInput = document.querySelector('input[name="trackingId"]');
            if (!trackingInput.value.trim()) {
                e.preventDefault();
                alert('<?php echo $lang == 'th' ? 'กรุณากรอกหมายเลขติดตาม' : 'Please enter tracking number'; ?>');
                trackingInput.focus();
            }
        });
    </script>
</body>
</html>