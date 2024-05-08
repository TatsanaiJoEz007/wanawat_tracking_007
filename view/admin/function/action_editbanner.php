<?php
// config.php file should contain the database connection settings
require_once('../../config/connect.php');

// Set the content type to JSON
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all required fields are set
    if (isset($_POST['user_firstname'], $_FILES['user_img']['name'], $_POST['banner_id'])) {
        $bannerName = $_POST['user_firstname'];
        $bannerId = $_POST['banner_id'];
        $bannerImg = $_FILES['user_img']['name'];

        // Set the upload directory
        $uploadDir = '../uploads/';
        $targetFile = $uploadDir. basename($_FILES['user_img']['name']);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $uploadOk = 1;

        // Check file size
        if ($_FILES['user_img']['size'] > 5000000) {
            $uploadOk = 0;
            $errorMessage = 'ไฟล์ใหญ่เกินไป';
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg', 'gif'])) {
            $uploadOk = 0;
            $errorMessage = 'เฉพาะไฟล์ JPG, JPEG, PNG & GIF เท่านั้น';
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo json_encode(['success' => false, 'message' => $errorMessage]);
        } else {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES['user_img']['tmp_name'], $targetFile)) {
                // Prepare the SQL query
                $sql = "UPDATE tb_banner SET banner_name =?, banner_img =? WHERE banner_id =?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssi", $bannerName, $targetFile, $bannerId);

                // Execute the query
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['success' => true, 'message' => 'Banner ถูกแก้ไขเรียบร้อยแล้ว']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'ไม่มีการเปลี่ยนแปลงข้อมูล']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error executing query']);
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

// Close the database connection
$conn->close();
?>