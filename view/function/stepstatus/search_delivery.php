<?php
// Establish database connection
require_once('../view/config/connect.php');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch delivery number or bill number from URL parameter
$trackingId = htmlspecialchars($_GET['trackingId']);

// Initialize variables
$delivery_number = null;
$delivery_date = null;
$delivery_status = null;
$searchByBillNumber = false;

// Query to fetch details from tb_delivery using delivery_number
$sql = "SELECT d.delivery_number, d.delivery_date, d.delivery_status 
            FROM tb_delivery AS d
            WHERE d.delivery_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $trackingId);
$stmt->execute();
$stmt->bind_result($delivery_number, $delivery_date, $delivery_status);
$stmt->fetch();
$stmt->close();

if (!$delivery_number) {
    $sql = "SELECT di.delivery_id, di.bill_number 
            FROM tb_delivery_items AS di
            WHERE TRIM(di.bill_number) =?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $trackingId);
    $stmt->execute();
    $stmt->bind_result($delivery_id, $bill_number);
    $stmt->fetch();
    $stmt->close();

    if ($bill_number) {
        $searchByBillNumber = true;
        // Fetch delivery_number, delivery_date, and delivery_status from tb_delivery using delivery_id
        $sql = "SELECT d.delivery_number, d.delivery_date, d.delivery_status 
                FROM tb_delivery AS d
                WHERE d.delivery_id =?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $delivery_id);
        $stmt->execute();
        $stmt->bind_result($temp_delivery_number, $temp_delivery_date, $temp_delivery_status);
        $stmt->fetch();
        $stmt->close();

        // Update main variables
        $delivery_number = $temp_delivery_number;
        $delivery_date = $temp_delivery_date;
        $delivery_status = $temp_delivery_status;
    }
}


echo "</pre>";

// Determine active steps based on delivery status
$active_steps = [];
if ($delivery_number && $delivery_status !== null) {
    $active_steps = range(1, $delivery_status);
} else {
    echo "<b>ไม่พบวันที่คำสั่งซื้อเข้าระบบ</b>"; // If neither delivery number nor bill number is found
}

$show_error = false;
if ($delivery_status == 99) {
    $show_error = true;
}

// Close connection
$conn->close();
