<?php
require_once('../../../config/connect.php');

$conn = new mysqli($host, $username, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function logAdminActivity($user_id, $action, $entity, $entity_id, $additional_info)
{
    global $conn;

    $user_id = mysqli_real_escape_string($conn, $user_id);
    $action = mysqli_real_escape_string($conn, $action);
    $entity = mysqli_real_escape_string($conn, $entity);
    $entity_id = mysqli_real_escape_string($conn, $entity_id);
    $additional_info = mysqli_real_escape_string($conn, $additional_info);

    $create_at = date('Y-m-d H:i:s');

    $sql = "INSERT INTO admin_activity_log (user_id, action, entity, entity_id, create_at, additional_info)
                VALUES ('$user_id', '$action', '$entity', '$entity_id', '$create_at', '$additional_info')";

    if ($conn->query($sql) === TRUE) {
        echo "Admin activity logged successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

    $conn->close();

?>