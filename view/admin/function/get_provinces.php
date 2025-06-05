<?php
if (!isset($_SESSION)) {
    session_start();
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    echo '<option value="">กรุณาเข้าสู่ระบบ</option>';
    exit;
}

require_once('../../config/connect.php');

try {
    // ดึงข้อมูลจังหวัด
    $sql = "SELECT id, name_th FROM provinces ORDER BY name_th ASC";
    $result = $conn->query($sql);
    
    echo '<option value="">เลือกจังหวัด</option>';
    
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name_th']) . '</option>';
    }
    
} catch (Exception $e) {
    error_log("Error in get_provinces.php: " . $e->getMessage());
    echo '<option value="">เกิดข้อผิดพลาด</option>';
}
?>