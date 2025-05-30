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
    $province_id = intval($_GET['province_id'] ?? 0);
    
    if (empty($province_id)) {
        echo '<option value="">เลือกจังหวัดก่อน</option>';
        exit;
    }
    
    // ดึงข้อมูลอำเภอ
    $sql = "SELECT id, name_th FROM amphures WHERE province_id = ? ORDER BY name_th ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $province_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    echo '<option value="">เลือกอำเภอ</option>';
    
    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name_th']) . '</option>';
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    error_log("Error in get_amphures.php: " . $e->getMessage());
    echo '<option value="">เกิดข้อผิดพลาด</option>';
}
?>