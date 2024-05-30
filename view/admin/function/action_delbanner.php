<?php
require_once('../../config/connect.php');

// Include the function for logging admin activity
require_once('action_activity_log/log_activity.php');   

if (isset($_POST['delBanner'])) {
    // Get banner details before deletion
    $banner_id = $_POST['id'];
    $banner_name = " "; // Fetch the banner name from the database based on $banner_id if needed
    
    // Perform deletion
    if ($conn->query("DELETE FROM tb_banner WHERE banner_id = '$banner_id'")) {
        // Log admin activity
        logAdminActivity($user_id, 'Delete', 'Banner', $banner_id, 'Deleted banner: ' . $banner_name);
        
        echo "Banner deleted successfully.";
    } else {
        echo "Error deleting banner: " . $conn->error;
    }
}
?>
