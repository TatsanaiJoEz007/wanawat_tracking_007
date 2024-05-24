<?php
require_once('../../../config/connect.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['bill_id']) && !empty(trim($_GET['bill_id']))) {
        $bill_id = trim($_GET['bill_id']);

        $sql = "DELETE FROM tb_bill WHERE bill_id = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind variables to the prepared statement as parameters
            $stmt->bind_param("i", $bill_id); // Assuming bill_id is an integer
            
            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to the previous page after successful deletion
                header("location: ../../uploadedbill.php");
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
        echo json_encode(array("success" => false, "error" => "Invalid bill ID."));
    }
} else {
    echo json_encode(array("success" => false, "error" => "Invalid request method."));
}
?>
