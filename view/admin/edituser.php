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

// ตรวจสอบสิทธิ์ในการเข้าถึงหน้าจัดการผู้ใช้
if (!isset($permissions['manage_permission']) || $permissions['manage_permission'] != 1) {
    echo '<script>alert("คุณไม่มีสิทธิ์เข้าถึงหน้านี้"); location.href="dashboard.php"</script>';
    exit;
}

// Function to get user profile picture
function Profilepic($conn, $userId)
{
    $sql = "SELECT user_img FROM tb_user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
}

// Function to convert image data to base64
function base64img($imageData)
{
    return 'data:image/jpeg;base64,' . base64_encode($imageData);
}

// Get provinces for address selection
$provinces_query = "SELECT * FROM provinces ORDER BY name_th ASC";
$provinces_result = $conn->query($provinces_query);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>User Management - Wanawat Tracking System</title>
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

        /* Custom Tabs */
        .user-type-tabs {
            display: flex;
            margin-bottom: 25px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            padding: 5px;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .tab-button {
            flex: 1;
            padding: 12px 20px;
            border: none;
            background: transparent;
            color: #666;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.3s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .tab-button.active {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
        }

        .tab-button:hover:not(.active) {
            background: rgba(240, 89, 46, 0.1);
            color: #F0592E;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .btn-add {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
        }

        .btn-add:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.4);
            color: white;
        }

        .btn-delete-selected {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn-delete-selected:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
            color: white;
        }

        /* Table Styling */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            padding: 15px 12px;
            font-weight: 600;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .table tbody td {
            padding: 12px;
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

        /* User Avatar */
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(240, 89, 46, 0.3);
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            border-color: #F0592E;
            transform: scale(1.1);
        }

        /* Status Badge */
        .status-badge {
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-active {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .status-inactive {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
        }

        .status-pending {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
        }

        /* Action Buttons in Table */
        .table-action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            margin: 2px;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-reset {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
        }

        .btn-reset:hover {
            background: linear-gradient(135deg, #138496, #117a8b);
            transform: translateY(-1px);
            color: white;
        }

        .btn-edit {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            color: #212529;
        }

        .btn-edit:hover {
            background: linear-gradient(135deg, #e0a800, #d39e00);
            transform: translateY(-1px);
            color: #212529;
        }

        .btn-del {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
        }

        .btn-del:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-1px);
            color: white;
        }

        /* Modal Styling */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border-radius: 15px 15px 0 0;
            border: none;
        }

        .modal-title {
            font-weight: 600;
        }

        .btn-close {
            filter: brightness(0) invert(1);
        }

        .form-control, .form-select {
            border: 2px solid rgba(240, 89, 46, 0.2);
            border-radius: 8px;
            padding: 10px 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: #F0592E;
            box-shadow: 0 0 0 0.2rem rgba(240, 89, 46, 0.25);
        }

        .form-label {
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 8px;
        }

        /* Custom Checkbox */
        .custom-checkbox {
            width: 18px;
            height: 18px;
            accent-color: #F0592E;
            cursor: pointer;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #718096;
        }

        .empty-state i {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 15px;
            display: block;
        }

        /* Loading Overlay */
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

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .user-type-tabs {
                flex-direction: column;
                gap: 5px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .table-action-btn {
                font-size: 0.7rem;
                padding: 4px 8px;
            }
            
            .table {
                font-size: 0.9rem;
            }
        }

        /* Image Preview Styling */
        .image-preview {
            position: relative;
            display: inline-block;
            border: 2px dashed rgba(240, 89, 46, 0.3);
            border-radius: 10px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }

        .image-preview:hover {
            border-color: #F0592E;
            background: rgba(240, 89, 46, 0.05);
        }

        .preview-image {
            max-width: 200px;
            max-height: 200px;
            width: auto;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: block;
            margin-bottom: 10px;
        }

        .remove-image {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 0.8rem;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .remove-image:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            transform: translateY(-1px);
            color: white;
        }

        /* File input styling enhancement */
        .form-control[type="file"] {
            padding: 8px 12px;
        }

        .form-control[type="file"]:focus {
            border-color: #F0592E;
            box-shadow: 0 0 0 0.2rem rgba(240, 89, 46, 0.25);
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

        /* Customer ID Field Highlight */
        .customer-id-field {
            background: rgba(33, 150, 243, 0.05);
            border: 2px solid rgba(33, 150, 243, 0.3) !important;
        }

        .customer-id-field:focus {
            border-color: #2196F3 !important;
            box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25) !important;
        }

        .customer-id-label {
            color: #2196F3 !important;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <!-- Include Sidebar - ใช้เหมือน activity.php -->
    <?php include('function/sidebar.php'); ?>

    <!-- Main Content - ใช้ class เดียวกับ activity.php -->
    <div class="content">
        <!-- Header with menu button -->
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text">User Management</span>
        </div>
        
        <div class="container">
            <a href="dashboard" class="back-button">
                <i class="bi bi-arrow-left"></i> กลับไปหน้า Dashboard
            </a>
            
            <div class="page-title">
                <i class="bi bi-people"></i>จัดการผู้ใช้งานในระบบ
            </div>

            <!-- User Type Tabs -->
            <div class="user-type-tabs">
                <button class="tab-button active" data-type="admin">
                    <i class="bi bi-shield-check"></i> ผู้ดูแลระบบ
                </button>
                <button class="tab-button" data-type="employee">
                    <i class="bi bi-person-badge"></i> พนักงาน
                </button>
                <button class="tab-button" data-type="user">
                    <i class="bi bi-person"></i> ลูกค้า
                </button>
            </div>

            <!-- Admin Tab Content -->
            <div id="admin-content" class="tab-content active">
                <div class="action-buttons">
                    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#userModal" data-action="add" data-type="admin">
                        <i class="bi bi-plus-circle"></i> เพิ่มผู้ดูแลระบบ
                    </button>
                    <button class="btn-delete-selected" id="deleteSelectedAdmin">
                        <i class="bi bi-trash"></i> ลบที่เลือก
                    </button>
                </div>
                
                <div class="table-container">
                    <div class="loading-overlay" id="adminLoading">
                        <div class="spinner"></div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="5%"><input type="checkbox" class="custom-checkbox" id="selectAllAdmin"></th>
                                <th width="5%">#</th>
                                <th width="10%">รูปภาพ</th>
                                <th width="15%">ชื่อ - นามสกุล</th>
                                <th width="20%">อีเมล</th>
                                <th width="10%">สถานะ</th>
                                <th width="35%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="adminTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Employee Tab Content -->
            <div id="employee-content" class="tab-content">
                <div class="action-buttons">
                    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#userModal" data-action="add" data-type="employee">
                        <i class="bi bi-plus-circle"></i> เพิ่มพนักงาน
                    </button>
                    <button class="btn-delete-selected" id="deleteSelectedEmployee">
                        <i class="bi bi-trash"></i> ลบที่เลือก
                    </button>
                </div>
                
                <div class="table-container">
                    <div class="loading-overlay" id="employeeLoading">
                        <div class="spinner"></div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="5%"><input type="checkbox" class="custom-checkbox" id="selectAllEmployee"></th>
                                <th width="5%">#</th>
                                <th width="10%">รูปภาพ</th>
                                <th width="15%">ชื่อ - นามสกุล</th>
                                <th width="20%">อีเมล</th>
                                <th width="10%">สถานะ</th>
                                <th width="35%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="employeeTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- User Tab Content -->
            <div id="user-content" class="tab-content">
                <div class="action-buttons">
                    <button class="btn-add" data-bs-toggle="modal" data-bs-target="#userModal" data-action="add" data-type="user">
                        <i class="bi bi-plus-circle"></i> เพิ่มลูกค้า
                    </button>
                    <button class="btn-delete-selected" id="deleteSelectedUser">
                        <i class="bi bi-trash"></i> ลบที่เลือก
                    </button>
                </div>
                
                <div class="table-container">
                    <div class="loading-overlay" id="userLoading">
                        <div class="spinner"></div>
                    </div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th width="5%"><input type="checkbox" class="custom-checkbox" id="selectAllUser"></th>
                                <th width="5%">#</th>
                                <th width="8%">รูปภาพ</th>
                                <th width="12%">ชื่อ - นามสกุล</th>
                                <th width="10%">รหัสลูกค้า</th>
                                <th width="15%">อีเมล</th>
                                <th width="10%">เบอร์โทร</th>
                                <th width="10%">สถานะ</th>
                                <th width="25%">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <!-- Data will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- User Modal -->
    <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalTitle">เพิ่มผู้ใช้</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="userForm" enctype="multipart/form-data">
                        <input type="hidden" id="userId" name="user_id">
                        <input type="hidden" id="userType" name="user_type">
                        <input type="hidden" id="actionType" name="action">
                        <input type="hidden" id="removeImageFlag" name="remove_image" value="0">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="firstName" class="form-label">ชื่อ <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="firstName" name="firstname" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lastName" class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="lastName" name="lastname" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="email" class="form-label">อีเมล <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                        </div>

                        <!-- Additional fields for users -->
                        <div id="userExtraFields" style="display: none;">
                            <!-- Customer ID Field - เพิ่มใหม่ -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="customerIdField" class="form-label customer-id-label">
                                            <i class="bi bi-credit-card me-2"></i>รหัสลูกค้า <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control customer-id-field" id="customerIdField" name="customer_id" 
                                               placeholder="กรอกรหัสลูกค้า เช่น CUST001" maxlength="20" required>
                                        <div class="form-text">
                                            <i class="bi bi-info-circle me-1"></i>
                                            รหัสลูกค้าต้องเป็นตัวอักษรและตัวเลข ไม่เกิน 20 ตัวอักษร (ห้ามซ้ำกับรหัสเดิม)
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">เบอร์โทรศัพท์</label>
                                        <input type="text" class="form-control" id="phone" name="phone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">ที่อยู่</label>
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="province" class="form-label">จังหวัด</label>
                                        <select class="form-select" id="province" name="province_id">
                                            <option value="">เลือกจังหวัด</option>
                                            <?php 
                                            $provinces_result->data_seek(0);
                                            while ($province = $provinces_result->fetch_assoc()): 
                                            ?>
                                                <option value="<?= $province['id'] ?>"><?= $province['name_th'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="amphure" class="form-label">อำเภอ</label>
                                        <select class="form-select" id="amphure" name="amphure_id">
                                            <option value="">เลือกอำเภอ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="district" class="form-label">ตำบล</label>
                                        <select class="form-select" id="district" name="district_id">
                                            <option value="">เลือกตำบล</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="userImage" class="form-label">รูปภาพ</label>
                                    <input type="file" class="form-control" id="userImage" name="user_img" accept="image/*">
                                    <div class="image-preview mt-3" id="imagePreview" style="display: none;">
                                        <img id="previewImg" src="" alt="Preview" class="preview-image">
                                        <button type="button" class="btn btn-sm btn-danger remove-image" id="removeImage">
                                            <i class="bi bi-x-circle"></i> ลบรูป
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">สถานะ</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="1">อยู่ในระบบ</option>
                                        <option value="0">ไม่อยู่ในระบบ</option>
                                        <option value="9" selected>รอการยืนยัน</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" form="userForm" class="btn btn-primary" id="saveUserBtn">บันทึกข้อมูล</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เปลี่ยนรหัสผ่าน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="resetPasswordForm">
                        <input type="hidden" id="resetUserId" name="user_id">
                        <div class="mb-3">
                            <label for="newPassword" class="form-label">รหัสผ่านใหม่</label>
                            <input type="password" class="form-control" id="newPassword" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" form="resetPasswordForm" class="btn btn-primary">เปลี่ยนรหัสผ่าน</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer ID Modal -->
    <div class="modal fade" id="customerIdModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">แก้ไขรหัสลูกค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="customerIdForm">
                        <input type="hidden" id="customerUserId" name="user_id">
                        <div class="mb-3">
                            <label for="customerId" class="form-label">รหัสลูกค้า</label>
                            <input type="text" class="form-control" id="customerId" name="customer_id" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="submit" form="customerIdForm" class="btn btn-primary">บันทึก</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const type = this.getAttribute('data-type');
                
                // Update active tab
                tabButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Update active content
                tabContents.forEach(content => content.classList.remove('active'));
                document.getElementById(type + '-content').classList.add('active');
                
                // Clear select all checkbox
                const selectAllCheckbox = document.getElementById(`selectAll${type.charAt(0).toUpperCase() + type.slice(1)}`);
                if (selectAllCheckbox) {
                    selectAllCheckbox.checked = false;
                }
                
                // Load data for the selected tab
                loadUsers(type);
            });
        });

        // Modal functionality with proper accessibility
        const userModal = document.getElementById('userModal');
        userModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const action = button ? button.getAttribute('data-action') : 'add';
            const type = button ? button.getAttribute('data-type') : 'user';
            
            document.getElementById('actionType').value = action;
            document.getElementById('userType').value = getUserTypeId(type);
            document.getElementById('removeImageFlag').value = '0';
            
            // Update modal title
            const titles = {
                'admin': action === 'add' ? 'เพิ่มผู้ดูแลระบบ' : 'แก้ไขผู้ดูแลระบบ',
                'employee': action === 'add' ? 'เพิ่มพนักงาน' : 'แก้ไขพนักงาน',
                'user': action === 'add' ? 'เพิ่มลูกค้า' : 'แก้ไขลูกค้า'
            };
            document.getElementById('userModalTitle').textContent = titles[type];
            
            // Show/hide extra fields for users
            const extraFields = document.getElementById('userExtraFields');
            const customerIdField = document.getElementById('customerIdField');
            
            if (type === 'user') {
                extraFields.style.display = 'block';
                // Make customer ID required for users
                customerIdField.setAttribute('required', 'required');
            } else {
                extraFields.style.display = 'none';
                // Remove required attribute for non-users
                customerIdField.removeAttribute('required');
            }
            
            // Clear form if adding new user
            if (action === 'add') {
                document.getElementById('userForm').reset();
                document.getElementById('userId').value = '';
                document.getElementById('removeImageFlag').value = '0';
                // Set default status for new user
                document.getElementById('status').value = '9';
                // Hide image preview for new user
                document.getElementById('imagePreview').style.display = 'none';
                
                // Clear customer ID field for new users
                if (type === 'user') {
                    document.getElementById('customerIdField').value = '';
                }
            }
        });

        // Proper modal cleanup
        userModal.addEventListener('hidden.bs.modal', function() {
            // Reset form
            document.getElementById('userForm').reset();
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('removeImageFlag').value = '0';
            // Set default status for new user
            document.getElementById('status').value = '9';
            
            // Clear customer ID field
            document.getElementById('customerIdField').value = '';
            
            // Clear any validation messages
            const formControls = userModal.querySelectorAll('.form-control, .form-select');
            formControls.forEach(control => {
                control.classList.remove('is-invalid');
            });
        });

        // Customer ID validation
        document.getElementById('customerIdField').addEventListener('input', function(e) {
            const value = e.target.value;
            // Allow only alphanumeric characters
            const alphanumeric = value.replace(/[^A-Za-z0-9]/g, '');
            if (value !== alphanumeric) {
                e.target.value = alphanumeric;
            }
            
            // Limit to 20 characters
            if (alphanumeric.length > 20) {
                e.target.value = alphanumeric.substring(0, 20);
            }
        });

        // Form submissions with improved modal handling
        document.getElementById('userForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Disable submit button to prevent double submission
            const submitBtn = document.getElementById('saveUserBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>กำลังบันทึก...';
            
            // Validate required fields
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('email').value.trim();
            const userType = document.getElementById('userType').value;
            
            if (!firstName || !lastName || !email) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                Swal.fire({
                    icon: 'warning',
                    title: 'ข้อมูลไม่ครบถ้วน',
                    text: 'กรุณากรอกชื่อ นามสกุล และอีเมลให้ครบถ้วน'
                });
                return;
            }
            
            // Validate customer ID for users
            if (userType === '0') { // User type
                const customerId = document.getElementById('customerIdField').value.trim();
                if (!customerId) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    Swal.fire({
                        icon: 'warning',
                        title: 'ข้อมูลไม่ครบถ้วน',
                        text: 'กรุณากรอกรหัสลูกค้า'
                    });
                    return;
                }
                
                // Validate customer ID format
                const customerIdRegex = /^[A-Za-z0-9]{1,20}$/;
                if (!customerIdRegex.test(customerId)) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    Swal.fire({
                        icon: 'warning',
                        title: 'รูปแบบรหัสลูกค้าไม่ถูกต้อง',
                        text: 'รหัสลูกค้าต้องเป็นตัวอักษรและตัวเลข ไม่เกิน 20 ตัวอักษร'
                    });
                    return;
                }
            }
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                Swal.fire({
                    icon: 'warning',
                    title: 'อีเมลไม่ถูกต้อง',
                    text: 'กรุณากรอกอีเมลในรูปแบบที่ถูกต้อง'
                });
                return;
            }
            
            const formData = new FormData(this);
            
            // Handle remove image flag
            const removeImageFlag = document.getElementById('removeImageFlag').value;
            if (removeImageFlag === '1') {
                formData.set('remove_image', '1');
            }
            
            // Debug: Check if image file is being sent
            const imageFile = document.getElementById('userImage').files[0];
            if (imageFile) {
                console.log('Image file selected:', {
                    name: imageFile.name,
                    size: imageFile.size,
                    type: imageFile.type
                });
            } else {
                console.log('No image file selected');
            }
            
            $.ajax({
                url: 'function/user_actions.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    
                    if (response.success) {
                        // Get the modal instance and hide it properly
                        const modalElement = document.getElementById('userModal');
                        const modalInstance = bootstrap.Modal.getInstance(modalElement);
                        
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        
                        // Debug: Log success response
                        console.log('Success response:', response);
                        
                        // Wait for modal to be hidden before showing success message
                        modalElement.addEventListener('hidden.bs.modal', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                const activeType = document.querySelector('.tab-button.active').getAttribute('data-type');
                                loadUsers(activeType);
                            });
                        }, { once: true });
                        
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message || 'ไม่สามารถดำเนินการได้'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                    
                    console.error('AJAX Error:', {
                        status: xhr.status,
                        statusText: xhr.statusText,
                        responseText: xhr.responseText,
                        error: error
                    });
                    
                    let errorMessage = 'ไม่สามารถประมวลผลคำขอได้';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 400) {
                        errorMessage = 'ข้อมูลที่ส่งไม่ถูกต้อง กรุณาตรวจสอบและลองใหม่';
                    } else if (xhr.status === 403) {
                        errorMessage = 'คุณไม่มีสิทธิ์ในการดำเนินการนี้';
                    } else if (xhr.status === 500) {
                        errorMessage = 'เกิดข้อผิดพลาดของเซิร์ฟเวอร์';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: errorMessage,
                        footer: xhr.status !== 200 ? `รหัสข้อผิดพลาด: ${xhr.status}` : ''
                    });
                }
            });
        });

        // Reset password form
        document.getElementById('resetPasswordForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            
            if (newPassword !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'รหัสผ่านไม่ตรงกัน',
                    text: 'กรุณาตรวจสอบรหัสผ่านอีกครั้ง'
                });
                return;
            }
            
            if (newPassword.length < 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'รหัสผ่านสั้นเกินไป',
                    text: 'รหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร'
                });
                return;
            }
            
            const formData = new FormData(this);
            
            $.ajax({
                url: 'function/reset_password.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Hide modal properly
                        const modal = bootstrap.Modal.getInstance(document.getElementById('resetPasswordModal'));
                        if (modal) {
                            modal.hide();
                        } else {
                            $('#resetPasswordModal').modal('hide');
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        // Clear form
                        document.getElementById('resetPasswordForm').reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเปลี่ยนรหัสผ่านได้'
                    });
                }
            });
        });

        // Customer ID form
        document.getElementById('customerIdForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const customerId = document.getElementById('customerId').value.trim();
            if (!customerId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณากรอกรหัสลูกค้า',
                    text: 'รหัสลูกค้าไม่สามารถเว้นว่างได้'
                });
                return;
            }
            
            // Validate customer ID format
            const customerIdRegex = /^[A-Za-z0-9]{1,20}$/;
            if (!customerIdRegex.test(customerId)) {
                Swal.fire({
                    icon: 'warning',
                    title: 'รูปแบบรหัสลูกค้าไม่ถูกต้อง',
                    text: 'รหัสลูกค้าต้องเป็นตัวอักษรและตัวเลข ไม่เกิน 20 ตัวอักษร'
                });
                return;
            }
            
            const formData = new FormData(this);
            
            $.ajax({
                url: 'function/update_customer_id.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Hide modal properly
                        const modal = bootstrap.Modal.getInstance(document.getElementById('customerIdModal'));
                        if (modal) {
                            modal.hide();
                        } else {
                            $('#customerIdModal').modal('hide');
                        }
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: 'อัปเดตรหัสลูกค้าเรียบร้อยแล้ว',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            loadUsers('user');
                        });
                        
                        // Clear form
                        document.getElementById('customerIdForm').reset();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถอัปเดตรหัสลูกค้าได้'
                    });
                }
            });
        });

        // Image preview functionality
        document.getElementById('userImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            
            if (file) {
                // Check if file is an image
                if (file.type.startsWith('image/')) {
                    // Check file size (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'ไฟล์ใหญ่เกินไป',
                            text: 'กรุณาเลือกไฟล์รูปภาพที่มีขนาดไม่เกิน 5MB'
                        });
                        e.target.value = '';
                        preview.style.display = 'none';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        preview.style.display = 'block';
                        // Reset remove image flag when new image is selected
                        document.getElementById('removeImageFlag').value = '0';
                    };
                    reader.readAsDataURL(file);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไฟล์ไม่ถูกต้อง',
                        text: 'กรุณาเลือกไฟล์รูปภาพเท่านั้น (JPG, PNG, GIF)'
                    });
                    e.target.value = '';
                    preview.style.display = 'none';
                }
            } else {
                preview.style.display = 'none';
            }
        });

        // Remove image functionality
        document.getElementById('removeImage').addEventListener('click', function() {
            Swal.fire({
                title: 'ยืนยันการลบรูป',
                text: 'คุณต้องการลบรูปภาพนี้หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Clear file input
                    document.getElementById('userImage').value = '';
                    // Hide preview
                    document.getElementById('imagePreview').style.display = 'none';
                    // Set remove flag
                    document.getElementById('removeImageFlag').value = '1';
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'ลบรูปแล้ว',
                        text: 'รูปภาพจะถูกลบเมื่อบันทึกข้อมูล',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        // Location selectors
        $('#province').change(function() {
            const provinceId = $(this).val();
            if (provinceId) {
                $.ajax({
                    url: 'function/get_amphures.php',
                    type: 'GET',
                    data: { province_id: provinceId },
                    success: function(response) {
                        $('#amphure').html(response);
                        $('#district').html('<option value="">เลือกตำบล</option>');
                    }
                });
            }
        });

        $('#amphure').change(function() {
            const amphureId = $(this).val();
            if (amphureId) {
                $.ajax({
                    url: 'function/get_districts.php',
                    type: 'GET',
                    data: { amphure_id: amphureId },
                    success: function(response) {
                        $('#district').html(response);
                    }
                });
            }
        });

        // Helper functions
        function getUserTypeId(type) {
            const types = {
                'admin': 999,
                'employee': 1,
                'user': 0
            };
            return types[type];
        }

        function loadUsers(type) {
            const userTypeId = getUserTypeId(type);
            const tableBody = document.getElementById(type + 'TableBody');
            const loading = document.getElementById(type + 'Loading');
            
            if (loading) loading.style.display = 'flex';
            
            $.ajax({
                url: 'function/get_user.php',
                type: 'GET',
                data: { user_type: userTypeId },
                dataType: 'json',
                success: function(response) {
                    if (loading) loading.style.display = 'none';
                    
                    if (response.success) {
                        renderUserTable(type, response.data);
                    } else {
                        const colSpan = type === 'user' ? '9' : '7';
                        tableBody.innerHTML = `
                            <tr>
                                <td colspan="${colSpan}" class="empty-state">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    <div>เกิดข้อผิดพลาดในการโหลดข้อมูล</div>
                                </td>
                            </tr>
                        `;
                    }
                },
                error: function() {
                    if (loading) loading.style.display = 'none';
                    const colSpan = type === 'user' ? '9' : '7';
                    tableBody.innerHTML = `
                        <tr>
                            <td colspan="${colSpan}" class="empty-state">
                                <i class="bi bi-wifi-off"></i>
                                <div>ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้</div>
                            </td>
                        </tr>
                    `;
                }
            });
        }

        function renderUserTable(type, users) {
            const tableBody = document.getElementById(type + 'TableBody');
            const colSpan = type === 'user' ? '9' : '7';
            
            if (!users || users.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="${colSpan}" class="empty-state">
                            <i class="bi bi-people"></i>
                            <div>ไม่พบข้อมูลผู้ใช้</div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            users.forEach((user, index) => {
                const avatar = user.user_img ? 
                    `data:image/jpeg;base64,${user.user_img}` : 
                    '../../view/assets/img/logo/mascot.png';
                
                // Debug: Log user image status
                console.log(`User ${user.user_id}: Has image = ${!!user.user_img}, Image size = ${user.user_img ? user.user_img.length : 0}`);
                
                const statusClass = user.user_status == 1 ? 'status-active' : user.user_status == 9 ? 'status-pending' : 'status-inactive';
                const statusText = user.user_status == 1 ? 'อยู่ในระบบ' : user.user_status == 9 ? 'รอการยืนยัน' : 'ไม่อยู่ในระบบ';
                
                html += `<tr>`;
                
                // เพิ่ม checkbox สำหรับทุก type
                html += `<td><input type="checkbox" class="custom-checkbox user-checkbox" value="${user.user_id}"></td>`;
                
                html += `
                    <td>${index + 1}</td>
                    <td><img src="${avatar}" alt="Avatar" class="user-avatar"></td>
                    <td><strong>${user.user_firstname} ${user.user_lastname}</strong></td>
                `;
                
                if (type === 'user') {
                    html += `<td>${user.customer_id || '-'}</td>`;
                }
                
                html += `
                    <td>${user.user_email}</td>
                `;
                
                if (type === 'user') {
                    html += `<td>${user.user_tel || '-'}</td>`;
                }
                
                html += `
                    <td><span class="status-badge ${statusClass}">${statusText}</span></td>
                    <td>
                        <button class="table-action-btn btn-edit" onclick="editUser(${user.user_id}, '${type}')">
                            <i class="bi bi-pencil"></i> แก้ไข
                        </button>
                        <button class="table-action-btn btn-reset" onclick="resetPassword(${user.user_id})">
                            <i class="bi bi-key"></i> Reset
                        </button>
                `;
                
                if (type === 'user') {
                    html += `
                        <button class="table-action-btn btn-edit" onclick="editCustomerId(${user.user_id}, '${user.customer_id || ''}')">
                            <i class="bi bi-credit-card"></i> รหัสลูกค้า
                        </button>
                    `;
                }
                
                html += `
                        <button class="table-action-btn btn-del" onclick="deleteUser(${user.user_id})">
                            <i class="bi bi-trash"></i> ลบ
                        </button>
                    </td>
                </tr>
                `;
            });
            
            tableBody.innerHTML = html;
        }

        window.editUser = function(userId, userType) {
            // Fetch user data
            $.ajax({
                url: 'function/get_user_detail.php',
                type: 'GET',
                data: { user_id: userId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const user = response.data;
                        
                        // Populate form fields
                        document.getElementById('userId').value = user.user_id;
                        document.getElementById('actionType').value = 'edit';
                        document.getElementById('userType').value = getUserTypeId(userType);
                        document.getElementById('removeImageFlag').value = '0';
                        document.getElementById('firstName').value = user.user_firstname || '';
                        document.getElementById('lastName').value = user.user_lastname || '';
                        document.getElementById('email').value = user.user_email || '';
                        document.getElementById('status').value = user.user_status || '1';
                        
                        // Populate extra fields for users
                        if (userType === 'user') {
                            document.getElementById('customerIdField').value = user.customer_id || '';
                            document.getElementById('phone').value = user.user_tel || '';
                            document.getElementById('address').value = user.user_address || '';
                            
                            // Set location selectors
                            if (user.province_id) {
                                $('#province').val(user.province_id).trigger('change');
                                
                                // Load amphures and set value
                                setTimeout(() => {
                                    if (user.amphure_id) {
                                        $('#amphure').val(user.amphure_id).trigger('change');
                                        
                                        // Load districts and set value
                                        setTimeout(() => {
                                            if (user.district_id) {
                                                $('#district').val(user.district_id);
                                            }
                                        }, 500);
                                    }
                                }, 500);
                            }
                        }
                        
                        // Show existing image if available
                        if (user.user_img) {
                            const previewImg = document.getElementById('previewImg');
                            const preview = document.getElementById('imagePreview');
                            previewImg.src = `data:image/jpeg;base64,${user.user_img}`;
                            preview.style.display = 'block';
                        } else {
                            document.getElementById('imagePreview').style.display = 'none';
                        }
                        
                        // Show modal
                        $('#userModal').modal('show');
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถดึงข้อมูลผู้ใช้ได้'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้'
                    });
                }
            });
        };

        window.resetPassword = function(userId) {
            document.getElementById('resetUserId').value = userId;
            $('#resetPasswordModal').modal('show');
        };

        window.editCustomerId = function(userId, currentId) {
            document.getElementById('customerUserId').value = userId;
            document.getElementById('customerId').value = currentId;
            $('#customerIdModal').modal('show');
        };

        window.deleteUser = function(userId) {
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: 'คุณต้องการลบผู้ใช้นี้หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ลบ',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'function/delete_user.php',
                        type: 'POST',
                        data: { user_id: userId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ',
                                    text: 'ลบผู้ใช้เรียบร้อยแล้ว',
                                    timer: 1500
                                }).then(() => {
                                    const activeType = document.querySelector('.tab-button.active').getAttribute('data-type');
                                    loadUsers(activeType);
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: response.message
                                });
                            }
                        }
                    });
                }
            });
        };

        // Select all functionality for admin, employee, user
        document.getElementById('selectAllAdmin').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('#admin-content .user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        document.getElementById('selectAllEmployee').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('#employee-content .user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        document.getElementById('selectAllUser').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('#user-content .user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Delete selected functions
        function handleDeleteSelected(type, typeName) {
            const selectedIds = [];
            const checkboxes = document.querySelectorAll(`#${type}-content .user-checkbox:checked`);
            
            checkboxes.forEach(checkbox => {
                selectedIds.push(checkbox.value);
            });
            
            if (selectedIds.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกผู้ใช้',
                    text: `กรุณาเลือก${typeName}ที่ต้องการลบ`
                });
                return;
            }
            
            Swal.fire({
                title: 'ยืนยันการลบ',
                text: `คุณต้องการลบ${typeName} ${selectedIds.length} คนหรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ลบทั้งหมด',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'function/delete_users.php',
                        type: 'POST',
                        data: { user_ids: selectedIds },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ',
                                    text: `ลบ${typeName}เรียบร้อยแล้ว`,
                                    timer: 1500
                                }).then(() => {
                                    document.getElementById(`selectAll${type.charAt(0).toUpperCase() + type.slice(1)}`).checked = false;
                                    loadUsers(type);
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: response.message
                                });
                            }
                        }
                    });
                }
            });
        }

        // Delete selected admin users
        document.getElementById('deleteSelectedAdmin').addEventListener('click', function() {
            handleDeleteSelected('admin', 'ผู้ดูแลระบบ');
        });

        // Delete selected employee users
        document.getElementById('deleteSelectedEmployee').addEventListener('click', function() {
            handleDeleteSelected('employee', 'พนักงาน');
        });

        // Delete selected regular users
        document.getElementById('deleteSelectedUser').addEventListener('click', function() {
            handleDeleteSelected('user', 'ลูกค้า');
        });

        // Load initial data for all tabs
        loadUsers('admin');
        
        // Pre-load other tabs data in background for better UX
        setTimeout(() => {
            loadUsers('employee');
            loadUsers('user');
        }, 1000);
    });
    </script>
</body>
</html>