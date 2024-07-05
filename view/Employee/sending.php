<?php
        require_once('../../view/config/connect.php');

        $user_id = $_SESSION['user_id'];
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>อยู่ระหว่างการขนส่ง</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="style/style.css">
</head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>
    <div class="container">
        <h1>อยู่ระหว่างการขนส่ง</h1>
        <!-- Search bar -->
        <div class="search-bar">
            <form method="GET" action="">
                <input class="insearch" type="text" name="search" placeholder="Search by delivery number" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="search">Search</button>
            </form>
        </div>
        <?php
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
            WHERE d.created_by = $user_id AND d.delivery_status IN (2, 3, 4)";

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

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>เลขบิล</th>
                        <th>จำนวน</th>
                        <th>สถานะ</th>
                        <th>วันที่สร้างบิล</th>
                        <th>ประเภทการขนย้าย</th>
                </thead>
                <tbody>
                    <?php
                    require_once "../..//view/Employee/function/get_tabledata.php"
                    ?>
                </tbody>
            </table>
        </div>

        <div class="pagination">
            <?php if ($current_page > 1) : ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" class="btn-custom">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="btn-custom <?php echo ($i == $current_page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages) : ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" class="btn-custom">Next &raquo;</a>
            <?php endif; ?>
        </div>

    </div>

    <!-- Include SweetAlert for modal notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

    
   
</body>

</html>