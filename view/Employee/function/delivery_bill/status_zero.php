<?php
session_start();
require_once('../../../../view/config/connect.php');

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
    // Get transfer type from POST data
    $transferType = $_POST['transfer_type'];

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert data into tb_delivery table
        $deliveryNumber = generateDeliveryNumber();
        $deliveryDate = date("Y-m-d H:i:s");

        $stmt = $conn->prepare("INSERT INTO tb_delivery (delivery_number, delivery_date, created_by) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $deliveryNumber, $deliveryDate, $user_id);
        $stmt->execute();
        $deliveryId = $stmt->insert_id;
        $stmt->close();

        // Insert data into tb_delivery_items table
        $stmt = $conn->prepare("INSERT INTO tb_delivery_items (delivery_id, bill_number, bill_customer_name, bill_customer_id, item_code, item_desc, item_quantity, item_unit, item_price, line_total, created_by, transfer_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        foreach ($selectedItems as $item) {
            $stmt->bind_param(
                "isssssssssis",
                $deliveryId,
                $item['billnum'],
                $item['billcus'],
                $item['billcusid'],
                $item['itemcode'],
                $item['name'],
                $item['quantity'],
                $item['unit'],
                $item['price'],
                $item['total'],
                $user_id,
                $transferType
            );
            $stmt->execute();
        }
        $stmt->close();

        // Update status to 0 for all selected bill numbers
        $billNumbers = array_column($selectedItems, 'billnum');
        $billNumbers = array_unique($billNumbers); // Ensure unique bill numbers

        foreach ($billNumbers as $billnum) {
            $stmt = $conn->prepare("UPDATE tb_header SET bill_status = 0 WHERE TRIM(bill_number) = ?");
            $stmt->bind_param("s", $billnum);
            $stmt->execute();
            $stmt->close();

            $stmt = $conn->prepare("UPDATE tb_line SET line_status = 0 WHERE TRIM(line_bill_number) = ?");
            $stmt->bind_param("s", $billnum);
            $stmt->execute();
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
}

// Close connection
$conn->close();

// Function to generate a unique delivery number
function generateDeliveryNumber()
{
    return "WDL" . date("mds") . rand(0, 99) . "TH";
}
?>