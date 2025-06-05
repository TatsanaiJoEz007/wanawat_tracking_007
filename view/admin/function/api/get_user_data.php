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

// Check if user is admin (user_type = 999)
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 999) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'error' => 'Access denied',
        'message' => 'คุณไม่มีสิทธิ์เข้าถึงข้อมูลนี้'
    ]);
    exit;
}

// Include database connection
require_once('../../../config/connect.php');

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

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['type']) || empty($input['type'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'Invalid input',
        'message' => 'ต้องระบุประเภทข้อมูลผู้ใช้งาน'
    ]);
    exit;
}

$type = $input['type'];

try {
    // Initialize variables
    $users = [];
    $sql = "";
    $params = [];
    $param_types = "";

    // Build SQL query based on type
    switch ($type) {
        case 'all_users':
            $sql = "SELECT user_id, user_firstname, user_lastname, user_email, user_type, 
                           user_status, user_is_online, user_create_date, user_last_login, user_last_ip
                    FROM tb_user 
                    WHERE user_status = 1 
                    ORDER BY user_create_date DESC, user_firstname ASC";
            break;

        case 'admin_users':
            $sql = "SELECT user_id, user_firstname, user_lastname, user_email, user_type, 
                           user_status, user_is_online, user_create_date, user_last_login, user_last_ip
                    FROM tb_user 
                    WHERE user_type = ? AND user_status = 1 
                    ORDER BY user_create_date DESC, user_firstname ASC";
            $params = [999];
            $param_types = "i";
            break;

        case 'employee_users':
            $sql = "SELECT user_id, user_firstname, user_lastname, user_email, user_type, 
                           user_status, user_is_online, user_create_date, user_last_login, user_last_ip
                    FROM tb_user 
                    WHERE user_type = ? AND user_status = 1 
                    ORDER BY user_create_date DESC, user_firstname ASC";
            $params = [1];
            $param_types = "i";
            break;

        case 'online_users':
            $sql = "SELECT user_id, user_firstname, user_lastname, user_email, user_type, 
                           user_status, user_is_online, user_create_date, user_last_login, user_last_ip
                    FROM tb_user 
                    WHERE user_is_online = ? AND user_status = 1 
                    ORDER BY user_last_login DESC, user_firstname ASC";
            $params = [1];
            $param_types = "i";
            break;

        default:
            throw new Exception('ประเภทข้อมูลผู้ใช้งานไม่ถูกต้อง');
    }

    // Prepare and execute query
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    // Bind parameters if any
    if (!empty($params)) {
        $stmt->bind_param($param_types, ...$params);
    }

    if (!$stmt->execute()) {
        throw new Exception('Failed to execute query: ' . $stmt->error);
    }

    $result = $stmt->get_result();

    // Fetch all users
    while ($row = $result->fetch_assoc()) {
        // Sanitize output data
        $user = [
            'user_id' => (int)$row['user_id'],
            'user_firstname' => htmlspecialchars($row['user_firstname'] ?? '', ENT_QUOTES, 'UTF-8'),
            'user_lastname' => htmlspecialchars($row['user_lastname'] ?? '', ENT_QUOTES, 'UTF-8'),
            'user_email' => htmlspecialchars($row['user_email'] ?? '', ENT_QUOTES, 'UTF-8'),
            'user_type' => (int)$row['user_type'],
            'user_status' => (int)$row['user_status'],
            'user_is_online' => (int)$row['user_is_online'],
            'user_create_date' => $row['user_create_date'],
            'user_last_login' => $row['user_last_login'],
            'user_last_ip' => htmlspecialchars($row['user_last_ip'] ?? '', ENT_QUOTES, 'UTF-8')
        ];

        $users[] = $user;
    }

    $stmt->close();

    // Log the successful retrieval (optional)
    error_log("Admin user data retrieved successfully by user ID: " . $_SESSION['user_id'] . " for type: " . $type);

    // Return success response
    echo json_encode([
        'success' => true,
        'users' => $users,
        'total_count' => count($users),
        'type' => $type,
        'message' => 'ดึงข้อมูลผู้ใช้งานสำเร็จ'
    ]);

} catch (mysqli_sql_exception $e) {
    // Database error
    error_log("Database error in get_user_data.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error',
        'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล'
    ]);

} catch (Exception $e) {
    // General error
    error_log("Error in get_user_data.php: " . $e->getMessage());
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);

} finally {
    // Close database connection if still open
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>