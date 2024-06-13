<?php
// เชื่อมต่อกับฐานข้อมูล
require_once('../config/connect.php');

// ตรวจสอบว่ามีการส่งค่า trackingId หรือไม่
if(isset($_POST['trackingId'])) {
    $trackingId = $_POST['trackingId'];

    // เขียน SQL query สำหรับค้นหา delivery_number ในตาราง tb_delivery
    $sql = "SELECT * FROM tb_delivery WHERE delivery_number = ?";
    
    // ใช้ prepared statement เพื่อป้องกันการ SQL injection
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $trackingId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // ตรวจสอบว่ามีข้อมูลที่ค้นพบหรือไม่
    if(mysqli_num_rows($result) > 0) {
        // ค้นพบ delivery_number ในฐานข้อมูล
        $response = array(
            'status' => 'match'
        );
    } else {
        // ไม่พบ delivery_number ในฐานข้อมูล
        $response = array(
            'status' => 'not_found'
        );
    }

    // แปลงข้อมูลเป็น JSON format และส่งคืนไปยัง function/tracking.php
    echo json_encode($response);
}


?>
