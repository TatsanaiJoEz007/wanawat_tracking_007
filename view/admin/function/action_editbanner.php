<?php
require_once('../config/connect.php');

header('Content-Type: application/json');

// ตรวจสอบว่าเป็น POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$bannerId = $_POST['banner_id'] ?? null;
$bannerName = $_POST['user_firstname'] ?? null;
$bannerImage = $_FILES['user_img']['name'] ?? null;
$uploadOk = 1;

// ที่อยู่ของไฟล์ที่จะเก็บรูปภาพ
$targetDir = "../uploads/";
$targetFile = $targetDir . basename($_FILES['user_img']['name']);
$imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

// ตรวจสอบว่าไฟล์เป็นรูปภาพจริงหรือไม่
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES['user_img']['tmp_name']);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo json_encode(['success' => false, 'message' => 'File is not an image.']);
        $uploadOk = 0;
    }
}

// ตรวจสอบขนาดไฟล์
if ($_FILES['user_img']['size'] > 500000) {
    echo json_encode(['success' => false, 'message' => 'Sorry, your file is too large.']);
    $uploadOk = 0;
}

// อนุญาตเฉพาะบางประเภทของไฟล์
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
    echo json_encode(['success' => false, 'message' => 'Sorry, only JPG, JPEG, PNG & GIF files are allowed.']);
    $uploadOk = 0;
}

// ตรวจสอบ $uploadOk ถูกตั้งค่าเป็น 0 หรือไม่
if ($uploadOk == 0) {
    echo json_encode(['success' => false, 'message' => 'Sorry, your file was not uploaded.']);
// ถ้าทุกอย่างเรียบร้อย, ลองอัปโหลดไฟล์
} else {
    if (move_uploaded_file($_FILES['user_img']['tmp_name'], $targetFile)) {
        $stmt = $conn->prepare("UPDATE tb_banner SET banner_name=?, banner_img=? WHERE banner_id=?");
        $stmt->bind_param("ssi", $bannerName, $targetFile, $bannerId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Banner updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes were made.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file.']);
    }
}

$conn->close();
?>