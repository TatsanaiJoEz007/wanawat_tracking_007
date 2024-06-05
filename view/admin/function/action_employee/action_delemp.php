<?php
require_once('../../../config/connect.php');
require_once('../action_activity_log/log_activity.php');

session_start(); // Make sure the session is started

if (isset($_POST['delUser'])) {
    if (isset($_SESSION['user_id'])) {
        $adminUserId = $_SESSION['user_id']; 
        $userIdToDelete = $_POST['id'];
        
        // Fetch email of the user to be deleted
        $emailStmt = $conn->prepare("SELECT user_email FROM tb_user WHERE user_id = ?");
        $emailStmt->bind_param("i", $userIdToDelete);
        $emailStmt->execute();
        $emailStmt->bind_result($userEmail);
        $emailStmt->fetch();
        $emailStmt->close();
        
        if ($userEmail) {
            $action = "delete user";
            $entity = "user";
            $additionalInfo = "Deleted user with email: " . $userEmail;

            // Call the logAdminActivity function to log admin activity
            if (logAdminActivity($adminUserId, $action, $entity, $userIdToDelete, $additionalInfo)) {
                // Proceed with deleting the user
                $stmt = $conn->prepare("DELETE FROM tb_user WHERE user_id = ?");
                $stmt->bind_param("i", $userIdToDelete);
                if ($stmt->execute() && $stmt->affected_rows > 0) {
                    echo json_encode(['status' => 'success', 'message' => 'User deleted successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to delete user.']);
                }
                $stmt->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to log admin activity.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized action.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
