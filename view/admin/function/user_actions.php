<?php
// Start session
if (!isset($_SESSION)) {
    session_start();
}

// Security headers
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// Check if user is logged in
if (!isset($_SESSION['login'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'error' => 'Unauthorized access',
        'message' => 'กรุณาเข้าสู่ระบบ'
    ]);
    exit;
}

// Check if user has permission to manage users
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
if (!isset($permissions['manage_permission']) || $permissions['manage_permission'] != 1) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Access denied',
        'message' => 'คุณไม่มีสิทธิ์จัดการผู้ใช้งาน'
    ]);
    exit;
}

// Include database connection
require_once('../../config/connect.php');

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed',
        'message' => 'เฉพาะ POST method เท่านั้น'
    ]);
    exit;
}

try {
    // Debug: Log received data
    error_log("User Actions Debug - POST data: " . print_r($_POST, true));
    error_log("User Actions Debug - FILES data: " . print_r($_FILES, true));
    
    // Get form data
    $action = $_POST['action'] ?? '';
    $user_id = !empty($_POST['user_id']) ? (int)$_POST['user_id'] : null;
    $user_type = isset($_POST['user_type']) ? (int)$_POST['user_type'] : null;
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $province_id = !empty($_POST['province_id']) ? (int)$_POST['province_id'] : null;
    $amphure_id = !empty($_POST['amphure_id']) ? (int)$_POST['amphure_id'] : null;
    $district_id = !empty($_POST['district_id']) ? $_POST['district_id'] : null;
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 9;
    $remove_image = isset($_POST['remove_image']) ? (int)$_POST['remove_image'] : 0;

    // Validate required fields
    if (empty($action) || is_null($user_type) || empty($firstname) || empty($lastname) || empty($email)) {
        throw new Exception('กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน');
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('รูปแบบอีเมลไม่ถูกต้อง');
    }

    // Validate user type
    $valid_user_types = [0, 1, 999]; // 0=user, 1=employee, 999=admin
    if (!in_array($user_type, $valid_user_types)) {
        throw new Exception('ประเภทผู้ใช้งานไม่ถูกต้อง');
    }

    // Handle image upload
    $image_data = null;
    $update_image = false;
    
    if (isset($_FILES['user_img']) && $_FILES['user_img']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['user_img']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            throw new Exception('รูปแบบไฟล์ไม่ถูกต้อง กรุณาใช้ไฟล์ JPG, PNG หรือ GIF');
        }
        
        $max_size = 5 * 1024 * 1024; // 5MB
        if ($_FILES['user_img']['size'] > $max_size) {
            throw new Exception('ขนาดไฟล์ใหญ่เกินไป (สูงสุด 5MB)');
        }
        
        $image_data = file_get_contents($_FILES['user_img']['tmp_name']);
        $update_image = true;
    } elseif ($remove_image == 1) {
        // Remove image
        $image_data = null;
        $update_image = true;
    }

    if ($action === 'add') {
        // Check if email already exists
        $check_email = $conn->prepare("SELECT user_id FROM tb_user WHERE user_email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $existing_user = $check_email->get_result()->fetch_assoc();
        $check_email->close();
        
        if ($existing_user) {
            throw new Exception('อีเมลนี้มีอยู่ในระบบแล้ว');
        }

        // Generate customer ID for regular users (user_type = 0)
        $customer_id = null;
        if ($user_type == 0) {
            $customer_id = generateCustomerId($conn);
        }

        // Default password for new users
        $default_password = password_hash('ilovewehome', PASSWORD_DEFAULT);

        // Handle NULL values for optional fields
        if ($province_id === null) $province_id = 1; // Default province
        if ($amphure_id === null) $amphure_id = 1; // Default amphure
        if ($district_id === null) $district_id = '100101'; // Default district
        if (empty($phone)) $phone = '00000000'; // Default phone
        if (empty($address)) $address = 'Default'; // Default address

        // Force new users to have status = 9 (pending approval)
        $new_user_status = 9;

        // Insert new user
        $sql = "INSERT INTO tb_user (
                    user_firstname, user_lastname, user_email, user_pass, user_type, 
                    user_status, user_tel, user_address, province_id, amphure_id, district_id,
                    customer_id, user_img, user_create_at, user_last_activity
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = $conn->prepare($sql);
        
        // Bind all parameters - use $new_user_status instead of $status
        $stmt->bind_param("ssssiississss", 
            $firstname, $lastname, $email, $default_password, $user_type,
            $new_user_status, $phone, $address, $province_id, $amphure_id, $district_id,
            $customer_id, $image_data
        );
        
        if ($stmt->execute()) {
            $new_user_id = $conn->insert_id;
            $stmt->close();
            
            // Log activity (if available)
            logActivity($conn, $_SESSION['user_id'], 'CREATE_USER', 'tb_user', $new_user_id, "สร้างผู้ใช้ใหม่: {$firstname} {$lastname} (รอการยืนยัน)");
            
            echo json_encode([
                'success' => true,
                'message' => 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว (สถานะ: รอการยืนยัน)',
                'user_id' => $new_user_id,
                'customer_id' => $customer_id,
                'status' => $new_user_status
            ]);
        } else {
            throw new Exception('ไม่สามารถเพิ่มผู้ใช้งานได้: ' . $stmt->error);
        }
        
    } elseif ($action === 'edit') {
        // Validate user_id for edit
        if (!$user_id) {
            throw new Exception('ไม่พบรหัสผู้ใช้งาน');
        }
        
        // Check if user exists
        $check_user = $conn->prepare("SELECT user_email FROM tb_user WHERE user_id = ?");
        $check_user->bind_param("i", $user_id);
        $check_user->execute();
        $existing_user = $check_user->get_result()->fetch_assoc();
        $check_user->close();
        
        if (!$existing_user) {
            throw new Exception('ไม่พบผู้ใช้งานที่ต้องการแก้ไข');
        }
        
        // Check if email already exists (except current user)
        $check_email = $conn->prepare("SELECT user_id FROM tb_user WHERE user_email = ? AND user_id != ?");
        $check_email->bind_param("si", $email, $user_id);
        $check_email->execute();
        $email_conflict = $check_email->get_result()->fetch_assoc();
        $check_email->close();
        
        if ($email_conflict) {
            throw new Exception('อีเมลนี้มีอยู่ในระบบแล้ว');
        }
        
        // Handle NULL values for optional fields in edit
        if ($province_id === null) $province_id = 1;
        if ($amphure_id === null) $amphure_id = 1;
        if ($district_id === null) $district_id = '100101';
        if (empty($phone)) $phone = '00000000';
        if (empty($address)) $address = 'Default';

        // Build update query
        if ($update_image) {
            // Update with image
            $sql = "UPDATE tb_user SET 
                        user_firstname = ?, 
                        user_lastname = ?, 
                        user_email = ?,
                        user_type = ?,
                        user_status = ?,
                        user_tel = ?,
                        user_address = ?,
                        province_id = ?,
                        amphure_id = ?,
                        district_id = ?,
                        user_img = ?,
                        user_last_activity = NOW()
                    WHERE user_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiiississsi", 
                $firstname, $lastname, $email, $user_type, $status, 
                $phone, $address, $province_id, $amphure_id, $district_id,
                $image_data, $user_id
            );
        } else {
            // Update without image
            $sql = "UPDATE tb_user SET 
                        user_firstname = ?, 
                        user_lastname = ?, 
                        user_email = ?,
                        user_type = ?,
                        user_status = ?,
                        user_tel = ?,
                        user_address = ?,
                        province_id = ?,
                        amphure_id = ?,
                        district_id = ?,
                        user_last_activity = NOW()
                    WHERE user_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiiissssi", 
                $firstname, $lastname, $email, $user_type, $status, 
                $phone, $address, $province_id, $amphure_id, $district_id, $user_id
            );
        }
        
        if ($stmt->execute()) {
            $stmt->close();
            
            // Log activity
            logActivity($conn, $_SESSION['user_id'], 'UPDATE_USER', 'tb_user', $user_id, "อัปเดตผู้ใช้: {$firstname} {$lastname}");
            
            echo json_encode([
                'success' => true,
                'message' => 'อัปเดตข้อมูลผู้ใช้งานเรียบร้อยแล้ว',
                'user_id' => $user_id,
                'image_updated' => $update_image
            ]);
        } else {
            error_log("UPDATE failed: " . $stmt->error);
            throw new Exception('ไม่สามารถอัปเดตข้อมูลผู้ใช้งานได้: ' . $stmt->error);
        }
        
    } else {
        throw new Exception('การดำเนินการไม่ถูกต้อง');
    }

} catch (Exception $e) {
    error_log("User Actions Error: " . $e->getMessage());
    
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'User action failed',
        'message' => $e->getMessage()
    ]);
    
} finally {
    // Close database connection
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}

// Helper function to generate customer ID
function generateCustomerId($conn) {
    $prefix = 'WH';
    $year = date('y'); // 2-digit year
    
    // Get last customer ID for current year
    $sql = "SELECT customer_id FROM tb_user 
            WHERE customer_id LIKE ? 
            ORDER BY customer_id DESC 
            LIMIT 1";
    
    $like_pattern = $prefix . $year . '%';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $like_pattern);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    
    if ($result) {
        // Extract number from existing customer ID
        $last_id = $result['customer_id'];
        $number = (int)substr($last_id, 4); // Remove "WH24" prefix
        $new_number = $number + 1;
    } else {
        // First customer of the year
        $new_number = 1;
    }
    
    // Format: WH25001, WH25002, etc.
    return $prefix . $year . str_pad($new_number, 3, '0', STR_PAD_LEFT);
}

// Helper function to log activity
function logActivity($conn, $user_id, $action, $entity, $entity_id, $details) {
    try {
        // Check if log table exists
        $check_table = $conn->query("SHOW TABLES LIKE 'admin_activity_log'");
        if ($check_table->num_rows > 0) {
            $log_sql = "INSERT INTO admin_activity_log (userId, action, entity, entity_id, additional_info, create_at) VALUES (?, ?, ?, ?, ?, NOW())";
            $log_stmt = $conn->prepare($log_sql);
            $log_stmt->bind_param("issis", $user_id, $action, $entity, $entity_id, $details);
            $log_stmt->execute();
            $log_stmt->close();
        }
    } catch (Exception $e) {
        // Log error but don't stop execution
        error_log("Activity log error: " . $e->getMessage());
    }
}
?>