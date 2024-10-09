<?php
session_start();

if (isset($_SESSION['permissions'])) {
    echo json_encode($_SESSION['permissions']);
} else {
    echo json_encode([]);
}
?>
