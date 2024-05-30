<?php
// Assuming you have already established a database connection

// Function to log admin activity
function logAdminActivity($userId, $action, $entity, $entityId = null, $additionalInfo = null) {
    global $pdo; // Assuming $pdo is your database connection object

    // Prepare the SQL statement
    $stmt = $pdo->prepare("INSERT INTO admin_activity_log (userId, action, entity, entity_id, additional_info) VALUES (?, ?, ?, ?, ?)");

    // Bind parameters and execute the statement
    $stmt->execute([$userId, $action, $entity, $entityId, $additionalInfo]);

    // Check if the insertion was successful
    return $stmt->rowCount() > 0;
}
?>
