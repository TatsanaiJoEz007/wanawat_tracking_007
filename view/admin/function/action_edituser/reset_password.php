<?php
require_once('../../../config/connect.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบว่ามีการรับค่า user_id และ new_password หรือไม่
    if (isset($_POST['user_id']) && isset($_POST['new_password'])) {
        $userId = $_POST['user_id'];
        $newPassword = $_POST['new_password'];
        $hashedPassword = md5($newPassword); // ใช้ md5 ในการเข้ารหัสรหัสผ่าน

        // ตรวจสอบการเชื่อมต่อฐานข้อมูล
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "UPDATE tb_user SET user_pass = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $hashedPassword, $userId);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating password.']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    }
}
?>
