<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Connect to the database
    $conn = mysqli_connect("localhost", "root", "", "wanawat_tracking");

    // Check connection
    if (!$conn) {
        throw new Exception('Connection failed: '. mysqli_connect_error());
    }

    $deliveryIds = [];
    
    // Check if deliveryId is set in POST
    if (isset($_POST['deliveryId'])) {
        $deliveryIds[] = $_POST['deliveryId'];
    }
    
    // Check if deliveryIds is set in POST
    if (isset($_POST['deliveryIds'])) {
        $additionalIds = explode(',', $_POST['deliveryIds']);
        $additionalIds = array_map('trim', $additionalIds);
        $deliveryIds = array_merge($deliveryIds, $additionalIds);
    }
    
    // If no deliveryId or deliveryIds were set, throw an error
    if (empty($deliveryIds)) {
        throw new Exception('Delivery ID(s) are not set in POST');
    }

    // Create placeholders for the query
    $placeholders = implode(',', array_fill(0, count($deliveryIds), '?'));

    // Prepare the SQL query
    $query = "SELECT 
                TRIM(di.bill_number),
                TRIM(di.bill_customer_name),
                TRIM(di.item_code), 
                TRIM(di.item_desc), 
                TRIM(di.item_quantity), 
                TRIM(di.item_unit), 
                TRIM(di.item_price), 
                TRIM(di.line_total),
                TRIM(di.delivery_id) 
            FROM 
                tb_delivery_items di 
            WHERE 
                TRIM(di.delivery_id) IN ($placeholders)";

    $stmt = $conn->prepare($query);

    // Bind the parameters
    $types = str_repeat('s', count($deliveryIds)); // Assuming delivery IDs are strings
    $stmt->bind_param($types, ...$deliveryIds);

    // Execute the query
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception('Query failed: '. mysqli_error($conn));
    }

    // Fetch the data
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data['items'][] = $row;
    }

    // Check for any output before the JSON data
    if (ob_get_contents()) {
        throw new Exception('Output before JSON data');
    }

    // Return the data as JSON
    ob_clean();
    header('Content-Type: application/json');
    $json_data = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON encoding error: '. json_last_error_msg());
    }
    echo $json_data;

} catch (Exception $e) {
    $error = array('error' => $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode($error);
    exit;
}

// Close the database connection
mysqli_close($conn);
?>
