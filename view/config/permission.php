<?php
function checkPermission($requiredPermissions = []) {
    if (!isset($_SESSION['permissions'])) {
        return false;
    }

    foreach ($requiredPermissions as $permission) {
        if (!isset($_SESSION['permissions'][$permission]) || $_SESSION['permissions'][$permission] != 1) {
            return false;
        }
    }

    return true;
}

function requirePermission($requiredPermissions = []) {
    if (!checkPermission($requiredPermissions)) {
        // ถ้าเป็นการร้องขอผ่าน AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์การเข้าถึง.']);
            exit;
        } else {
            // ถ้าเป็นการเข้าถึงผ่านเบราว์เซอร์ทั่วไป
            header("Location: /error-403");
            exit;
        }
    }
}
?>
