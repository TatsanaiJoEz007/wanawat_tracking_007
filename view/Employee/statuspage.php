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
    $truckId = "TRK001"; // Example truck ID, you need to fetch this from somewhere
    $deliveryDate = date("Y-m-d H:i:s");

    $deliveryInsertSql = "INSERT INTO tb_delivery (delivery_number, delivery_truck_id, delivery_date) 
                          VALUES ('$deliveryNumber', '$truckId', '$deliveryDate')";

    if ($conn->query($deliveryInsertSql) === TRUE) {
        $deliveryId = $conn->insert_id;

        // Insert data into tb_delivery_items table
        foreach ($selectedItems as $item) {
            $billNumber = $item['billnum']; // Assuming the name field contains the bill number
            $desc = $item['name']; // Assuming the desc field contains the item description
            // $lineId = $item['']; // Assuming you have a line ID associated with each item

            // Other fields to insert, like quantity, can be extracted from $item as needed

            $deliveryItemsInsertSql = "INSERT INTO tb_delivery_items (delivery_id, bill_number, item_desc ,  line_id, quantity) 
                                       VALUES ('$deliveryId', '$billNumber','$desc' , '1111', '1')"; // You need to adjust the quantity value as needed

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
    // Implement your logic to generate a unique delivery number, for example:
    return "DEPYL" . date("YmdHis");
}
?>
