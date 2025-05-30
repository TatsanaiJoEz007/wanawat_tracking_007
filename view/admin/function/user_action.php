<?php
// เริ่ม session ก่อนมี output ใดๆ
if (!isset($_SESSION)) {
    session_start();
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once('../../config/connect.php');

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];

// ตรวจสอบสิทธิ์ในการเข้าถึง
if (!isset($permissions['manage_permission']) || $permissions['manage_permission'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit;
}

// ตั้งค่า header สำหรับ JSON response
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $user_type = intval($_POST['user_type'] ?? 0);
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $status = intval($_POST['status'] ?? 1);
    
    // ข้อมูลเพิ่มเติมสำหรับลูกค้า
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $province_id = $_POST['province_id'] ?? null;
    $amphure_id = $_POST['amphure_id'] ?? null;
    $district_id = $_POST['district_id'] ?? null;
    
    // จัดการไฟล์รูปภาพ
    $user_img = null;
    if (isset($_FILES['user_img']) && $_FILES['user_img']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['user_img']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $user_img = file_get_contents($_FILES['user_img']['tmp_name']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ประเภทไฟล์ไม่ถูกต้อง กรุณาเลือกไฟล์รูปภาพ']);
            exit;
        }
    }
    
    // Validation
    if (empty($firstname) || empty($lastname) || empty($email)) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน']);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'รูปแบบอีเมลไม่ถูกต้อง']);
        exit;
    }
    
    if ($action === 'add') {
        // เพิ่มผู้ใช้ใหม่
        if (empty($password)) {
            echo json_encode(['success' => false, 'message' => 'กรุณากรอกรหัสผ่าน']);
            exit;
        }
        
        // ตรวจสอบอีเมลซ้ำ
        $check_email = $conn->prepare("SELECT user_id FROM tb_user WHERE user_email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        if ($check_email->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'อีเมลนี้มีอยู่ในระบบแล้ว']);
            exit;
        }
        $check_email->close();
        
        // เข้ารหัสรหัสผ่าน
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // สร้าง customer_id สำหรับลูกค้า
        $customer_id = null;
        if ($user_type == 0) {
            // สร้าง customer_id แบบ auto
            $customer_result = $conn->query("SELECT MAX(CAST(SUBSTRING(customer_id, 2) AS UNSIGNED)) as max_id FROM tb_user WHERE customer_id LIKE 'C%'");
            $max_id = $customer_result->fetch_assoc()['max_id'] ?? 0;
            $customer_id = 'C' . str_pad($max_id + 1, 4, '0', STR_PAD_LEFT);
        }
        
        // เตรียม SQL สำหรับเพิ่มข้อมูล
        if ($user_img) {
            $sql = "INSERT INTO tb_user (user_firstname, user_lastname, user_email, user_password, user_tel, user_address, user_img, user_status, user_type, customer_id, province_id, amphure_id, district_id, create_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssssiisiii", $firstname, $lastname, $email, $hashed_password, $phone, $address, $user_img, $status, $user_type, $customer_id, $province_id, $amphure_id, $district_id);
        } else {
            $sql = "INSERT INTO tb_user (user_firstname, user_lastname, user_email, user_password, user_tel, user_address, user_status, user_type, customer_id, province_id, amphure_id, district_id, create_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssisisii", $firstname, $lastname, $email, $hashed_password, $phone, $address, $status, $user_type, $customer_id, $province_id, $amphure_id, $district_id);
        }
        
        if ($stmt->execute()) {
            $new_user_id = $conn->insert_id;
            
            // Log activity
            $activity_sql = "INSERT INTO admin_activity_log (userId, action, entity, entity_id, additional_info, create_at) VALUES (?, 'CREATE_USER', 'tb_user', ?, ?, NOW())";
            $activity_stmt = $conn->prepare($activity_sql);
            $additional_info = "เพิ่มผู้ใช้: {$firstname} {$lastname} ({$email})";
            $activity_stmt->bind_param("iis", $_SESSION['user_id'], $new_user_id, $additional_info);
            $activity_stmt->execute();
            $activity_stmt->close();
            
            echo json_encode(['success' => true, 'message' => 'เพิ่มผู้ใช้เรียบร้อยแล้ว']);
        } else {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเพิ่มผู้ใช้']);
        }
        $stmt->close();
        
    } elseif ($action === 'edit') {
        // แก้ไขข้อมูลผู้ใช้
        if (empty($user_id)) {
            echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสผู้ใช้']);
            exit;
        }
        
        // ตรวจสอบอีเมลซ้ำ (ยกเว้นตัวเอง)
        $check_email = $conn->prepare("SELECT user_id FROM tb_user WHERE user_email = ? AND user_id != ?");
        $check_email->bind_param("si", $email, $user_id);
        $check_email->execute();
        if ($check_email->get_result()->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'อีเมลนี้มีอยู่ในระบบแล้ว']);
            exit;
        }
        $check_email->close();
        
        // อัปเดตข้อมูล
        if ($user_img) {
            if (!empty($password)) {
                // มีรูปภาพและรหัสผ่านใหม่
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE tb_user SET user_firstname=?, user_lastname=?, user_email=?, user_password=?, user_tel=?, user_address=?, user_img=?, user_status=?, province_id=?, amphure_id=?, district_id=? WHERE user_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssssssiiii", $firstname, $lastname, $email, $hashed_password, $phone, $address, $user_img, $status, $province_id, $amphure_id, $district_id, $user_id);
            } else {
                // มีรูปภาพแต่ไม่เปลี่ยนรหัสผ่าน
                $sql = "UPDATE tb_user SET user_firstname=?, user_lastname=?, user_email=?, user_tel=?, user_address=?, user_img=?, user_status=?, province_id=?, amphure_id=?, district_id=? WHERE user_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssiiii", $firstname, $lastname, $email, $phone, $address, $user_img, $status, $province_id, $amphure_id, $district_id, $user_id);
            }
        } else {
            if (!empty($password)) {
                // ไม่มีรูปภาพแต่มีรหัสผ่านใหม่
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE tb_user SET user_firstname=?, user_lastname=?, user_email=?, user_password=?, user_tel=?, user_address=?, user_status=?, province_id=?, amphure_id=?, district_id=? WHERE user_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssiiiii", $firstname, $lastname, $email, $hashed_password, $phone, $address, $status, $province_id, $amphure_id, $district_id, $user_id);
            } else {
                // ไม่มีรูปภาพและไม่เปลี่ยนรหัสผ่าน
                $sql = "UPDATE tb_user SET user_firstname=?, user_lastname=?, user_email=?, user_tel=?, user_address=?, user_status=?, province_id=?, amphure_id=?, district_id=? WHERE user_id=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssssssiiiii", $firstname, $lastname, $email, $phone, $address, $status, $province_id, $amphure_id, $district_id, $user_id);
            }
        }
        
        if ($stmt->execute()) {
            // Log activity
            $activity_sql = "INSERT INTO admin_activity_log (userId, action, entity, entity_id, additional_info, create_at) VALUES (?, 'UPDATE_USER', 'tb_user', ?, ?, NOW())";
            $activity_stmt = $conn->prepare($activity_sql);
            $additional_info = "แก้ไขผู้ใช้: {$firstname} {$lastname} ({$email})";
            $activity_stmt->bind_param("iis", $_SESSION['user_id'], $user_id, $additional_info);
            $activity_stmt->execute();
            $activity_stmt->close();
            
            echo json_encode(['success' => true, 'message' => 'แก้ไขข้อมูลผู้ใช้เรียบร้อยแล้ว']);
        } else {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล']);
        }
        $stmt->close();
        
    } else {
        echo json_encode(['success' => false, 'message' => 'Action ไม่ถูกต้อง']);
    }
    
} catch (Exception $e) {
    error_log("Error in user_actions.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดระบบ: ' . $e->getMessage()]);
}
?>