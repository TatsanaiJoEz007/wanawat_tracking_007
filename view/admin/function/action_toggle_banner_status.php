<?php
session_start();
require_once('../../config/connect.php');

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ไม่ได้เข้าสู่ระบบ']);
    exit;
}

// ตรวจสอบสิทธิ์
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
if (!isset($permissions['manage_website']) || $permissions['manage_website'] != 1) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bannerId = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;
    
    if ($bannerId <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ID แบนเนอร์ไม่ถูกต้อง']);
        exit;
    }
    
    // ตรวจสอบว่า banner_status column มีอยู่หรือไม่
    $checkColumn = $conn->query("SHOW COLUMNS FROM tb_banner LIKE 'banner_status'");
    if (!$checkColumn || $checkColumn->num_rows == 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ตาราง tb_banner ยังไม่มีคอลัมน์ banner_status']);
        exit;
    }
    
    try {
        // ตรวจสอบว่าแบนเนอร์มีอยู่จริงหรือไม่
        $checkSql = "SELECT banner_id FROM tb_banner WHERE banner_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("i", $bannerId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if ($result->num_rows == 0) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'ไม่พบแบนเนอร์ที่ต้องการแก้ไข']);
            exit;
        }
        
        // อัพเดทสถานะ
        $updateSql = "UPDATE tb_banner SET banner_status = ? WHERE banner_id = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("ii", $status, $bannerId);
        
        if ($updateStmt->execute()) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'message' => 'อัพเดทสถานะแบนเนอร์เรียบร้อยแล้ว',
                'data' => [
                    'banner_id' => $bannerId,
                    'new_status' => $status
                ]
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล']);
        }
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดของระบบ: ' . $e->getMessage()]);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Method ไม่ถูกต้อง']);
}
?>