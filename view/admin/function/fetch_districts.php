<?php
require_once('../../config/connect.php');

if (isset($_POST['amphure_id'])) {
    $amphureId = $_POST['amphure_id'];

    // Validate amphure ID (similar to province ID validation)
    if (empty($amphureId) || !is_numeric($amphureId)) {
        echo '<option value="" disabled selected>Invalid Amphure</option>';
        exit; // Stop execution
    }

    $sql = "SELECT * FROM districts WHERE amphure_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $amphureId); // Bind amphure_id as an integer
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="" disabled selected>เลือกตำบล</option>';
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row["id"]}'>{$row["name_th"]}</option>";
    }
} else {
    echo '<option value="" disabled selected>No Amphure Selected</option>';
}
?>
