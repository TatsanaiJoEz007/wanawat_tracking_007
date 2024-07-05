<?php
header('Content-Type: application/json');

// Ensure session is started
if (!isset($_SESSION)) {
    session_start();
}

require_once('../../view/config/connect.php');
require_once('../admin/function/action_activity_log/log_activity.php'); 

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Decode JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Debugging: Log the received data
error_log(print_r($data, true));

// Check if POST request is made
if (isset($data['login'])) {
    $user_email = $data['user_email'];
    $user_pass = md5($data['user_pass']);
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

        // Verify password
        if ($user_pass == $user['user_pass']) {
            if ($user['user_status'] != 0) {
                // Set session variables
                $_SESSION['login'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['customer_id'] = $user['customer_id'];
                $_SESSION['user_firstname'] = $user['user_firstname'];
                $_SESSION['user_lastname'] = $user['user_lastname'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['user_img'] = $user['user_img'];
                $_SESSION['user_address'] = $user['user_address'];
                $_SESSION['user_tel'] = $user['user_tel'];
                $_SESSION['user_create_at'] = $user['user_create_at'];
                
                $user_type = $user['user_type'];

                // Log activity
                $admin_user_id = $_SESSION['user_id'];
                $action = 'login';
                $entity = 'user';
                $entity_id = $user['user_id'];
                $additional_info = "User logged in with email: " . $user_email;
                logAdminActivity($admin_user_id, $action, $entity, $entity_id, $additional_info);

                // Handle "Remember Me"
                if ($remember) {
                    setcookie('username', $user_email, time() + (86400 * 30), "/"); // 30 days
                    setcookie('password', $data['user_pass'], time() + (86400 * 30), "/"); // Save plain password
                } else {
                    setcookie('username', '', time() - 3600, "/");
                    setcookie('password', '', time() - 3600, "/");
                }

                // Return user type
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
    error_log("Login key not detected");
    echo json_encode('no_post');
}
?>