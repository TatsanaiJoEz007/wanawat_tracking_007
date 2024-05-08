<?php
require_once('../../config/connect.php');

if (isset($_POST['delFreq'])) {
    $conn->query("DELETE FROM tb_freq WHERE freq_id = '$_POST[id]'");
}
?>