<?php
require_once('../../../config/connect.php');

require_once('../action_activity_log/log_activity.php');
// Include the logAdminActivity function here

// Function to log admin activity
function logAdminActivity($userId, $action, $entity, $entityId = null, $additionalInfo = null) {
    global $conn; // Assuming $conn is your database connection object
    
    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO admin_activity_log (userId, action, entity, entity_id, additional_info) VALUES (?, ?, ?, ?, ?)");
    
    // Bind parameters and execute the statement
    $stmt->execute([$userId, $action, $entity, $entityId, $additionalInfo]);

    return true; // Log entry inserted successfully
}


if (isset($_POST['delUser'])) {
    // Assuming $_SESSION['user_id'] contains the ID of the admin user
    $userId = $_SESSION['user_id']; 
    $action = "delete"; // Action performed by the admin
    $entity = "user"; // Entity affected by the action
    $entityId = $_POST['id']; // ID of the deleted user
    $additionalInfo = "Deleted user with ID: " . $_POST['id']; // Additional information about the action

    // Call the logAdminActivity function to log admin activity
    logAdminActivity($userId, $action, $entity, $entityId, $additionalInfo);

    // Proceed with deleting the user
    $stmt = $conn->prepare("DELETE FROM tb_user WHERE user_id = ?");
    $stmt->execute([$_POST['id']]);

}
?>

