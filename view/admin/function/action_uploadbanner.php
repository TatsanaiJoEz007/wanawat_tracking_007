<?php
require_once('../../config/connect.php');

header('Content-Type: application/json'); // สำคัญ: ตั้งค่า header ให้รองรับ JSON

$target_dir = "../uploads/";  // ตรวจสอบให้แน่ใจว่าโฟลเดอร์นี้มีอยู่จริง
$bannerName = $_POST['user_firstname'];
$bannerImg = $_FILES['user_img']['name'];
$target_file = $target_dir . basename($bannerImg);
$uploadOk = 1;

// ตรวจสอบและย้ายไฟล์ไปยังโฟลเดอร์ uploads
if (move_uploaded_file($_FILES['user_img']['tmp_name'], $target_file)) {
    $stmt = $conn->prepare("INSERT INTO tb_banner (banner_name, banner_img) VALUES (?, ?)");
    $stmt->bind_param("ss", $bannerName, $target_file);
    $stmt->execute();
    if ($stmt->affected_rows > 0) 
    {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลลงฐานข้อมูลได้']);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปโหลดไฟล์ได้']);
}
$conn->close();
?>
