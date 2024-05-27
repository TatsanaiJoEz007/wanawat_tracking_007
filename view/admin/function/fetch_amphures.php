<?php
require_once('../../config/connect.php'); 

if (isset($_POST['province_id'])) {
    $provinceId = $_POST['province_id'];

    // Validate province ID
    if (empty($provinceId) || !is_numeric($provinceId)) {
        echo '<option value="" disabled selected>Invalid Province</option>';
        exit; // Stop execution
    }

    $sql = "SELECT * FROM amphures WHERE province_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $provinceId); // Bind province_id as an integer
    $stmt->execute();
    $result = $stmt->get_result(); // Use a different variable to store the query result

    echo '<option value="" disabled selected>เลือกอำเภอ</option>';
    while ($row = $result->fetch_assoc()) { // Use $row for the fetched row
        echo "<option value='{$row["id"]}'>{$row["name_th"]}</option>";
    }
} else {
    echo '<option value="" disabled selected>No Province Selected</option>';
}
?>
