<?php
            require_once('../../view/config/connect.php');

        $search_term = isset($_GET['search']) ? $_GET['search'] : '';

        // Query to get total number of items
        $total_items_query = "SELECT COUNT(DISTINCT d.delivery_id) as total 
                                FROM tb_delivery d 
                                INNER JOIN tb_delivery_items di ON d.delivery_id  = di.delivery_id 
                                WHERE d.created_by = $user_id";

        // Append search term filter if provided
        if ($search_term) {
            $search_term_escaped = mysqli_real_escape_string($conn, $search_term);
            $total_items_query .= " AND d.delivery_number LIKE '%$search_term_escaped%'";
        }

        // Execute query to get total count
        $total_items_result = mysqli_query($conn, $total_items_query);

        if (!$total_items_result) {
            echo "Error fetching total items: " . mysqli_error($conn);
            exit;
        }

        $total_items = mysqli_fetch_assoc($total_items_result)['total'];

        $items_per_page = 20;
        $total_pages = ceil($total_items / $items_per_page);
        $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

        $offset = ($current_page - 1) * $items_per_page;

        $query = "SELECT d.delivery_id, d.delivery_number, d.delivery_date, COUNT(di.item_code) AS item_count, d.delivery_status, di.transfer_type 
            FROM tb_delivery d 
            INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
            WHERE d.created_by = $user_id";

        // Append search term filter if provided
        if ($search_term) {
            $search_term_escaped = mysqli_real_escape_string($conn, $search_term);
            $query .= " AND d.delivery_number LIKE '%$search_term_escaped%'";
        }

        $query .= " GROUP BY d.delivery_id, di.transfer_type LIMIT $items_per_page OFFSET $offset";


        // Execute query to fetch data
        $result = mysqli_query($conn, $query);

        if (!$result) {
            echo "Error fetching data: " . mysqli_error($conn);
            exit;
        }
        ?>