<?php
session_start();
require_once('../../config/connect.php'); // แก้ไข path ให้ตรงกับ ic_delivery.php

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    die('User not logged in');
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_items']) && isset($_POST['transfer_type'])) {
    // Decode the JSON data sent from the client
    $selectedItems = json_decode($_POST['selected_items'], true);
    
    if (!$selectedItems || !is_array($selectedItems)) {
        die('Invalid data format');
    }
    
    // Get transfer type from POST data
    $transferType = $_POST['transfer_type'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert data into tb_delivery table (ไม่ต้องมี transfer_type เพราะอยู่ใน tb_delivery_items)
        $deliveryNumber = generateDeliveryNumber();
        $deliveryDate = date("Y-m-d H:i:s");

        // Insert with step1 timestamp (รับคำสั่งซื้อ)
        $stmt = $conn->prepare("INSERT INTO tb_delivery (delivery_number, delivery_date, created_by, delivery_step1_received) VALUES (?, ?, ?, NOW())");
        if (!$stmt) {
            throw new Exception("Prepare failed for tb_delivery: " . $conn->error);
        }
        
        $stmt->bind_param("ssi", $deliveryNumber, $deliveryDate, $user_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed for tb_delivery: " . $stmt->error);
        }
        
        $deliveryId = $stmt->insert_id;
        $stmt->close();

        // Insert data into tb_delivery_items table
        $stmt = $conn->prepare("INSERT INTO tb_delivery_items (delivery_id, bill_number, bill_customer_name, bill_customer_id, item_code, item_desc, item_sequence, item_quantity, item_unit, item_price, line_total, created_by, transfer_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            throw new Exception("Prepare failed for tb_delivery_items: " . $conn->error);
        }

        foreach ($selectedItems as $item) {
            // Validate required fields
            if (!isset($item['billnum']) || !isset($item['billcus']) || !isset($item['billcusid']) || 
                !isset($item['itemcode']) || !isset($item['name']) || !isset($item['seq']) || 
                !isset($item['quantity']) || !isset($item['unit']) || !isset($item['price']) || 
                !isset($item['total'])) {
                throw new Exception("Missing required item data");
            }
            
            $stmt->bind_param(
                "issssssissdis",
                $deliveryId,
                $item['billnum'],
                $item['billcus'],
                $item['billcusid'],
                $item['itemcode'],
                $item['name'],
                $item['seq'],
                $item['quantity'],
                $item['unit'],
                $item['price'],
                $item['total'],
                $user_id,
                $transferType
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed for tb_delivery_items: " . $stmt->error);
            }
        }
        $stmt->close();

        // Update status or quantity for all selected bill numbers and sequences
        foreach ($selectedItems as $item) {
            // Fetch the current quantity from the database
            $stmt = $conn->prepare("SELECT item_quantity FROM tb_line WHERE TRIM(line_bill_number) = ? AND item_sequence = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed for SELECT: " . $conn->error);
            }
            
            $stmt->bind_param("si", $item['billnum'], $item['seq']);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed for SELECT: " . $stmt->error);
            }
            
            $stmt->bind_result($currentQuantity);
            $stmt->fetch();
            $stmt->close();

            if (floatval($item['quantity']) >= floatval($currentQuantity)) {
                // If the selected quantity equals or exceeds the current quantity, set the status to 0
                $stmt = $conn->prepare("UPDATE tb_line SET line_status = 0 WHERE TRIM(line_bill_number) = ? AND item_sequence = ?");
                if (!$stmt) {
                    throw new Exception("Prepare failed for UPDATE status: " . $conn->error);
                }
                $stmt->bind_param("si", $item['billnum'], $item['seq']);
            } else {
                // Otherwise, subtract the selected quantity from the current quantity
                $newQuantity = floatval($currentQuantity) - floatval($item['quantity']);
                $stmt = $conn->prepare("UPDATE tb_line SET item_quantity = ? WHERE TRIM(line_bill_number) = ? AND item_sequence = ?");
                if (!$stmt) {
                    throw new Exception("Prepare failed for UPDATE quantity: " . $conn->error);
                }
                $stmt->bind_param("dsi", $newQuantity, $item['billnum'], $item['seq']);
            }
            
            if (!$stmt->execute()) {
                throw new Exception("Execute failed for UPDATE: " . $stmt->error);
            }
            $stmt->close();
        }

        // Commit transaction
        $conn->commit();

        echo "success";
    } catch (Exception $e) {
        // Rollback transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Error: Invalid request method or missing data";
}

// Close connection
$conn->close();

// Function to generate a unique delivery number
function generateDeliveryNumber()
{
    return "WDL" . date("mds") . rand(100, 999) . "TH";
}
?>