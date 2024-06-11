

<?php
// db.php

//mockup stuff fix it later broooo

$servername = "localhost";  // Usually 'localhost' if running on the same server
$username = "root";  // Replace with your database username
$password = "";  // Replace with your database password
$dbname = "wanawat_tracking";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_items'])) {
    // Decode the JSON data sent from the client
    $selectedItems = json_decode($_POST['selected_items'], true);

    // Insert data into tb_delivery table
    $deliveryNumber = generateDeliveryNumber(); // You need to implement this function
    $deliveryDate = date("Y-m-d H:i:s");

    $deliveryInsertSql = "INSERT INTO tb_delivery (delivery_number, delivery_date) 
                          VALUES ('$deliveryNumber', '$deliveryDate')";

    if ($conn->query($deliveryInsertSql) === TRUE) {
        $deliveryId = $conn->insert_id;

        // Insert data into tb_delivery_items table
        foreach ($selectedItems as $item) {
            $billNumber = $item['billnum']; // Assuming the name field contains the bill number
            $billCus = $item['billcus']; // Assuming the name field contains the bill customer name
            $itemcode = $item['itemcode']; // Assuming the name field contains the item code
            $name = $item['name']; // Assuming the name field contains the item description
            $quantity = $item['quantity']; // Assuming the name field contains the item quantity
            $unit = $item['unit']; // Assuming the name field contains the item unit
            $price = $item['price']; // Assuming the name field contains the item price
            $total = $item['total']; // Assuming the name field contains the line total
            
            // Other fields to insert, like quantity, can be extracted from $item as needed

            $deliveryItemsInsertSql = "INSERT INTO tb_delivery_items (delivery_id, bill_number, bill_customer_name , item_code , item_desc , item_quantity , item_unit , item_price , line_total) 
                                       VALUES ('$deliveryId', '$billNumber','$billCus' , '$itemcode', '$name' , '$quantity' , '$unit' , '$price' , '$total')"; // You need to adjust the quantity value as needed

            $conn->query($deliveryItemsInsertSql);
        }

        // Perform any additional actions after successful insertion, such as redirecting to a success page
        echo "Data inserted successfully!";
    } else {
        echo "Error: " . $deliveryInsertSql . "<br>" . $conn->error;
    }
}

// Close connection
$conn->close();

// Function to generate a unique delivery number
function generateDeliveryNumber() {
    return "WDL" . date("mds") . rand(0 , 99) . "TH" ;
}

?>
