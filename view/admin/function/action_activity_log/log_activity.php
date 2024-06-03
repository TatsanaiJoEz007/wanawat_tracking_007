<?php
// Assuming you have already established a database connection

// Function to log admin activity
function logAdminActivity($userId, $action, $entity, $entityId = null, $additionalInfo = null) {
    global $conn; // Use the global $conn variable

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO admin_activity_log (userId, action, entity, entity_id, additional_info) VALUES (?, ?, ?, ?, ?)");

    // Bind parameters and execute the statement
    $stmt->bind_param("issss", $userId, $action, $entity, $entityId, $additionalInfo);
    $stmt->execute();

    // Check if the insertion was successful
    return $stmt->affected_rows > 0;
}
?>
