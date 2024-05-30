<?php
require_once('../../../config/connect.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['line_id']) && !empty(trim($_GET['line_id']))) {
        $line_id = trim($_GET['line_id']);

        $sql = "DELETE FROM tb_line WHERE line_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("i", $line_id); // Assuming line_id is an integer
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to the previous page after successful deletion
                header("location: ../../table_line.php");
                exit();
            } else {
                echo json_encode(array("success" => false, "error" => "Error executing the delete statement."));
            }

            // Close statement
            $stmt->close();
        } else {
            echo json_encode(array("success" => false, "error" => "Error preparing the delete statement."));
        }
    } else {
        echo json_encode(array("success" => false, "error" => "Invalid line ID."));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Invalid request method."));
}
?>
