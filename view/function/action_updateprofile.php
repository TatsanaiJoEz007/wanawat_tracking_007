<?php
session_start();

require_once('config/connect.php');

if (!isset($_SESSION['login'])) {
    // หากไม่มีการเข้าสู่ระบบ ให้ redirect ไปยังหน้า login
    header("Location: login.php");
    exit; // ออกจากการทำงานของสคริปต์
}

// ตรวจสอบว่ามีการส่งข้อมูลผ่าน POST หรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $user_firstname = $_POST['user_firstname'];
    $user_lastname = $_POST['user_lastname'];
    $user_email = $_POST['user_email'];
    $user_tel = $_POST['user_tel'];

    // ตรวจสอบว่ามีการอัปโหลดรูปภาพหรือไม่
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
        // กำหนดตำแหน่งและชื่อไฟล์เพื่อบันทึกรูปภาพ
        $target_dir = "uploads/avatars/";
        $user_img = $target_dir . basename($_FILES['avatar']['name']);

        // เช็คประเภทของไฟล์
        $imageFileType = strtolower(pathinfo($$user_img, PATHINFO_EXTENSION));

        // ตรวจสอบว่าไฟล์เป็นรูปภาพหรือไม่
        $valid_extensions = array('jpg', 'jpeg', 'png', 'gif');
        if (!in_array($imageFileType, $valid_extensions)) {
            echo "เฉพาะไฟล์รูปภาพเท่านั้นที่อนุญาต";
            exit; // ออกจากการทำงานของสคริปต์
        }

        // ย้ายไฟล์ที่อัปโหลดเข้าสู่ตำแหน่งเก็บไฟล์ที่ตั้งไว้
        if (move_uploaded_file($_FILES['avatar']['tmp_name'], $user_img)) {
            // ทำต่อไปหากไฟล์ถูกอัปโหลดเรียบร้อย
            // อัปเดตข้อมูลโปรไฟล์ในฐานข้อมูล
            $sql = "UPDATE tb_user SET user_firstname = '$user_firstname', user_lastname = '$user_lastname', user_email = '$user_email', user_tel = '$user_tel', user_img = '$user_img' WHERE user_id = '$_SESSION[user_id]'";
            if ($conn->query($sql) === TRUE) {
                echo "บันทึกการเปลี่ยนแปลงโปรไฟล์สำเร็จ";
            } else {
                echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error;
            }
        } else {
            echo "เกิดข้อผิดพลาดในการอัปโหลดรูปภาพ";
        }
    } else {
        // หากไม่มีการอัปโหลดรูปภาพ
        // อัปเดตข้อมูลโปรไฟล์ในฐานข้อมูล
        $sql = "UPDATE tb_user SET user_firstname = '$user_firstname', user_lastname = '$user_lastname', user_email = '$user_email', user_tel = '$user_tel' WHERE user_id = '$_SESSION[user_id]'";
        if ($conn->query($sql) === TRUE) {
            echo "บันทึกการเปลี่ยนแปลงโปรไฟล์สำเร็จ";
        } else {
            echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล: " . $conn->error;
        }
    }

}
// ปิดการเชื่อมต่อกับฐานข้อมูล
$conn->close();

?>
