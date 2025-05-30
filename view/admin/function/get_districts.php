<?php
// เริ่ม session ก่อนมี output ใดๆ
if (!isset($_SESSION)) {
    session_start();
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    echo '<option value="">กรุณาเข้าสู่ระบบ</option>';
    exit;
}

require_once('../../config/connect.php');

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];

// ตรวจสอบสิทธิ์ในการเข้าถึง
if (!isset($permissions['manage_permission']) || $permissions['manage_permission'] != 1) {
    echo '<option value="">ไม่มีสิทธิ์เข้าถึง</option>';
    exit;
}

try {
    $amphure_id = intval($_GET['amphure_id'] ?? 0);
    
    if (empty($amphure_id)) {
        echo '<option value="">เลือกอำเภอก่อน</option>';
        exit;
    }
    
    // ดึงข้อมูลตำบล
    $sql = "SELECT id, name_th, zip_code FROM districts WHERE amphure_id = ? ORDER BY name_th ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $amphure_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo '<option value="">เลือกตำบล</option>';
    
    while ($row = $result->fetch_assoc()) {
        $zip_display = $row['zip_code'] ? ' (' . $row['zip_code'] . ')' : '';
        echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name_th']) . $zip_display . '</option>';
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Error in get_districts.php: " . $e->getMessage());
    echo '<option value="">เกิดข้อผิดพลาด</option>';
}
?>