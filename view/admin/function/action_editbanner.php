<?php
require_once('../config/connect.php');

header('Content-Type: application/json'); // สำคัญ: กำหนดให้เอาต์พุทเป็น JSON

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบว่าตัวแปรที่จำเป็นทั้งหมดมีอยู่หรือไม่
    if (isset($_POST['user_firstname'], $_FILES['user_img']['name'], $_POST['banner_id'])) {
        $bannerName = $_POST['user_firstname'];
        $bannerId = $_POST['banner_id'];
        $bannerImg = $_FILES['user_img']['name'];

        $uploadDir = '../uploads/';
        $targetFile = $uploadDir . basename($_FILES['user_img']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $uploadOk = 1;

        // ตรวจสอบขนาดไฟล์
        if ($_FILES['user_img']['size'] > 5000000) {
            echo json_encode(['success' => false, 'message' => 'ไฟล์ใหญ่เกินไป']);
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            echo json_encode(['success' => false, 'message' => 'ขออภัย, ไฟล์ของคุณไม่สามารถอัปโหลดได้']);
        } else {
            if (move_uploaded_file($_FILES['user_img']['tmp_name'], $targetFile)) {
                $sql = "UPDATE tb_banner SET banner_name = ?, banner_img = ? WHERE banner_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $bannerName, $targetFile, $bannerId);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Banner ถูกแก้ไขเรียบร้อยแล้ว']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'ไม่มีการเปลี่ยนแปลงข้อมูล']);
                }
                $stmt->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปโหลดไฟล์ได้']);
            }
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
$conn->close();
?>
