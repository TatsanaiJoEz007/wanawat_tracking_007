<?php
include '../../config/connect.php';

if (isset($_GET['id'])) {
    $faq_id = $_GET['id'];
    
    // Delete FAQ
    $query = "DELETE FROM tb_freq WHERE freq_id = $faq_id";
    mysqli_query($conn, $query);
    
    echo "FAQ deleted successfully";
}

mysqli_close($conn);
?>
