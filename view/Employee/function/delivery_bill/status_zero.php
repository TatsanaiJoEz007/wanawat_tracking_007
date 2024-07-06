<?php
require_once('../../../../../view/config/connect.php'); // ปรับเส้นทางให้ถูกต้อง

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bill_number = $_POST['bill_number'];

    if (empty($bill_number)) {
        echo "Bill number is missing";
        exit;
    }

    // Check if all items in tb_line have been delivered
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM tb_line WHERE line_bill_number = ? AND line_status = 1");
    $stmt->bind_param("s", $bill_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $stmt->close();

    // If no items are left to be delivered, update tb_header status
    if ($count == 0) {
        $stmt = $conn->prepare("UPDATE tb_header SET bill_status = 0 WHERE bill_number = ?");
        $stmt->bind_param("s", $bill_number);
        $stmt->execute();
        $stmt->close();
    }

    // Update all items in tb_line to be status 0
    $stmt = $conn->prepare("UPDATE tb_line SET line_status = 0 WHERE line_bill_number = ?");
    $stmt->bind_param("s", $bill_number);
    $stmt->execute();
    $stmt->close();

    $conn->close();

    echo "Status updated successfully";
}
?>