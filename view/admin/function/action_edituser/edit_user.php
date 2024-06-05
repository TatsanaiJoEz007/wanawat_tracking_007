<?php
require_once('../../../config/connect.php'); // Include the database connection file
require_once('../action_activity_log/log_activity.php'); // Include the logging function

session_start(); // Start the session to access session variables

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_user_id'], $_POST['customer_id'])) {
        $userId = $_POST['edit_user_id'];
        $customerId = $_POST['customer_id'];

        // Check if the user ID is valid
        if (!filter_var($userId, FILTER_VALIDATE_INT)) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid user ID.']);
            exit;
        }

        // Fetch the current customer_id for logging purposes
        $oldCustomerIdStmt = $conn->prepare("SELECT customer_id FROM tb_user WHERE user_id = ?");
        if (!$oldCustomerIdStmt) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
            exit;
        }
        $oldCustomerIdStmt->bind_param("i", $userId);
        $oldCustomerIdStmt->execute();
        $oldCustomerIdStmt->bind_result($oldCustomerId);
        $oldCustomerIdStmt->fetch();
        $oldCustomerIdStmt->close();

        if ($oldCustomerId === null) {
            echo json_encode(['status' => 'error', 'message' => 'User not found.']);
            exit;
        }

        if (isset($_SESSION['user_id'])) {
            $adminUserId = $_SESSION['user_id'];
            $action = "update customer id for user";
            $entity = "user";
            $additionalInfo = "Updated customer ID from $oldCustomerId to $customerId for user ID: $userId";

            // Log the admin activity
            if (logAdminActivity($adminUserId, $action, $entity, $userId, $additionalInfo)) {
                // Update the user record with the new customer ID
                $sql = "UPDATE tb_user SET customer_id = ? WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
                    exit;
                }
                $stmt->bind_param("si", $customerId, $userId);
                
                if ($stmt->execute()) {
                    echo json_encode(['status' => 'success', 'message' => 'Customer ID updated successfully']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update Customer ID']);
                }

                $stmt->close();
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to log admin activity.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized action.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>
