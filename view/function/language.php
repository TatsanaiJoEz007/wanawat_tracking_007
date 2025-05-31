<?php
// เริ่ม output buffering ก่อนอื่น
ob_start();

// ตรวจสอบและเริ่ม session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// จัดการการเปลี่ยนภาษา
if (isset($_POST['lang'])) {
    $lang = $_POST['lang'];
    $_SESSION['lang'] = $lang;
    setcookie('lang', $lang, time() + (86400 * 30), "/");
    
    // ล้าง output buffer และส่ง JSON response
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
} elseif (isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'];
    $_SESSION['lang'] = $lang;
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    $lang = 'th';
    $_SESSION['lang'] = $lang;
    setcookie('lang', $lang, time() + (86400 * 30), "/");
}

// รวมไฟล์แปลภาษา
require_once('th_eng.php');
?>