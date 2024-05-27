<?php
require_once('../../../config/connect.php');

if (isset($_POST['delUser'])) {
    $conn->query("DELETE FROM tb_user WHERE user_id = '$_POST[id]'");
}
?>