<?php
require_once('../config/connect.php');

if (isset($_POST['delBanner'])) {
    $conn->query("DELETE FROM tb_banner WHERE id = '$_POST[id]'");
}
?>