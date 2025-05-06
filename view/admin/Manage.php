<?php
require_once('../config/connect.php');
header('Content-Type: application/json');



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
        $sql = "SHOW COLUMNS FROM tb_role WHERE Field NOT IN ('role_id', 'role_name')";
        $result = $conn->query($sql);
        $permissions = [];
        while($row = $result->fetch_assoc()) {
            $permissions[] = $row['Field'];
        }
        return $permissions;
    }
}

function getAllUsers($role_id = null) {
    global $conn;
    $sql = "SELECT * FROM tb_user";
    if ($role_id !== null && $role_id !== '') {
        $sql .= " WHERE user_type = ?";
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
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <title>Manage - Manage</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
</head>

<style>
* {
  box-sizing: border-box;
}

body {
  font-family: 'Kanit', sans-serif;
  background-color: #f5f6fa;
  margin: 0;
  padding: 20px;
}

.access-control {
  width: 100%;
  max-width: 100%;
  margin: auto;
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  padding: 24px;
}

.access-control h2 {
  font-size: 1.8rem;
  color: #333;
  margin-bottom: 8px;
  text-align: center;
}

.access-control h3 {
  font-size: 1rem;
  color: #666;
  text-align: center;
  margin-bottom: 16px;
}

.content {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.container {
  background: #fff;
  border-radius: 8px;
  padding: 16px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
}

.user-table {
  width: 100%;
  border-collapse: collapse;
}

.user-table th, .user-table td {
  padding: 12px;
  text-align: left;
  border-bottom: 1px solid #e0e0e0;
}

.user-table th {
  background-color: #f0f0f0;
  font-weight: bold;
  color: #333;
}

.user-table tr:hover {
  background-color: #f9f9f9;
}

.permission-input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  outline: none;
  transition: border-color 0.3s;
}

.permission-input:focus {
  border-color: #00b300;
}

.permissions-container h4 {
  margin-bottom: 8px;
  color: #333;
  font-size: 1.2rem;
  text-align: center;
}

.permissions {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  justify-content: center;
}

.permission-badge {
  background-color: #00b300;
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 20px;
  cursor: pointer;
  transition: background-color 0.3s, transform 0.3s;
  font-size: 0.9rem;
}

.permission-badge:hover {
  background-color: #008f00;
  transform: translateY(-2px);
}

.permission-badge:active {
  background-color: #006b00;
}

.clear-button {
  background-color: #ff0000;
  color: #fff;
  border: none;
  padding: 10px 16px;
  border-radius: 8px;
  cursor: pointer;
  transition: background-color 0.3s;
  margin-top: 16px;
  display: block;
  width: 100%;
  text-align: center;
}

.clear-button:hover {
  background-color: #cc0000;
}

.user-permissions {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.user-permission-item {
  background-color: #00b300;
  color: #fff;
  padding: 4px 8px;
  border-radius: 12px;
  display: flex;
  align-items: center;
}

.user-permission-item .remove-permission {
  background: none;
  border: none;
  color: #fff;
  margin-left: 8px;
  cursor: pointer;
  font-weight: bold;
}

.user-permission-item .remove-permission:hover {
  color: #ff0000;
}
</style>

<body>
<?php require_once('function/sidebar.php'); ?>

<div class="access-control">
    <h2>ควบคุมสิทธิการเข้าถึง</h2>
    <h3>เลือกสิทธิการเข้าถึงสำหรับ แต่ละ User ว่าเข้าถึงสิทธิไหนได้บ้าง</h3>

    <div class="content">
        <div class="container">
            <select id="roleFilter" class="form-select mb-3">
                <option value="">ทั้งหมด</option>
                <?php 
                $roles = getAllRoles();
                foreach($roles as $role): ?>
                    <option value="<?= $role['role_id'] ?>"><?= $role['role_name'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="container permissions-container">
            <h4>เลือกสิทธิ</h4>
            <div class="permissions">
                <?php 
                $permissions = getRolePermissions();
                foreach($permissions as $permission): ?>
                    <button class="permission-badge" data-permission="<?= $permission ?>">
                        <?= $permission ?>
                    </button>
                <?php endforeach; ?>
            </div>
            <button id="clear-permissions" class="clear-button">ล้างสิทธิทั้งหมด</button>
        </div>

        <div class="container user-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all"></th>
                        <th>ชื่อผู้ใช้</th>
                        <th>สิทธิการเข้าถึง</th>
                    </tr>
                </thead>
                <tbody id="userTableBody">
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
async function getUserPermissions(roleId) {
    try {
        const response = await fetch('manage.php', {
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
        const response = await fetch('manage.php', {
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

        const text = await response.text(); // ดึงข้อมูลเป็น text ก่อน
        console.log('Response text:', text); // debug

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
    }
}

async function updatePermission(roleId, permission, value) {
    try {
        const response = await fetch('manage.php', {
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

async function updateUserTable(roleId = null) {
    try {
        const tbody = document.getElementById('userTableBody');
        const users = await getUsers(roleId);
        console.log('Users data:', users); // debug

        tbody.innerHTML = '';
        
        if (!Array.isArray(users)) {
            console.error('Users is not an array:', users);
            return;
        }
        
        for (const user of users) {
            const permissions = await getUserPermissions(user.user_type);
            const permissionsHtml = permissions.map(p => 
                `<span class="user-permission-item" data-permission="${p}">
                    ${p}
                    <button class="remove-permission">x</button>
                 </span>`
            ).join('');

            tbody.innerHTML += `
                <tr data-user-id="${user.user_id}" data-user-type="${user.user_type}">
                    <td><input type="checkbox" class="user-checkbox"></td>
                    <td>${user.user_firstname} ${user.user_lastname}</td>
                    <td><div class="user-permissions">${permissionsHtml}</div></td>
                </tr>
            `;
        }
    } catch (error) {
        console.error('Update table error:', error);
    }
}

document.getElementById('select-all').addEventListener('change', function() {
    const isChecked = this.checked;
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = isChecked;
    });
});

document.getElementById('roleFilter').addEventListener('change', function() {
    updateUserTable(this.value);
});

document.querySelectorAll('.permission-badge').forEach(button => {
    button.addEventListener('click', async () => {
        const selectedPermission = button.getAttribute('data-permission');
        const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
        const roleId = document.getElementById('roleFilter').value;

        if (!roleId) {
            alert('กรุณาเลือกประเภทผู้ใช้ก่อน');
            return;
        }

        const result = await updatePermission(roleId, selectedPermission, 1);
        if (result.success) {
            await updateUserTable(roleId);
        }
    });
});

document.getElementById('clear-permissions').addEventListener('click', async () => {
    const roleId = document.getElementById('roleFilter').value;
    if (!roleId) {
        alert('กรุณาเลือกประเภทผู้ใช้ก่อน');
        return;
    }

    const permissions = <?php echo json_encode(getRolePermissions()); ?>;
    for (const permission of permissions) {
        await updatePermission(roleId, permission, 0);
    }
    await updateUserTable(roleId);
});

// โหลดข้อมูลครั้งแรก
updateUserTable();
</script>

</body>
</html>