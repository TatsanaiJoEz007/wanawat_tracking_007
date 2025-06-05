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
header('Content-Type: application/json');

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];

// ตรวจสอบสิทธิ์ในการเข้าถึงหน้า manage
if (!isset($permissions['manage_permission']) || $permissions['manage_permission'] != 1) {
    echo '<script>alert("คุณไม่มีสิทธิ์เข้าถึงหน้านี้"); location.href="dashboard.php"</script>';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action'])) {
        $response = [];
        
        switch($data['action']) {
            case 'update_permission':
                $role_id = $data['role_id'];
                $permission = $data['permission'];
                $value = $data['value'];
                
                $sql = "UPDATE tb_role SET $permission = ? WHERE role_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $value, $role_id);
                
                $response = ['success' => $stmt->execute()];
                break;
                
            case 'get_users':
                $role_id = isset($data['role_id']) ? $data['role_id'] : null;
                $response = getAllUsers($role_id);
                break;
                
            case 'get_permissions':
                $role_id = $data['role_id'];
                $response = getRolePermissions($role_id);
                break;
        }
        
        echo json_encode($response);
        exit;
    }
}

// ถ้าไม่ใช่ POST request ให้แสดงหน้าเว็บตามปกติ
header('Content-Type: text/html; charset=utf-8');

// เพิ่ม debug
if (isset($_GET['debug'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
}

function getRolePermissions($role_id = null) {
    global $conn;
    if ($role_id !== null) {
        $sql = "SELECT * FROM tb_role WHERE role_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $role_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $role = $result->fetch_assoc();
        
        $permissions = [];
        if ($role) {
            foreach ($role as $key => $value) {
                if ($key != 'role_id' && $key != 'role_name' && $value == 1) {
                    $permissions[] = $key;
                }
            }
        }
        return $permissions;
    } else {
        // กำหนด permissions ตาม role แบบ hardcode
        return [
            999 => ['manage_permission', 'manage_website', 'manage_logs'], // Admin
            1 => ['manage_csv', 'manage_statusbill', 'manage_history', 'manage_problem', 'manage_ic_delivery', 'manage_iv_delivery'] // Employee
        ];
    }
}

function getAllUsers($role_id = null) {
    global $conn;
    $sql = "SELECT * FROM tb_user WHERE user_status = 1";
    if ($role_id !== null && $role_id !== '') {
        $sql .= " AND user_type = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $role_id);
    } else {
        $stmt = $conn->prepare($sql);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function getAllRoles() {
    global $conn;
    $sql = "SELECT * FROM tb_role WHERE role_id != 0";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

$role_id = isset($_SESSION['role_id']) ? $_SESSION['role_id'] : 999;

// กำหนด permission labels
$permission_labels = [
    'manage_permission' => 'จัดการสิทธิ์ผู้ใช้',
    'manage_website' => 'จัดการเว็บไซต์',
    'manage_logs' => 'จัดการประวัติกิจกรรม',
    'manage_csv' => 'จัดการไฟล์ CSV',
    'manage_statusbill' => 'จัดการสถานะบิล',
    'manage_history' => 'จัดการประวัติ',
    'manage_problem' => 'จัดการปัญหา',
    'manage_ic_delivery' => 'จัดการใบส่งสินค้า IC',
    'manage_iv_delivery' => 'จัดการใบส่งสินค้า IV'
];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>Permission Management - Wanawat Tracking System</title>
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
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
            justify-content: center;
        }
        
        .page-title i {
            margin-right: 10px;
            color: #F0592E;
        }

        .page-subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
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

        /* Role Filter Section */
        .role-filter-section {
            margin-bottom: 25px;
            padding: 20px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(240, 89, 46, 0.2);
            box-shadow: 0 8px 32px rgba(240, 89, 46, 0.1);
        }

        .filter-header {
            font-weight: 600;
            color: #F0592E;
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
        
        .filter-header i {
            margin-right: 8px;
        }

        .form-select {
            border: 2px solid rgba(240, 89, 46, 0.2);
            border-radius: 10px;
            padding: 12px 16px;
            background: rgba(255, 255, 255, 0.9);
            color: #2d3748;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: #F0592E;
            box-shadow: 0 0 0 0.2rem rgba(240, 89, 46, 0.25);
            background: rgba(255, 255, 255, 1);
        }

        /* Permissions Section */
        .permissions-section {
            margin-bottom: 25px;
            padding: 20px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(240, 89, 46, 0.2);
            box-shadow: 0 8px 32px rgba(240, 89, 46, 0.1);
        }

        .permissions-header {
            font-weight: 600;
            color: #F0592E;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 1.1rem;
            justify-content: center;
        }
        
        .permissions-header i {
            margin-right: 8px;
        }

        .permissions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .permission-badge {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .permission-badge:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.4);
        }

        .permission-badge:active {
            transform: translateY(-1px);
        }

        .permission-badge.disabled {
            background: linear-gradient(135deg, #9E9E9E, #757575);
            cursor: not-allowed;
            transform: none;
        }

        .permission-badge.disabled:hover {
            background: linear-gradient(135deg, #9E9E9E, #757575);
            transform: none;
            box-shadow: 0 4px 15px rgba(158, 158, 158, 0.3);
        }

        .clear-button {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            display: block;
            width: 100%;
            text-align: center;
            box-shadow: 0 4px 15px rgba(245, 101, 101, 0.3);
        }

        .clear-button:hover {
            background: linear-gradient(135deg, #e53e3e, #c53030);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(245, 101, 101, 0.4);
        }

        /* Users Table Section */
        .users-section {
            padding: 20px;
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(240, 89, 46, 0.2);
            box-shadow: 0 8px 32px rgba(240, 89, 46, 0.1);
        }

        .users-header {
            font-weight: 600;
            color: #F0592E;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }
        
        .users-header i {
            margin-right: 8px;
        }

        .table-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 0;
        }

        .user-table th {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            border: none;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .user-table td {
            padding: 12px;
            border-bottom: 1px solid rgba(240, 89, 46, 0.1);
            color: #2d3748;
        }

        .user-table tr:nth-child(even) {
            background-color: rgba(240, 89, 46, 0.05);
        }

        .user-table tr:hover {
            background-color: rgba(240, 89, 46, 0.1);
            transition: background-color 0.3s;
        }

        .user-permissions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .user-permission-item {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            font-weight: 500;
            box-shadow: 0 2px 8px rgba(72, 187, 120, 0.3);
        }

        .user-permission-item .remove-permission {
            background: none;
            border: none;
            color: white;
            margin-left: 8px;
            cursor: pointer;
            font-weight: bold;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .user-permission-item .remove-permission:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        /* Custom Checkbox */
        .custom-checkbox {
            width: 18px;
            height: 18px;
            accent-color: #F0592E;
            cursor: pointer;
        }

        /* Loading States */
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

        /* Responsive Design */
        @media (max-width: 768px) {
            .permissions-grid {
                grid-template-columns: 1fr;
            }
            
            .user-table {
                font-size: 0.9rem;
            }
            
            .user-table th,
            .user-table td {
                padding: 8px;
            }
            
            .user-permissions {
                flex-direction: column;
                gap: 5px;
            }
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
    </style>
</head>

<body>
    <!-- Include Sidebar -->
    <?php include('function/sidebar.php'); ?>

    <!-- Main Content - ใช้ class เดียวกับ activity.php -->
    <div class="content">
        <!-- Header with menu button -->
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text">Permission Management</span>
        </div>
        
        <div class="container">
            <a href="dashboard.php" class="back-button">
                <i class="bi bi-arrow-left"></i> กลับไปหน้า Dashboard
            </a>
            
            <div class="page-title">
                <i class="bi bi-shield-lock"></i>ควบคุมสิทธิการเข้าถึง
            </div>
            <div class="page-subtitle">
                เลือกสิทธิการเข้าถึงสำหรับแต่ละประเภทผู้ใช้ว่าสามารถเข้าถึงสิทธิไหนได้บ้าง
            </div>

            <!-- Role Filter Section -->
            <div class="role-filter-section">
                <div class="filter-header">
                    <i class="bi bi-funnel"></i> เลือกประเภทผู้ใช้
                </div>
                <select id="roleFilter" class="form-select">
                    <option value="">เลือกประเภทผู้ใช้...</option>
                    <?php 
                    $roles = getAllRoles();
                    foreach($roles as $role): 
                        $role_display = $role['role_name'];
                        if ($role['role_id'] == 999) $role_display = 'ผู้ดูแลระบบ (Admin)';
                        if ($role['role_id'] == 1) $role_display = 'พนักงาน (Employee)';
                    ?>
                        <option value="<?= $role['role_id'] ?>"><?= $role_display ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Permissions Section -->
            <div class="permissions-section">
                <div class="permissions-header">
                    <i class="bi bi-key"></i> เลือกสิทธิการเข้าถึง
                </div>
                <div class="permissions-grid" id="permissionsGrid">
                    <!-- Permissions will be loaded here by JavaScript -->
                </div>
                <button id="clear-permissions" class="clear-button" disabled>
                    <i class="bi bi-trash me-2"></i>ล้างสิทธิทั้งหมด
                </button>
            </div>

            <!-- Users Table Section -->
            <div class="users-section">
                <div class="users-header">
                    <i class="bi bi-people"></i> รายชื่อผู้ใช้งาน
                </div>
                <div class="table-container">
                    <div class="loading-overlay" id="loadingOverlay">
                        <div class="spinner"></div>
                    </div>
                    <table class="user-table">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <input type="checkbox" id="select-all" class="custom-checkbox">
                                </th>
                                <th width="25%">ชื่อผู้ใช้</th>
                                <th width="15%">ประเภท</th>
                                <th width="55%">สิทธิการเข้าถึงปัจจุบัน</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="bi bi-person-plus"></i>
                                    <div>กรุณาเลือกประเภทผู้ใช้เพื่อแสดงรายชื่อ</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ไม่ต้องมี sidebar JavaScript ที่นี่ เพราะ sidebar.php จะจัดการให้แล้ว

        // Permission labels in Thai
        const permissionLabels = <?php echo json_encode($permission_labels); ?>;
        
        // Role-based permissions
        const rolePermissions = {
            999: ['manage_permission', 'manage_website', 'manage_logs'], // Admin
            1: ['manage_csv', 'manage_statusbill', 'manage_history', 'manage_problem', 'manage_ic_delivery', 'manage_iv_delivery'] // Employee
        };

        let currentRoleId = null;

        async function getUserPermissions(roleId) {
            try {
                const response = await fetch('Manage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'get_permissions',
                        role_id: roleId
                    })
                });
                return await response.json();
            } catch (error) {
                console.error('Error:', error);
                return [];
            }
        }

        async function getUsers(roleId = null) {
            try {
                showLoading();
                const response = await fetch('Manage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'get_users',
                        role_id: roleId || null
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const text = await response.text();
                console.log('Response text:', text);

                try {
                    const data = JSON.parse(text);
                    return Array.isArray(data) ? data : [];
                } catch (e) {
                    console.error('JSON parse error:', e);
                    return [];
                }
            } catch (error) {
                console.error('Fetch error:', error);
                return [];
            } finally {
                hideLoading();
            }
        }

        async function updatePermission(roleId, permission, value) {
            try {
                const response = await fetch('Manage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'update_permission',
                        role_id: roleId,
                        permission: permission,
                        value: value
                    })
                });
                return await response.json();
            } catch (error) {
                console.error('Error:', error);
                return {success: false};
            }
        }

        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        function updatePermissionsGrid(roleId) {
            const permissionsGrid = document.getElementById('permissionsGrid');
            const clearButton = document.getElementById('clear-permissions');
            
            if (!roleId) {
                permissionsGrid.innerHTML = '<div class="empty-state"><i class="bi bi-key"></i><div>กรุณาเลือกประเภทผู้ใช้เพื่อแสดงสิทธิการเข้าถึง</div></div>';
                clearButton.disabled = true;
                return;
            }

            const permissions = rolePermissions[roleId] || [];
            clearButton.disabled = false;
            
            if (permissions.length === 0) {
                permissionsGrid.innerHTML = '<div class="empty-state"><i class="bi bi-exclamation-triangle"></i><div>ไม่พบสิทธิสำหรับประเภทผู้ใช้นี้</div></div>';
                return;
            }

            permissionsGrid.innerHTML = permissions.map(permission => {
                const label = permissionLabels[permission] || permission;
                const icon = getPermissionIcon(permission);
                return `
                    <button class="permission-badge" data-permission="${permission}">
                        <i class="bi ${icon}"></i>
                        ${label}
                    </button>
                `;
            }).join('');

            // Add click handlers to permission badges
            document.querySelectorAll('.permission-badge').forEach(button => {
                button.addEventListener('click', async () => {
                    const selectedPermission = button.getAttribute('data-permission');
                    
                    if (!currentRoleId) {
                        await Swal.fire({
                            icon: 'warning',
                            title: 'กรุณาเลือกประเภทผู้ใช้',
                            text: 'กรุณาเลือกประเภทผู้ใช้ก่อนกำหนดสิทธิ',
                            confirmButtonColor: '#F0592E'
                        });
                        return;
                    }

                    // Show confirmation
                    const result = await Swal.fire({
                        title: 'ยืนยันการกำหนดสิทธิ',
                        text: `คุณต้องการกำหนดสิทธิ "${permissionLabels[selectedPermission]}" ให้กับ ${getRoleName(currentRoleId)} หรือไม่?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#F0592E',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'ยืนยัน',
                        cancelButtonText: 'ยกเลิก'
                    });

                    if (result.isConfirmed) {
                        showLoading();
                        const updateResult = await updatePermission(currentRoleId, selectedPermission, 1);
                        hideLoading();
                        
                        if (updateResult.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: 'กำหนดสิทธิเรียบร้อยแล้ว',
                                confirmButtonColor: '#F0592E',
                                timer: 1500
                            });
                            await updateUserTable(currentRoleId);
                        } else {
                            await Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถกำหนดสิทธิได้',
                                confirmButtonColor: '#F0592E'
                            });
                        }
                    }
                });
            });
        }

        function getPermissionIcon(permission) {
            const icons = {
                'manage_permission': 'bi-shield-check',
                'manage_website': 'bi-globe',
                'manage_logs': 'bi-activity',
                'manage_csv': 'bi-file-earmark-spreadsheet',
                'manage_statusbill': 'bi-receipt',
                'manage_history': 'bi-clock-history',
                'manage_problem': 'bi-exclamation-triangle',
                'manage_ic_delivery': 'bi-truck',
                'manage_iv_delivery': 'bi-box-seam'
            };
            return icons[permission] || 'bi-key';
        }

        function getRoleName(roleId) {
            const roleNames = {
                999: 'ผู้ดูแลระบบ',
                1: 'พนักงาน'
            };
            return roleNames[roleId] || 'ผู้ใช้';
        }

        async function updateUserTable(roleId = null) {
            try {
                const tbody = document.getElementById('userTableBody');
                const users = await getUsers(roleId);

                if (!roleId) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="bi bi-person-plus"></i>
                                <div>กรุณาเลือกประเภทผู้ใช้เพื่อแสดงรายชื่อ</div>
                            </td>
                        </tr>
                    `;
                    return;
                }

                if (!Array.isArray(users) || users.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="bi bi-person-x"></i>
                                <div>ไม่พบผู้ใช้ในประเภทนี้</div>
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                tbody.innerHTML = '';
                
                for (const user of users) {
                    const permissions = await getUserPermissions(user.user_type);
                    const permissionsHtml = permissions.map(p => {
                        const label = permissionLabels[p] || p;
                        return `<span class="user-permission-item" data-permission="${p}">
                            ${label}
                            <button class="remove-permission" title="ลบสิทธิ์นี้">×</button>
                         </span>`;
                    }).join('');

                    const userTypeDisplay = user.user_type == 999 ? 'ผู้ดูแลระบบ' : 
                                          user.user_type == 1 ? 'พนักงาน' : 'อื่นๆ';

                    tbody.innerHTML += `
                        <tr data-user-id="${user.user_id}" data-user-type="${user.user_type}">
                            <td><input type="checkbox" class="user-checkbox custom-checkbox"></td>
                            <td><strong>${user.user_firstname} ${user.user_lastname}</strong><br>
                                <small class="text-muted">${user.user_email || ''}</small></td>
                            <td><span class="badge" style="background: linear-gradient(135deg, #F0592E, #FF8A65); color: white;">${userTypeDisplay}</span></td>
                            <td><div class="user-permissions">${permissionsHtml}</div></td>
                        </tr>
                    `;
                }

                // Add remove permission handlers
                document.querySelectorAll('.remove-permission').forEach(button => {
                    button.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        const permissionItem = e.target.closest('.user-permission-item');
                        const permission = permissionItem.getAttribute('data-permission');
                        const row = e.target.closest('tr');
                        const userType = row.getAttribute('data-user-type');
                        
                        const result = await Swal.fire({
                            title: 'ยืนยันการลบสิทธิ',
                            text: `คุณต้องการลบสิทธิ "${permissionLabels[permission]}" หรือไม่?`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'ลบ',
                            cancelButtonText: 'ยกเลิก'
                        });

                        if (result.isConfirmed) {
                            showLoading();
                            const updateResult = await updatePermission(userType, permission, 0);
                            hideLoading();
                            
                            if (updateResult.success) {
                                await Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ',
                                    text: 'ลบสิทธิเรียบร้อยแล้ว',
                                    confirmButtonColor: '#F0592E',
                                    timer: 1500
                                });
                                await updateUserTable(currentRoleId);
                            }
                        }
                    });
                });
            } catch (error) {
                console.error('Update table error:', error);
            }
        }

        // Event Listeners
        document.getElementById('select-all').addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
        });

        document.getElementById('roleFilter').addEventListener('change', function() {
            currentRoleId = this.value ? parseInt(this.value) : null;
            updatePermissionsGrid(currentRoleId);
            updateUserTable(currentRoleId);
        });

        document.getElementById('clear-permissions').addEventListener('click', async () => {
            if (!currentRoleId) {
                await Swal.fire({
                    icon: 'warning',
                    title: 'กรุณาเลือกประเภทผู้ใช้',
                    text: 'กรุณาเลือกประเภทผู้ใช้ก่อนล้างสิทธิ',
                    confirmButtonColor: '#F0592E'
                });
                return;
            }

            const result = await Swal.fire({
                title: 'ยืนยันการล้างสิทธิทั้งหมด',
                text: `คุณต้องการล้างสิทธิทั้งหมดของ ${getRoleName(currentRoleId)} หรือไม่?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ล้างทั้งหมด',
                cancelButtonText: 'ยกเลิก'
            });

            if (result.isConfirmed) {
                showLoading();
                const permissions = rolePermissions[currentRoleId] || [];
                
                try {
                    for (const permission of permissions) {
                        await updatePermission(currentRoleId, permission, 0);
                    }
                    
                    await Swal.fire({
                        icon: 'success',
                        title: 'สำเร็จ',
                        text: 'ล้างสิทธิทั้งหมดเรียบร้อยแล้ว',
                        confirmButtonColor: '#F0592E',
                        timer: 1500
                    });
                    await updateUserTable(currentRoleId);
                } catch (error) {
                    await Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถล้างสิทธิได้',
                        confirmButtonColor: '#F0592E'
                    });
                } finally {
                    hideLoading();
                }
            }
        });

        // Initialize
        updatePermissionsGrid(null);
        updateUserTable(null);
    });
    </script>
</body>
</html>