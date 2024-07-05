<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $billNumber = $_POST['bill_number'];

    // Create a connection to the database
    require_once('../../../view/config/connect.php');

    // Update the status to 0 for the given bill number
    $sql = "UPDATE tb_line SET status = 0 WHERE bill_number = '$billNumber'";

    // Execute the query and check for errors
    if ($conn->query($sql) === TRUE) {
        echo 'Status updated successfully';
    } else {
        echo 'Error updating status: ' . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>