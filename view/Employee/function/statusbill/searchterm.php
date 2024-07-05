<?php
require_once('../../view/config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Check if user_id is set
if ($user_id === null) {
    die("User ID is not set in session.");
}

$search_term = isset($_GET['search']) ? $_GET['search'] : '';

// Prepare the query to get the total number of items
$total_items_query = "SELECT COUNT(DISTINCT d.delivery_id) as total 
                      FROM tb_delivery d 
                      INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
                      WHERE d.created_by = ?";

// Append search term filter if provided
if ($search_term) {
    $total_items_query .= " AND d.delivery_number LIKE ?";
}

$stmt = $conn->prepare($total_items_query);

if ($search_term) {
    $search_term_escaped = "%" . $search_term . "%";
    $stmt->bind_param("is", $user_id, $search_term_escaped);
} else {
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$total_items_result = $stmt->get_result();

if (!$total_items_result) {
    echo "Error fetching total items: " . $stmt->error;
    exit;
}

$total_items = $total_items_result->fetch_assoc()['total'];

$items_per_page = 20;
$total_pages = ceil($total_items / $items_per_page);
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

$offset = ($current_page - 1) * $items_per_page;

// Prepare the query to fetch data
$query = "SELECT d.delivery_id, d.delivery_number, d.delivery_date, COUNT(di.item_code) AS item_count, d.delivery_status, di.transfer_type 
          FROM tb_delivery d 
          INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
          WHERE d.created_by = ?";

// Append search term filter if provided
if ($search_term) {
    $query .= " AND d.delivery_number LIKE ?";
}

$query .= " GROUP BY d.delivery_id, d.delivery_number, d.delivery_date, d.delivery_status, di.transfer_type LIMIT ? OFFSET ?";

$stmt = $conn->prepare($query);

if ($search_term) {
    $stmt->bind_param("isii", $user_id, $search_term_escaped, $items_per_page, $offset);
} else {
    $stmt->bind_param("iii", $user_id, $items_per_page, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    echo "Error fetching data: " . $stmt->error;
    exit;
}
?>
