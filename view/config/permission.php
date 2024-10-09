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
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'คุณไม่มีสิทธิ์การเข้าถึง.']);
            exit;
        } else {
            header("Location: /error-403");
            exit;
        }
    }
}
?>
