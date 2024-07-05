<?php
header('Content-Type: application/json');

// ตรวจสอบว่ามี Session หรือยัง หากยังไม่มีให้เริ่ม Session ใหม่
if (!isset($_SESSION)) {
    session_start();
}

// เชื่อมต่อกับฐานข้อมูล
require_once('../../view/config/connect.php');
require_once('../admin/function/action_activity_log/log_activity.php'); // Include log_activity.php

// ตรวจสอบว่ามีการส่งค่า POST มาจากฟอร์มหรือไม่
if (isset($_POST['login'])) {
    // รับค่าจากฟอร์ม
    $user_email = $_POST['user_email'];
    $user_pass = md5($_POST['user_pass']);
    $remember = isset($_POST['remember']) ? $_POST['remember'] : false;

    // ค้นหาผู้ใช้จากฐานข้อมูล
    $check = "SELECT * FROM tb_user WHERE user_email = ?";
    $check_user = $conn->prepare($check);
    $check_user->bind_param("s", $user_email);
    $check_user->execute();
    $result = $check_user->get_result();

    // ตรวจสอบว่ามีผู้ใช้งานนี้หรือไม่
    if ($result->num_rows >= 1) {
        // รับข้อมูลผู้ใช้งาน
        $user = $result->fetch_array();

        // ตรวจสอบรหัสผ่าน
        if ($user_pass == $user['user_pass']) {
            // ตรวจสอบสถานะของผู้ใช้งาน
            if ($user['user_status'] != 0) {
                // กำหนดค่า Session ตามประเภทผู้ใช้งาน
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

                // บันทึกการล็อกอินใน log_activity
                $admin_user_id = $_SESSION['user_id'];
                $action = 'login';
                $entity = 'user';
                $entity_id = $user['user_id'];
                $additional_info = "User logged in with email: " . $user_email;
                logAdminActivity($admin_user_id, $action, $entity, $entity_id, $additional_info);

                // การตั้งค่าสำหรับ "Remember Me"
                if ($remember) {
                    setcookie('username', $user_email, time() + (86400 * 30), "/"); // 30 days
                    setcookie('password', $_POST['user_pass'], time() + (86400 * 30), "/"); // Save plain password
                } else {
                    setcookie('username', '', time() - 3600, "/");
                    setcookie('password', '', time() - 3600, "/");
                }

                if ($user_type == 999) { // สำหรับ admin
                    $_SESSION['user_type'] = 'admin';
                    echo json_encode('admin');
                } elseif ($user_type == 0) { // สำหรับ user
                    $_SESSION['user_type'] = 'user';
                    echo json_encode('user');
                } elseif ($user_type == 1) { // สำหรับ employee
                    $_SESSION['user_type'] = 'employee';
                    echo json_encode('employee');
                } elseif ($user_type == 2) { // สำหรับ clerk
                    $_SESSION['user_type'] = 'clerk';
                    echo json_encode('clerk');
                }
            } else {
                // สถานะบัญชีถูกระงับ
                echo json_encode('close');
            }
        } else {
            // รหัสผ่านไม่ถูกต้อง
            echo json_encode('failpass');
        }
    } else {
        // ไม่พบบัญชีผู้ใช้งาน
        echo json_encode('failuser');
    }
}
?>
