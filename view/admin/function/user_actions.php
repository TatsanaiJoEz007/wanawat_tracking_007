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
    
    // Get customer_id from form
    $customer_id = trim($_POST['customer_id'] ?? '');

    // Validate required fields
    if (empty($action) || is_null($user_type) || empty($firstname) || empty($lastname) || empty($email)) {
        throw new Exception('กรุณากรอกข้อมูลที่จำเป็นให้ครบถ้วน');
    }

    // Validate customer_id for users (user_type = 0)
    if ($user_type == 0) {
        if (empty($customer_id)) {
            throw new Exception('กรุณากรอกรหัสลูกค้า');
        }
        
        // Validate customer_id format
        if (!preg_match('/^[A-Za-z0-9]{1,20}$/', $customer_id)) {
            throw new Exception('รหัสลูกค้าต้องเป็นตัวอักษรและตัวเลข ไม่เกิน 20 ตัวอักษร');
        }
    } else {
        // For admin and employee, customer_id should be null
        $customer_id = null;
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

    // Function to get default profile image
    function getDefaultProfileImage() {
        $default_image_path = '../../assets/img/logo/mascot.png';
        
        // Check if default image file exists
        if (file_exists($default_image_path)) {
            return file_get_contents($default_image_path);
        } else {
            // If mascot.png doesn't exist, create a simple placeholder
            error_log("Default profile image not found at: " . $default_image_path);
            return null;
        }
    }

    // Handle image upload
    $image_data = null;
    $update_image = false;
    
    if (isset($_FILES['user_img']) && $_FILES['user_img']['error'] === UPLOAD_ERR_OK) {
        // User uploaded an image
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
        
        error_log("User uploaded image - Size: " . strlen($image_data) . " bytes");
        
    } elseif ($remove_image == 1) {
        // User wants to remove image - set to default
        $image_data = getDefaultProfileImage();
        $update_image = true;
        
        error_log("Image removed - Using default image");
        
    } elseif ($action === 'add') {
        // New user without uploaded image - use default image
        $image_data = getDefaultProfileImage();
        $update_image = true;
        
        error_log("New user without image - Using default image");
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

        // Check if customer_id already exists (for users only)
        if ($user_type == 0 && !empty($customer_id)) {
            $check_customer_id = $conn->prepare("SELECT user_id FROM tb_user WHERE customer_id = ?");
            $check_customer_id->bind_param("s", $customer_id);
            $check_customer_id->execute();
            $existing_customer = $check_customer_id->get_result()->fetch_assoc();
            $check_customer_id->close();
            
            if ($existing_customer) {
                throw new Exception('รหัสลูกค้านี้มีอยู่ในระบบแล้ว กรุณาใช้รหัสอื่น');
            }
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

        // For new users, always use an image (either uploaded or default)
        if (!$update_image || $image_data === null) {
            $image_data = getDefaultProfileImage();
        }

        // Insert new user
        $sql = "INSERT INTO tb_user (
                    user_firstname, user_lastname, user_email, user_pass, user_type, 
                    user_status, user_tel, user_address, province_id, amphure_id, district_id,
                    customer_id, user_img, user_create_at, user_last_activity
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        
        $stmt = $conn->prepare($sql);
        
        // Bind all parameters
        $stmt->bind_param("ssssiississss", 
            $firstname, $lastname, $email, $default_password, $user_type,
            $new_user_status, $phone, $address, $province_id, $amphure_id, $district_id,
            $customer_id, $image_data
        );
        
        if ($stmt->execute()) {
            $new_user_id = $conn->insert_id;
            $stmt->close();
            
            // Prepare success message
            $message = 'เพิ่มผู้ใช้งานเรียบร้อยแล้ว (สถานะ: รอการยืนยัน)';
            if ($user_type == 0 && !empty($customer_id)) {
                $message .= ' รหัสลูกค้า: ' . $customer_id;
            }
            
            // Log activity (if available)
            $log_details = "สร้างผู้ใช้ใหม่: {$firstname} {$lastname} (รอการยืนยัน)";
            if ($user_type == 0 && !empty($customer_id)) {
                $log_details .= " รหัสลูกค้า: {$customer_id}";
            }
            
            // Add image info to log
            if ($image_data) {
                $log_details .= " (รูปโปรไฟล์: " . (isset($_FILES['user_img']) && $_FILES['user_img']['error'] === UPLOAD_ERR_OK ? "อัพโหลด" : "ค่าเริ่มต้น") . ")";
            }
            
            logActivity($conn, $_SESSION['user_id'], 'CREATE_USER', 'tb_user', $new_user_id, $log_details);
            
            error_log("New user created with ID: $new_user_id, Image size: " . ($image_data ? strlen($image_data) : 0) . " bytes");
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'user_id' => $new_user_id,
                'customer_id' => $customer_id,
                'status' => $new_user_status,
                'has_image' => ($image_data !== null)
            ]);
        } else {
            throw new Exception('ไม่สามารถเพิ่มผู้ใช้งานได้: ' . $stmt->error);
        }
        
    } elseif ($action === 'edit') {
        // Validate user_id for edit
        if (!$user_id) {
            throw new Exception('ไม่พบรหัสผู้ใช้งาน');
        }
        
        // Check if user exists and get current data
        $check_user = $conn->prepare("SELECT user_email, customer_id, user_type FROM tb_user WHERE user_id = ?");
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
        
        // Check if customer_id already exists (for users only, except current user)
        if ($user_type == 0 && !empty($customer_id)) {
            $check_customer_id = $conn->prepare("SELECT user_id FROM tb_user WHERE customer_id = ? AND user_id != ?");
            $check_customer_id->bind_param("si", $customer_id, $user_id);
            $check_customer_id->execute();
            $customer_conflict = $check_customer_id->get_result()->fetch_assoc();
            $check_customer_id->close();
            
            if ($customer_conflict) {
                throw new Exception('รหัสลูกค้านี้มีอยู่ในระบบแล้ว กรุณาใช้รหัสอื่น');
            }
        }
        
        // Handle NULL values for optional fields in edit
        if ($province_id === null) $province_id = 1;
        if ($amphure_id === null) $amphure_id = 1;
        if ($district_id === null) $district_id = '100101';
        if (empty($phone)) $phone = '00000000';
        if (empty($address)) $address = 'Default';

        // For edit mode, if remove_image is set, use default image
        if ($remove_image == 1 && !$update_image) {
            $image_data = getDefaultProfileImage();
            $update_image = true;
        }

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
                        customer_id = ?,
                        user_img = ?,
                        user_last_activity = NOW()
                    WHERE user_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiiississsi", 
                $firstname, $lastname, $email, $user_type, $status, 
                $phone, $address, $province_id, $amphure_id, $district_id,
                $customer_id, $image_data, $user_id
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
                        customer_id = ?,
                        user_last_activity = NOW()
                    WHERE user_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssiiissssi", 
                $firstname, $lastname, $email, $user_type, $status, 
                $phone, $address, $province_id, $amphure_id, $district_id, 
                $customer_id, $user_id
            );
        }
        
        if ($stmt->execute()) {
            $stmt->close();
            
            // Prepare success message
            $message = 'อัปเดตข้อมูลผู้ใช้งานเรียบร้อยแล้ว';
            
            // Log activity
            $log_details = "อัปเดตผู้ใช้: {$firstname} {$lastname}";
            if ($user_type == 0 && !empty($customer_id)) {
                $log_details .= " รหัสลูกค้า: {$customer_id}";
                
                // Check if customer_id was changed
                if ($existing_user['customer_id'] !== $customer_id) {
                    $message .= ' (รหัสลูกค้าได้รับการอัปเดต: ' . $customer_id . ')';
                    $log_details .= " (เปลี่ยนจาก: {$existing_user['customer_id']})";
                }
            }
            
            if ($update_image) {
                $log_details .= " (อัปเดตรูปโปรไฟล์)";
            }
            
            logActivity($conn, $_SESSION['user_id'], 'UPDATE_USER', 'tb_user', $user_id, $log_details);
            
            echo json_encode([
                'success' => true,
                'message' => $message,
                'user_id' => $user_id,
                'customer_id' => $customer_id,
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

// Helper function to generate customer ID (kept for backup/reference)
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