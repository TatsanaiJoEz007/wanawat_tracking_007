<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wanawat_tracking";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Error: User not logged in";
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_items'])) {
    // Decode the JSON data sent from the client
    $selectedItems = json_decode($_POST['selected_items'], true);

    // Insert data into tb_delivery table
    $deliveryNumber = generateDeliveryNumber();
    $deliveryDate = date("Y-m-d H:i:s");

    $deliveryInsertSql = "INSERT INTO tb_delivery (delivery_number, delivery_date, created_by) 
                          VALUES ('$deliveryNumber', '$deliveryDate', '$user_id')";

    if ($conn->query($deliveryInsertSql) === TRUE) {
        $deliveryId = $conn->insert_id;

        // Insert data into tb_delivery_items table
        foreach ($selectedItems as $item) {
            $billNumber = $item['billnum'];
            $billCus = $item['billcus'];
            $itemcode = $item['itemcode'];
            $name = $item['name'];
            $quantity = $item['quantity'];
            $unit = $item['unit'];
            $price = $item['price'];
            $total = $item['total'];

            $deliveryItemsInsertSql = "INSERT INTO tb_delivery_items (delivery_id, bill_number, bill_customer_name, item_code, item_desc, item_quantity, item_unit, item_price, line_total, created_by) 
                                       VALUES ('$deliveryId', '$billNumber', '$billCus', '$itemcode', '$name', '$quantity', '$unit', '$price', '$total', '$user_id')";

            $conn->query($deliveryItemsInsertSql);
        }

        echo "<script type='text/javascript'>
                setTimeout(function() {
                    location.href = '../delivery_bill.php';
                }, 10);
              </script>";
    } else {
        echo "Error: " . $deliveryInsertSql . "<br>" . $conn->error;
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
