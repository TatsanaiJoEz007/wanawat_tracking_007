<?php
header('Content-Type: application/json');

// ตรวจสอบว่ามี Session หรือยัง หากยังไม่มีให้เริ่ม Session ใหม่
if (!isset($_SESSION)) {
    session_start();
}

// เชื่อมต่อกับฐานข้อมูล
require_once('../config/connect.php');

// ตรวจสอบว่ามีการส่งค่า POST มาจากฟอร์มหรือไม่
if (isset($_POST['login'])) {
    // รับค่าจากฟอร์ม
    $user_email = ($_POST['user_email']);
    $user_pass = md5($_POST['user_pass']);

    // ค้นหาผู้ใช้จากฐานข้อมูล
    $check = "SELECT * FROM tb_user WHERE user_email = '$user_email'";
    $check_user = $conn->query($check);

    // ตรวจสอบว่ามีผู้ใช้งานนี้หรือไม่
    if ($check_user->num_rows >= 1) {
        // รับข้อมูลผู้ใช้งาน
        $user = $check_user->fetch_array();

        // ตรวจสอบรหัสผ่าน
        if ($user_pass == $user['user_pass']) {
            // ตรวจสอบสถานะของผู้ใช้งาน
            if ($user['user_status'] != 0) {
                // เช็คสถานะและกำหนดค่า Session ตามประเภทผู้ใช้งาน
                switch ($user['user_type']) {
                    case 999: // สำหรับ admin
                        $_SESSION['login'] = true;
                        $_SESSION['user_type'] = 'admin';
                        break;
                    case 0: // สำหรับ user
                        $_SESSION['login'] = true;
                        $_SESSION['user_type'] = 'user';
                        break;
                    case 1: // สำหรับ employee
                        $_SESSION['login'] = true;
                        $_SESSION['user_type'] = 'employee';
                        break;
                    case 2: // สำหรับ clerk
                        $_SESSION['login'] = true;
                        $_SESSION['user_type'] = 'clerk';
                        break;
                    default:
                        // หากไม่ตรงกับเงื่อนไขใด ๆ ให้กำหนดเป็น user โดยปริยาย
                        $_SESSION['login'] = true;
                        $_SESSION['user_type'] = 'user';
                        break;
                }

                // กำหนดค่า Session อื่น ๆ ตามต้องการ
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_firstname'] = $user['user_firstname'];
                $_SESSION['user_lastname'] = $user['user_lastname'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['user_img'] = $user['user_img'];
                $_SESSION['user_address'] = $user['user_address'];
                $_SESSION['user_tel'] = $user['user_tel'];
                $_SESSION['user_create_at'] = $user['user_create_at'];

                // ส่งค่ากลับเพื่อแสดงว่าเข้าสู่ระบบสำเร็จ
                echo json_encode('success');
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
