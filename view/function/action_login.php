<?php
header('Content-Type: application/json');

if (!isset($_SESSION)) {
    session_start();
}

require_once('../../view/config/connect.php');
require_once('../admin/function/action_activity_log/log_activity.php'); 

// Decode JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Check if POST request is made
if (isset($data['login'])) {
    $user_email = $data['user_email'];
    $user_pass = $data['user_pass'];
    $remember = isset($data['remember']) ? $data['remember'] : false;

    // Validate input
    if (empty($user_email) || empty($user_pass)) {
        echo json_encode('invalid_input');
        exit;
    }

    // Query user in the database
    $check = "SELECT * FROM tb_user WHERE user_email = ?";
    $check_user = $conn->prepare($check);
    $check_user->bind_param("s", $user_email);
    $check_user->execute();
    $result = $check_user->get_result();

    // Check if user exists
    if ($result->num_rows >= 1) {
        $user = $result->fetch_array();

        // Verify password using password_verify()
        if (password_verify($user_pass, $user['user_pass'])) {
            if ($user['user_status'] != 0) {
                // Regenerate session ID for security
                session_regenerate_id(true);

                // Get user activity tracking data
                $current_time = date('Y-m-d H:i:s');
                $user_ip = getUserIP();
                $user_browser = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
                $device_info = getDeviceInfo();
                $current_session_id = session_id();

                // Update user activity tracking
                $update_activity = "UPDATE tb_user SET 
                    user_last_login = ?,
                    user_is_online = 1,
                    user_last_ip = ?,
                    user_last_browser = ?,
                    user_device_info = ?,
                    user_current_session_id = ?,
                    user_login_count = user_login_count + 1,
                    user_last_activity = ?
                    WHERE user_id = ?";
                
                $update_stmt = $conn->prepare($update_activity);
                $update_stmt->bind_param("ssssssi", 
                    $current_time, 
                    $user_ip, 
                    $user_browser, 
                    $device_info, 
                    $current_session_id, 
                    $current_time,
                    $user['user_id']
                );
                $update_stmt->execute();

                // Set basic session variables
                $_SESSION['login'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['customer_id'] = $user['customer_id'];
                $_SESSION['user_firstname'] = $user['user_firstname'];
                $_SESSION['user_lastname'] = $user['user_lastname'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['user_create_at'] = $user['user_create_at'];
                $_SESSION['login_time'] = time(); // Store login timestamp for session duration calculation
                $user_type = $user['user_type'];

                // ดึงข้อมูลสิทธิ์จาก tb_role ตาม user_type
                $role_query = "SELECT * FROM tb_role WHERE role_id = ?";
                $role_stmt = $conn->prepare($role_query);
                $role_stmt->bind_param("i", $user_type);
                $role_stmt->execute();
                $role_result = $role_stmt->get_result();

                if ($role_result->num_rows >= 1) {
                    $role = $role_result->fetch_array();

                    // เก็บสิทธิ์ไว้ใน session เพื่อใช้ในการตรวจสอบสิทธิ์
                    $_SESSION['permissions'] = [
                        'manage_permission' => $role['manage_permission'],
                        'manage_website' => $role['manage_website'],
                        'manage_logs' => $role['manage_logs'],
                        'manage_csv' => $role['manage_csv'],
                        'manage_statusbill' => $role['manage_statusbill'],
                        'manage_history' => $role['manage_history'],
                        'manage_problem' => $role['manage_problem'],
                        'manage_ic_delivery' => $role['manage_ic_delivery'],
                        'manage_iv_delivery' => $role['manage_iv_delivery']
                    ];
                }

                // Log activity
                $admin_user_id = $_SESSION['user_id'];
                $action = 'login';
                $entity = 'user';
                $entity_id = $user['user_id'];
                $additional_info = "User logged in with email: " . $user_email . " from IP: " . $user_ip . " using: " . $device_info;
                logAdminActivity($admin_user_id, $action, $entity, $entity_id, $additional_info);

                // Set user type in session
                if ($user_type == 999) {
                    $_SESSION['user_type'] = 'admin';
                    echo json_encode('admin');
                } elseif ($user_type == 0) {
                    $_SESSION['user_type'] = 'user';
                    echo json_encode('user');
                } elseif ($user_type == 1) {
                    $_SESSION['user_type'] = 'employee';
                    echo json_encode('employee');
                } elseif ($user_type == 2) {
                    $_SESSION['user_type'] = 'clerk';
                    echo json_encode('clerk');
                } else {
                    $_SESSION['user_type'] = 'undefined';
                    echo json_encode('undefined');
                }
            } else {
                echo json_encode('close');
            }
        } else {
            echo json_encode('failpass');
        }
    } else {
        echo json_encode('failuser');
    }
} else {
    echo json_encode('no_post');
}

// Function to get user's real IP address
function getUserIP() {
    $ip_keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (array_map('trim', explode(',', $_SERVER[$key])) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
}

// Function to get device information
function getDeviceInfo() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    // Detect browser
    $browser = 'Unknown';
    if (preg_match('/Chrome/i', $user_agent)) {
        $browser = 'Chrome';
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $browser = 'Firefox';
    } elseif (preg_match('/Safari/i', $user_agent)) {
        $browser = 'Safari';
    } elseif (preg_match('/Edge/i', $user_agent)) {
        $browser = 'Edge';
    } elseif (preg_match('/Opera/i', $user_agent)) {
        $browser = 'Opera';
    }
    
    // Detect operating system
    $os = 'Unknown';
    if (preg_match('/Windows/i', $user_agent)) {
        $os = 'Windows';
    } elseif (preg_match('/Mac/i', $user_agent)) {
        $os = 'macOS';
    } elseif (preg_match('/Linux/i', $user_agent)) {
        $os = 'Linux';
    } elseif (preg_match('/Android/i', $user_agent)) {
        $os = 'Android';
    } elseif (preg_match('/iOS/i', $user_agent)) {
        $os = 'iOS';
    }
    
    // Detect device type
    $device_type = 'Desktop';
    if (preg_match('/Mobile/i', $user_agent)) {
        $device_type = 'Mobile';
    } elseif (preg_match('/Tablet/i', $user_agent)) {
        $device_type = 'Tablet';
    }
    
    return $browser . ' on ' . $os . ' (' . $device_type . ')';
}
?>