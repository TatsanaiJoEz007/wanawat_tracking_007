<?php 
// เริ่ม output buffering และจัดการภาษา
ob_start();
require_once('function/language.php');

require_once('config/connect.php'); 

// ตรวจสอบก่อนเริ่ม session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบ login และดึง customer_id จาก tb_user
if (!isset($_SESSION['user_id'])) {
    die("User ID is not set. Please login.");
}

$user_id = $_SESSION['user_id'];

// Query เพื่อหา customer_id จาก user_id
$user_query = "SELECT customer_id FROM tb_user WHERE user_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

if ($user_result->num_rows == 0) {
    die("User not found.");
}

$user_data = $user_result->fetch_assoc();
$customer_id = $user_data['customer_id'];

if (empty($customer_id)) {
    die("Customer ID not found for this user.");
}

// ตรวจสอบ tab parameter จาก URL
$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'ongoing';

// Query สำหรับสินค้าที่กำลังจัดส่ง (On Delivery)
$query_ongoing = "SELECT 
    TRIM(d.delivery_number) AS delivery_number,
    TRIM(d.delivery_date) AS delivery_date,
    TRIM(d.delivery_status) AS delivery_status,
    TRIM(d.delivery_id) AS delivery_id,
    TRIM(di.bill_number) AS bill_number,
    TRIM(di.item_desc) AS item_desc
FROM 
    tb_delivery d
JOIN 
    tb_delivery_items di ON d.delivery_id = di.delivery_id
WHERE 
    TRIM(di.bill_customer_id) = '$customer_id' AND d.delivery_status IN (1, 2, 3, 4, 99)
ORDER BY d.delivery_date DESC;";

// Query สำหรับประวัติการสั่งซื้อ (Completed)
$query_history = "SELECT 
    TRIM(d.delivery_number) AS delivery_number,
    TRIM(d.delivery_date) AS delivery_date,
    TRIM(d.delivery_status) AS delivery_status,
    TRIM(di.bill_number) AS bill_number,
    TRIM(di.item_desc) AS item_desc
FROM 
    tb_delivery d
JOIN 
    tb_delivery_items di ON d.delivery_id = di.delivery_id
WHERE 
    TRIM(di.bill_customer_id) = '$customer_id' AND d.delivery_status = 5
ORDER BY d.delivery_date DESC;";

$result_ongoing = mysqli_query($conn, $query_ongoing);
$result_history = mysqli_query($conn, $query_history);

if (!$result_ongoing || !$result_history) {
    die("Query failed: " . mysqli_error($conn));
}

// Debug information (ลบออกได้หลังจากแก้ไขเสร็จ)
echo "<!-- Debug Info: User ID: $user_id, Customer ID: $customer_id -->";
echo "<!-- Ongoing results: " . mysqli_num_rows($result_ongoing) . " -->";
echo "<!-- History results: " . mysqli_num_rows($result_history) . " -->";
echo "<!-- Query ongoing: $query_ongoing -->";
echo "<!-- Query history: $query_history -->";
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'th' ? 'ประวัติการสั่งซื้อ' : 'Order History'; ?></title>
    <?php require_once('function/head.php'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    
    <style>
        /* Reset และ base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        /* Main content wrapper */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content-area {
            flex: 1;
            padding-bottom: 20px;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 9px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-track {
            background-color: #f1f1f1;
            border-radius: 10px;
        }

        /* Footer จะอยู่ด้านล่างเสมอ */
        footer {
            margin-top: auto;
        }

        /* Order history content */
        .order-content {
            min-height: calc(100vh - 300px);
            padding: 20px 0;
        }

        /* Page header */
        .page-header {
            background: linear-gradient(45deg, #F0592E, #FF4B2B);
            color: white;
            padding: 10px 0;
            margin-bottom: 30px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .page-header h1 {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }

        /* Custom tabs */
        .custom-tabs {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .custom-tabs .nav-tabs {
            border: none;
            background: #f8f9fa;
        }

        .custom-tabs .nav-link {
            border: none;
            color: #666;
            font-weight: 600;
            padding: 15px 30px;
            transition: all 0.3s ease;
        }

        .custom-tabs .nav-link:hover {
            background: rgba(240, 89, 46, 0.1);
            color: #F0592E;
        }

        .custom-tabs .nav-link.active {
            background: linear-gradient(45deg, #F0592E, #FF4B2B);
            color: white;
            border-radius: 0;
        }

        /* Table styles */
        .order-table {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .order-table table {
            margin: 0;
        }

        .order-table thead {
            background: linear-gradient(45deg, #F0592E, #FF4B2B);
            color: white;
        }

        .order-table thead th {
            border: none;
            padding: 15px;
            font-weight: 600;
            text-align: center;
        }

        .order-table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
        }

        .order-table tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.3s ease;
        }

        /* Status styles */
        .status-badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            white-space: nowrap;
        }

        .status-blue {
            background-color: #e3f2fd;
            color: #1565c0;
            border: 1px solid #bbdefb;
        }

        .status-yellow {
            background-color: #fff8e1;
            color: #f57f17;
            border: 1px solid #ffe0b2;
        }

        .status-grey {
            background-color: #f5f5f5;
            color: #424242;
            border: 1px solid #e0e0e0;
        }

        .status-purple {
            background-color: #f3e5f5;
            color: #7b1fa2;
            border: 1px solid #e1bee7;
        }

        .status-red {
            background-color: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }

        .status-green {
            background-color: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }

        /* Button styles */
        .btn-track {
            background: linear-gradient(45deg, #F0592E, #FF4B2B);
            border: none;
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-track:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.4);
            color: white;
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            text-decoration: none;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateX(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-back i {
            transition: transform 0.3s ease;
        }

        .btn-back:hover i {
            transform: translateX(-2px);
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 50px;
            color: #666;
        }

        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: #999;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #bbb;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .page-header h1 {
                font-size: 2rem;
            }

            .page-header .row {
                flex-direction: column;
                gap: 15px;
            }

            .page-header .col-md-8 {
                order: 1;
            }

            .page-header .col-md-2:first-child {
                order: 2;
                text-align: center;
            }

            .btn-back {
                font-size: 13px;
                padding: 8px 16px;
            }

            .custom-tabs .nav-link {
                padding: 12px 20px;
                font-size: 14px;
            }

            .order-table {
                font-size: 14px;
            }

            .order-table thead th,
            .order-table tbody td {
                padding: 10px 8px;
            }

            .status-badge {
                font-size: 11px;
                padding: 6px 12px;
            }

            .order-content {
                min-height: calc(100vh - 250px);
                padding: 15px 0;
            }
        }

        @media (max-width: 576px) {
            .order-content {
                min-height: calc(100vh - 200px);
                padding: 10px 0;
            }

            .page-header {
                padding: 20px 0;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .btn-back {
                font-size: 12px;
                padding: 6px 12px;
            }

            .order-table thead th:nth-child(3),
            .order-table tbody td:nth-child(3) {
                display: none; /* ซ่อนคอลัมน์สินค้าในหน้าจอเล็ก */
            }
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <!-- Navigation -->
        <?php require_once('function/navindex.php'); ?>

        <!-- Page Header -->
        <div class="page-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <a href="profile.php" class="btn btn-back">
                            <i class="fas fa-arrow-left me-2"></i>
                            <?php echo $lang == 'th' ? 'กลับ' : 'Back'; ?>
                        </a>
                    </div>
                    <div class="col-md-8">
                        <h1 class="text-center mb-0">
                            <i class="fas fa-shopping-cart me-3"></i>
                            <?php echo $lang == 'th' ? 'ประวัติการสั่งซื้อ' : 'Order History'; ?>
                        </h1>
                    </div>
                    <div class="col-md-2"></div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-area">
            <div class="order-content">
                <div class="container">
                    <!-- Custom Tabs -->
                    <div class="custom-tabs">
                        <ul class="nav nav-tabs" id="orderTabs" role="tablist">
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link <?php echo ($active_tab == 'ongoing') ? 'active' : ''; ?> w-100" id="ongoing-tab" data-bs-toggle="tab" data-bs-target="#ongoing" type="button" role="tab">
                                    <i class="fas fa-truck me-2"></i>
                                    <?php echo $lang == 'th' ? 'กำลังจัดส่ง' : 'On Delivery'; ?>
                                    <span class="badge bg-light text-dark ms-2">
                                        <?php echo mysqli_num_rows($result_ongoing); ?>
                                    </span>
                                </button>
                            </li>
                            <li class="nav-item flex-fill" role="presentation">
                                <button class="nav-link <?php echo ($active_tab == 'history') ? 'active' : ''; ?> w-100" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">
                                    <i class="fas fa-history me-2"></i>
                                    <?php echo $lang == 'th' ? 'ประวัติการสั่งซื้อ' : 'Order History'; ?>
                                    <span class="badge bg-light text-dark ms-2">
                                        <?php echo mysqli_num_rows($result_history); ?>
                                    </span>
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="orderTabsContent">
                            <!-- On Delivery Tab -->
                            <div class="tab-pane fade <?php echo ($active_tab == 'ongoing') ? 'show active' : ''; ?>" id="ongoing" role="tabpanel">
                                <div class="order-table">
                                    <?php if (mysqli_num_rows($result_ongoing) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col"><?php echo $lang == 'th' ? 'หมายเลขบิล' : 'Bill Number'; ?></th>
                                                        <th scope="col"><?php echo $lang == 'th' ? 'สินค้า' : 'Product'; ?></th>
                                                        <th scope="col"><?php echo $lang == 'th' ? 'สถานะ' : 'Status'; ?></th>
                                                        <th scope="col"><?php echo $lang == 'th' ? 'วันที่สั่งซื้อ' : 'Order Date'; ?></th>
                                                        <th scope="col"><?php echo $lang == 'th' ? 'ติดตาม' : 'Track'; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $i = 1;
                                                    mysqli_data_seek($result_ongoing, 0); // Reset pointer
                                                    while ($row = mysqli_fetch_assoc($result_ongoing)): 
                                                        $status_text = '';
                                                        $status_class = '';
                                                        switch ($row['delivery_status']) {
                                                            case 1:
                                                                $status_text = $lang == 'th' ? 'คำสั่งซื้อเข้าสู่ระบบ' : 'Order Received';
                                                                $status_class = 'status-blue';
                                                                break;
                                                            case 2:
                                                                $status_text = $lang == 'th' ? 'กำลังจัดส่งไปยังศูนย์กระจายสินค้า' : 'Shipping to Distribution Center';
                                                                $status_class = 'status-yellow';
                                                                break;
                                                            case 3:
                                                                $status_text = $lang == 'th' ? 'อยู่ที่ศูนย์กระจายสินค้าปลายทาง' : 'At Destination Center';
                                                                $status_class = 'status-grey';
                                                                break;
                                                            case 4:
                                                                $status_text = $lang == 'th' ? 'กำลังนำส่งให้ลูกค้า' : 'Out for Delivery';
                                                                $status_class = 'status-purple';
                                                                break;
                                                            case 99:
                                                                $status_text = $lang == 'th' ? 'เกิดปัญหา' : 'Issue Detected';
                                                                $status_class = 'status-red';
                                                                break;
                                                        }
                                                    ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo $i++; ?></td>
                                                            <td class="text-center"><?php echo htmlspecialchars($row['bill_number']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['item_desc']); ?></td>
                                                            <td class="text-center">
                                                                <span class="status-badge <?php echo $status_class; ?>">
                                                                    <?php echo $status_text; ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-center"><?php echo htmlspecialchars($row['delivery_date']); ?></td>
                                                            <td class="text-center">
                                                                <a href="tracking_mainpage.php?trackingId=<?php echo htmlspecialchars($row['delivery_number']); ?>" 
                                                                   class="btn btn-track">
                                                                    <i class="fas fa-search me-1"></i>
                                                                    <?php echo $lang == 'th' ? 'ติดตาม' : 'Track'; ?>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <i class="fas fa-truck"></i>
                                            <h4><?php echo $lang == 'th' ? 'ไม่มีสินค้าที่กำลังจัดส่ง' : 'No Ongoing Deliveries'; ?></h4>
                                            <p><?php echo $lang == 'th' ? 'ขณะนี้ไม่มีสินค้าที่กำลังจัดส่ง' : 'Currently no items are being delivered'; ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Order History Tab -->
                            <div class="tab-pane fade <?php echo ($active_tab == 'history') ? 'show active' : ''; ?>" id="history" role="tabpanel">
                                <div class="order-table">
                                    <?php if (mysqli_num_rows($result_history) > 0): ?>
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">#</th>
                                                        <th scope="col"><?php echo $lang == 'th' ? 'หมายเลขบิล' : 'Bill Number'; ?></th>
                                                        <th scope="col"><?php echo $lang == 'th' ? 'สินค้า' : 'Product'; ?></th>
                                                        <th scope="col"><?php echo $lang == 'th' ? 'สถานะ' : 'Status'; ?></th>
                                                        <th scope="col"><?php echo $lang == 'th' ? 'วันที่สั่งซื้อ' : 'Order Date'; ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $i = 1;
                                                    mysqli_data_seek($result_history, 0); // Reset pointer
                                                    while ($row = mysqli_fetch_assoc($result_history)): 
                                                        $status_text = $lang == 'th' ? 'จัดส่งสำเร็จ' : 'Delivered Successfully';
                                                    ?>
                                                        <tr>
                                                            <td class="text-center"><?php echo $i++; ?></td>
                                                            <td class="text-center"><?php echo htmlspecialchars($row['bill_number']); ?></td>
                                                            <td><?php echo htmlspecialchars($row['item_desc']); ?></td>
                                                            <td class="text-center">
                                                                <span class="status-badge status-green">
                                                                    <i class="fas fa-check-circle me-1"></i>
                                                                    <?php echo $status_text; ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-center"><?php echo htmlspecialchars($row['delivery_date']); ?></td>
                                                        </tr>
                                                    <?php endwhile; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-state">
                                            <i class="fas fa-history"></i>
                                            <h4><?php echo $lang == 'th' ? 'ไม่มีประวัติการสั่งซื้อ' : 'No Order History'; ?></h4>
                                            <p><?php echo $lang == 'th' ? 'ยังไม่มีประวัติการสั่งซื้อที่เสร็จสิ้น' : 'No completed orders found'; ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <?php require_once('function/footer.php'); ?>
    </footer>

    <!-- Scripts -->
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>

    <script>
        // เช็คความสูงของเนื้อหาและปรับ footer
        function adjustFooter() {
            const body = document.body;
            const html = document.documentElement;
            const height = Math.max(body.scrollHeight, body.offsetHeight, 
                                   html.clientHeight, html.scrollHeight, html.offsetHeight);
            
            if (height < window.innerHeight) {
                document.querySelector('footer').style.position = 'fixed';
                document.querySelector('footer').style.bottom = '0';
                document.querySelector('footer').style.width = '100%';
            }
        }

        // เรียกใช้เมื่อโหลดหน้าเสร็จ
        window.addEventListener('load', adjustFooter);
        window.addEventListener('resize', adjustFooter);
        
        // Tab switching with smooth transition and URL parameter handling
        document.addEventListener('DOMContentLoaded', function() {
            // ตรวจสอบ URL parameter และเปิดแท็บที่ถูกต้อง
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            
            if (tabParam) {
                // เปิดแท็บตาม URL parameter
                const targetTab = document.querySelector(`#${tabParam}-tab`);
                const targetPane = document.querySelector(`#${tabParam}`);
                
                if (targetTab && targetPane) {
                    // ปิดแท็บอื่นทั้งหมด
                    document.querySelectorAll('.nav-link').forEach(tab => {
                        tab.classList.remove('active');
                    });
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });
                    
                    // เปิดแท็บที่ต้องการ
                    targetTab.classList.add('active');
                    targetPane.classList.add('show', 'active');
                }
            }
            
            // จัดการเมื่อมีการคลิกแท็บ
            const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabs.forEach(tab => {
                tab.addEventListener('shown.bs.tab', function(e) {
                    adjustFooter();
                    
                    // อัพเดท URL โดยไม่รีโหลดหน้า
                    const tabId = e.target.getAttribute('data-bs-target').replace('#', '');
                    const newUrl = new URL(window.location);
                    newUrl.searchParams.set('tab', tabId);
                    window.history.replaceState({}, '', newUrl);
                });
            });
        });
    </script>
</body>

</html>