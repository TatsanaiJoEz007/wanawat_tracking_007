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

// Handle date selection for historical data with pagination
$selected_date = null;
$data = null;
$total_records = 0;
$total_pages = 0;
$current_page = 1;
$items_per_page = 10;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_date'])) {
    $selected_date = $_POST['selected_date'];
    $user_id = $_SESSION['user_id'];
    $current_page = isset($_POST['page']) ? max(1, intval($_POST['page'])) : 1;
    $offset = ($current_page - 1) * $items_per_page;
    
    try {
        // Get total count
        $count_stmt = $conn->prepare("SELECT COUNT(*) as total FROM tb_delivery d 
                                    INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
                                    WHERE DATE(d.delivery_date) = ? AND d.created_by = ?");
        $count_stmt->bind_param("si", $selected_date, $user_id);
        $count_stmt->execute();
        $count_result = $count_stmt->get_result();
        $total_records = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total_records / $items_per_page);
        $count_stmt->close();

        // Get paginated data
        $stmt = $conn->prepare("SELECT 
                                d.delivery_id, 
                                d.delivery_number, 
                                d.delivery_date, 
                                COUNT(di.item_code) AS item_count, 
                                d.delivery_status,
                                d.delivery_step1_received,
                                d.delivery_step2_transit,
                                d.delivery_step3_warehouse,
                                d.delivery_step4_last_mile,
                                d.delivery_step5_completed,
                                d.delivery_problem_desc,
                                GROUP_CONCAT(DISTINCT di.transfer_type SEPARATOR ', ') as transfer_type 
                            FROM tb_delivery d 
                            INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
                            WHERE DATE(d.delivery_date) = ? AND d.created_by = ?
                            GROUP BY d.delivery_id, d.delivery_number, d.delivery_date, d.delivery_status, 
                                     d.delivery_step1_received, d.delivery_step2_transit, d.delivery_step3_warehouse, 
                                     d.delivery_step4_last_mile, d.delivery_step5_completed, d.delivery_problem_desc
                            ORDER BY d.delivery_date DESC, d.delivery_id DESC
                            LIMIT ? OFFSET ?");
        $stmt->bind_param("siii", $selected_date, $user_id, $items_per_page, $offset);
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
            background: rgba(255, 255, 255, 0.98);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            font-size: 0.95rem;
            position: relative;
        }

        .table thead th:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background: rgba(255, 255, 255, 0.3);
        }

        .table tbody td {
            padding: 15px 12px;
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid rgba(240, 89, 46, 0.1);
            color: #2d3748;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .table tbody tr:hover {
            transition: all 0.3s ease;
            transform: scale(1.002);
            cursor: pointer;
        }

        /* Status Circle */
        .status-circle {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.8);
            position: relative;
            animation: pulse 2s infinite;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .status-circle:hover {
            transform: scale(1.2);
            z-index: 10;
        }

        .status-circle.red {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .status-circle.green {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }

        .status-circle.blue {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }

        .status-circle.yellow {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }

        .status-circle.grey {
            background: linear-gradient(135deg, #6c757d, #545b62);
        }

        .status-circle.purple {
            background: linear-gradient(135deg, #6f42c1, #59339d);
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(240, 89, 46, 0.7);
            }
            70% {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 8px rgba(240, 89, 46, 0);
            }
            100% {
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(240, 89, 46, 0);
            }
        }

        /* Completed Row Styling */
        .completed-row {
            opacity: 0.7 !important;
        }

        .completed-row:hover {
            transform: none !important;
        }

        /* Completed Status Styling */
        .completed-status {
            color: #28a745;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .completed-time {
            background: rgba(40, 167, 69, 0.15) !important;
            color: #1e7e34 !important;
            border: 1px solid rgba(40, 167, 69, 0.3);
        }

        .status-time-badge {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 4px 8px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.85rem;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .status-time-badge:hover {
            background: rgba(40, 167, 69, 0.2);
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .no-data-badge {
            color: #6c757d;
            font-style: italic;
            font-size: 0.85rem;
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

        .pagination-info {
            text-align: center;
            margin: 15px 0;
            color: #6c757d;
            font-size: 0.9rem;
            padding: 10px;
            background: rgba(240, 89, 46, 0.05);
            border-radius: 8px;
            border: 1px solid rgba(240, 89, 46, 0.1);
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
            cursor: pointer;
            border: none;
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

        /* Datepicker Customization */
        .datepicker table tr td.enabled-date {
            background-color: #28a745 !important;
            color: white !important;
            border: 1px solid #1e7e34 !important;
            cursor: pointer !important;
            font-weight: 600 !important;
        }

        .datepicker table tr td.enabled-date:hover {
            background-color: #218838 !important;
            color: white !important;
        }

        .datepicker table tr td.disabled-date {
            background-color: #f8f9fa !important;
            color: #6c757d !important;
            cursor: not-allowed;
        }

        .datepicker .dow,
        .datepicker .month {
            color: #F0592E;
            font-weight: 600;
        }

        .datepicker table tr td.active {
            background-color: #F0592E !important;
            color: white !important;
        }

        .datepicker table tr td.today {
            background-color: #ffc107;
            color: #212529;
        }

        .datepicker table tr td.today.enabled-date {
            background-color: #28a745 !important;
            color: white !important;
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
                overflow-x: auto;
            }

            .table {
                min-width: 1200px;
            }

            .table thead th,
            .table tbody td {
                padding: 10px 8px;
                font-size: 0.85rem;
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

            .table {
                min-width: 1100px;
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

            <!-- Statistics Cards Row 1 (4 cards) -->
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
                    <div class="stat-card status-yellow" onclick="openStatModal('preparing', 'คำสั่งซื้อที่กำลังจัดเตรียม')">
                        <div class="stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="stat-number"><?php echo number_format($total_delivery_preparing_box); ?></div>
                        <div class="stat-title">คำสั่งซื้อที่กำลังจัดเตรียม</div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-purple" onclick="openStatModal('sending_center', 'สินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า')">
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

            <!-- Statistics Cards Row 2 (3 cards) -->
            <div class="row stats-row">
                <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
                    <div class="stat-card status-orange" onclick="openStatModal('delivering', 'สินค้าที่กำลังนำส่งให้ลูกค้า')">
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
                        <input type="hidden" name="page" value="1">
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

                    <!-- Pagination Info -->
                    <?php if ($total_records > 0): ?>
                    <div class="pagination-info">
                        <i class="bi bi-info-circle me-1"></i>
                        แสดงรายการที่ <?php echo number_format(($current_page - 1) * $items_per_page + 1); ?> - <?php echo number_format(min($current_page * $items_per_page, $total_records)); ?> 
                        จากทั้งหมด <?php echo number_format($total_records); ?> รายการ
                    </div>
                    <?php endif; ?>

                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="8%">สีสถานะ</th>
                                    <th width="18%">เลขที่การขนส่ง</th>
                                    <th width="10%">จำนวนสินค้า</th>
                                    <th width="15%">สถานะปัจจุบัน</th>
                                    <th width="15%">วันที่สร้างบิล</th>
                                    <th width="15%">วันเวลาสถานะล่าสุด</th>
                                    <th width="14%">ประเภทการขนย้าย</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($data)) {
                                    $i = ($current_page - 1) * $items_per_page + 1;
                                    foreach ($data as $row) {
                                        // Determine status text, class, and circle color
                                        $is_completed = ($row['delivery_status'] == 5);
                                        $row_class = $is_completed ? 'completed-row' : '';
                                        
                                        switch ($row['delivery_status']) {
                                            case 1:
                                                $status_text = 'รับคำสั่งซื้อ';
                                                $circle_color = 'blue';
                                                $latest_step_time = $row['delivery_step1_received'];
                                                break;
                                            case 2:
                                                $status_text = 'กำลังจัดส่งไปศูนย์';
                                                $circle_color = 'yellow';
                                                $latest_step_time = $row['delivery_step2_transit'];
                                                break;
                                            case 3:
                                                $status_text = 'ถึงศูนย์กระจาย';
                                                $circle_color = 'grey';
                                                $latest_step_time = $row['delivery_step3_warehouse'];
                                                break;
                                            case 4:
                                                $status_text = 'กำลังส่งลูกค้า';
                                                $circle_color = 'purple';
                                                $latest_step_time = $row['delivery_step4_last_mile'];
                                                break;
                                            case 5:
                                                $status_text = 'ส่งสำเร็จ';
                                                $circle_color = 'green';
                                                $latest_step_time = $row['delivery_step5_completed'];
                                                break;
                                            case 99:
                                                $status_text = 'เกิดปัญหา';
                                                $circle_color = 'red';
                                                // Find the latest non-null timestamp for problem status
                                                $timestamps = [
                                                    $row['delivery_step5_completed'],
                                                    $row['delivery_step4_last_mile'],
                                                    $row['delivery_step3_warehouse'],
                                                    $row['delivery_step2_transit'],
                                                    $row['delivery_step1_received']
                                                ];
                                                $latest_step_time = null;
                                                foreach ($timestamps as $timestamp) {
                                                    if (!empty($timestamp)) {
                                                        $latest_step_time = $timestamp;
                                                        break;
                                                    }
                                                }
                                                break;
                                            default:
                                                $status_text = 'ไม่ทราบสถานะ';
                                                $circle_color = 'grey';
                                                $latest_step_time = null;
                                                break;
                                        }

                                        echo '<tr class="' . $row_class . '" data-delivery-id="' . $row['delivery_id'] . '" style="cursor: pointer;" title="คลิกเพื่อดูรายละเอียด">';
                                        echo '<td><strong>' . $i++ . '</strong></td>';
                                        echo '<td><center><div class="status-circle ' . $circle_color . '" title="' . $status_text . '"></div></center></td>';
                                        echo '<td><strong>' . htmlspecialchars($row['delivery_number']) . '</strong></td>';
                                        echo '<td><center><span style="background: rgba(240, 89, 46, 0.1); padding: 4px 8px; border-radius: 12px; font-weight: 600; color: #F0592E;">' . $row['item_count'] . ' รายการ</span></center></td>';
                                        
                                        // Status text with completion badge
                                        echo '<td>';
                                        if ($is_completed) {
                                            echo '<span class="completed-status">';
                                            echo '<i class="bi bi-check-circle-fill text-success me-1"></i>';
                                            echo $status_text;
                                            echo '</span>';
                                        } else {
                                            echo $status_text;
                                        }
                                        echo '</td>';
                                        
                                        echo '<td>' . date('d/m/Y H:i', strtotime($row['delivery_date'])) . '</td>';
                                        
                                        // Display latest step time
                                        if (!empty($latest_step_time)) {
                                            $formatted_time = date('d/m/Y H:i', strtotime($latest_step_time));
                                            if ($is_completed) {
                                                echo '<td><span class="status-time-badge completed-time">' . $formatted_time . '</span></td>';
                                            } else {
                                                echo '<td><span class="status-time-badge">' . $formatted_time . '</span></td>';
                                            }
                                        } else {
                                            echo '<td><span class="no-data-badge">ยังไม่มีข้อมูล</span></td>';
                                        }
                                        
                                        // แสดง transfer_type อย่างปลอดภัย
                                        $transfer_type = isset($row['transfer_type']) ? $row['transfer_type'] : 'ทั่วไป';
                                        echo '<td><span class="transfer-badge">' . htmlspecialchars($transfer_type) . '</span></td>';
                                        echo '</tr>';
                                    }
                                } else {
                                    echo "<tr>";
                                    echo "<td colspan='8' class='empty-state'>";
                                    echo "<i class='bi bi-inbox'></i>";
                                    echo "<h3>ไม่พบข้อมูลการจัดส่ง</h3>";
                                    echo "<p>ไม่มีรายการการจัดส่งในวันที่เลือก</p>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($current_page > 1): ?>
                            <button type="button" class="btn-custom" onclick="changePage(<?php echo $current_page - 1; ?>)">
                                <i class="bi bi-chevron-left"></i> ก่อนหน้า
                            </button>
                        <?php endif; ?>

                        <?php 
                        // Show page numbers with smart pagination
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        
                        // Show first page if not in range
                        if ($start_page > 1) {
                            echo '<button type="button" class="btn-custom" onclick="changePage(1)">1</button>';
                            if ($start_page > 2) {
                                echo '<span class="btn-custom" style="border: none; background: transparent; cursor: default;">...</span>';
                            }
                        }
                        
                        // Show page range
                        for ($i = $start_page; $i <= $end_page; $i++): 
                        ?>
                            <button type="button" class="btn-custom <?php echo ($i == $current_page) ? 'active' : ''; ?>" onclick="changePage(<?php echo $i; ?>)">
                                <?php echo $i; ?>
                            </button>
                        <?php endfor; ?>
                        
                        <?php
                        // Show last page if not in range
                        if ($end_page < $total_pages) {
                            if ($end_page < $total_pages - 1) {
                                echo '<span class="btn-custom" style="border: none; background: transparent; cursor: default;">...</span>';
                            }
                            echo '<button type="button" class="btn-custom" onclick="changePage(' . $total_pages . ')">' . $total_pages . '</button>';
                        }
                        ?>

                        <?php if ($current_page < $total_pages): ?>
                            <button type="button" class="btn-custom" onclick="changePage(<?php echo $current_page + 1; ?>)">
                                ถัดไป <i class="bi bi-chevron-right"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

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

            // Add row click functionality for delivery details
            $(document).on('click', 'tbody tr[data-delivery-id]', function() {
                const deliveryId = $(this).data('delivery-id');
                if (deliveryId) {
                    openDeliveryDetail(deliveryId);
                }
            });
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

        // Function to change page for historical data
        function changePage(page) {
            const selectedDate = document.querySelector('input[name="selected_date"]').value;
            if (!selectedDate) {
                alert('กรุณาเลือกวันที่ก่อน');
                return;
            }

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            const dateInput = document.createElement('input');
            dateInput.type = 'hidden';
            dateInput.name = 'selected_date';
            dateInput.value = selectedDate;

            const pageInput = document.createElement('input');
            pageInput.type = 'hidden';
            pageInput.name = 'page';
            pageInput.value = page;

            form.appendChild(dateInput);
            form.appendChild(pageInput);
            document.body.appendChild(form);
            form.submit();
        }

        // Function to open statistics modal
        function openStatModal(type, title) {
            const modal = new bootstrap.Modal(document.getElementById('statModal'));
            const modalTitle = document.getElementById('statModalTitle');
            const modalContent = document.getElementById('statModalContent');
            
            modalTitle.innerHTML = `<i class="bi bi-info-circle me-2"></i>${title}`;
            
            // Show or hide tabs based on type
            if (type === 'all_bills') {
                fetchBillData(type);
            } else {
                modalContent.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">กำลังโหลดข้อมูล...</p></div>';
                fetchStatData(type);
            }
            
            modal.show();
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

                    if (!data.items || data.items.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'ไม่พบข้อมูล',
                            text: 'ไม่มีข้อมูลที่สามารถแสดงได้',
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