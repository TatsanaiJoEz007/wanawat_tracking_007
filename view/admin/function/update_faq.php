<?php
// Include database conn
include '../../config/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all fields are filled
    if (!empty($_POST['faq_id']) && !empty($_POST['header']) && !empty($_POST['content'])) {
        $faq_id = $_POST['faq_id'];
        $header = $_POST['header'];
        $content = $_POST['content'];
        
        // Update FAQ details in the database
        $query = "UPDATE tb_freq SET freq_header='$header', freq_content='$content' WHERE freq_id=$faq_id";
        $result = mysqli_query($conn, $query);
        
        if ($result) {
            echo "FAQ updated successfully";
        } else {
            echo "Error updating FAQ: " . mysqli_error($conn);
        }
    } else {
        echo "All fields are required";
    }
}

// Close database conn
mysqli_close($conn);
?>
