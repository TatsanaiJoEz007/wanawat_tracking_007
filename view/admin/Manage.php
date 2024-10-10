<!DOCTYPE html>
<html lang="th">


<head>
    <title> Manage - Manage</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    
    <!-- เรียกใช้ Bootstrap CSS จาก CDN -->
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
<?php require_once('function/sidebar.php');  ?>
<div class="access-control">
  <h2>ควบคุมสิทธิการเข้าถึง</h2>
  <h3>เลือกสิทธิการเข้าถึงสำหรับ แต่ละ User ว่าเข้าถึงสิทธิไหนได้บ้าง</h3>
  
  <div class="content">
    <!-- Container 2: Permission Selection -->
    <div class="container permissions-container">
      <h4>เลือกสิทธิ</h4>
      <div class="permissions">
        <button class="permission-badge" data-permission="manage_permission">manage_permission</button>
        <button class="permission-badge" data-permission="manage_website">manage_website</button>
        <button class="permission-badge" data-permission="manage_logs">manage_logs</button>
        <button class="permission-badge" data-permission="manage_users">manage_users</button>
        <button class="permission-badge" data-permission="view_reports">view_reports</button>
      </div>
      <button id="clear-permissions" class="clear-button">ล้างสิทธิทั้งหมด</button>
    </div>

    <!-- Container 1: User Table -->
    <div class="container user-container">
      <table class="user-table">
        <thead>
          <tr>
            <th><input type="checkbox" id="select-all"></th>
            <th>ชื่อผู้ใช้</th>
            <th>สิทธิการเข้าถึง</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><input type="checkbox" class="user-checkbox"></td>
            <td>A</td>
            <td><div class="user-permissions"></div></td>
          </tr>
          <tr>
            <td><input type="checkbox" class="user-checkbox"></td>
            <td>B</td>
            <td><div class="user-permissions"></div></td>
          </tr>
          <tr>
            <td><input type="checkbox" class="user-checkbox"></td>
            <td>C</td>
            <td><div class="user-permissions"></div></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
  // ฟังก์ชันสำหรับการเลือกหรือยกเลิกการเลือกทั้งหมด
  document.getElementById('select-all').addEventListener('change', function() {
    const isChecked = this.checked;
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
      checkbox.checked = isChecked;
    });
  });

  // ฟังก์ชันสำหรับการจัดการการคลิกปุ่มสิทธิ
  document.querySelectorAll('.permission-badge').forEach(button => {
    button.addEventListener('click', () => {
      const selectedPermission = button.getAttribute('data-permission');
      const selectedUsers = document.querySelectorAll('.user-checkbox:checked');

      selectedUsers.forEach(checkbox => {
        const permissionContainer = checkbox.closest('tr').querySelector('.user-permissions');
        
        // ตรวจสอบว่าสิทธิถูกเพิ่มอยู่แล้วหรือไม่
        if (!permissionContainer.textContent.includes(selectedPermission)) {
          const permissionSpan = document.createElement('span');
          permissionSpan.className = 'user-permission-item';
          permissionSpan.textContent = selectedPermission;

          // ปุ่มสำหรับลบสิทธิ
          const removeBtn = document.createElement('button');
          removeBtn.textContent = 'x';
          removeBtn.className = 'remove-permission';
          removeBtn.addEventListener('click', () => {
            permissionSpan.remove();
          });

          permissionSpan.appendChild(removeBtn);
          permissionContainer.appendChild(permissionSpan);
        }
      });
    });
  });

  // ฟังก์ชันสำหรับการล้างสิทธิทั้งหมด
  document.getElementById('clear-permissions').addEventListener('click', () => {
    const selectedUsers = document.querySelectorAll('.user-checkbox:checked');

    selectedUsers.forEach(checkbox => {
      const permissionContainer = checkbox.closest('tr').querySelector('.user-permissions');
      permissionContainer.innerHTML = ''; // ลบสิทธิทั้งหมดที่อยู่ใน container
    });
  });
</script>





</body>
</html>