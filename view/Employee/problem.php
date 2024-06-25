<?php
    session_start();

    // db.php
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

    $user_id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Problem Parcel</title>
        <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
        <script src="https://cdn.lordicon.com/lordicon.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <style>
            body {
                margin: 0;
                padding: 0;
                background-color: #fff;
            }

            .container {
                margin: 20px auto;
                max-width: 1200px;
                padding: 0 20px;
                background-color: #ffff;
            }

            h2 {
                font-size: 2.5em;
                text-align: center;
                margin-bottom: 20px;
                color: #333;
            }

            .search-bar {
                text-align: center;
                margin-bottom: 20px;
            }

            .search-bar input {
                width: 80%;
                padding: 10px;
                font-size: 1rem;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .table-container {
                width: 100%;
                margin: 0 auto;
                
                overflow-x: auto;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }

            table,
            th,
            td {
                border: 1px solid #ccc;
            }

            th,
            td {
                padding: 10px;
                text-align: left;
            }

            th {
                background-color: #ff9967;
                color: white;
            }

            .search {
                background-color: #f0592e;
                color: white;
                margin-top: 20px;
                margin-left: 20px;
                margin-right: 20px;
                padding: 10px;
                font-size: 1rem;
                border: 1px solid #ccc;
                border-radius: 5px;
            }

            .search:hover {
                background-color: #F1693E;
                cursor: pointer;
                transition: 0.3s ease-in-out;
            }

            .status-red {
                background-color: #ffcccc;
            }

            .status-green {
                background-color: #ccffcc;
            }

            .status-yellow {
                background-color: #ffffcc;
            }

            .status-blue {
                background-color: #cce5ff;
            }

            .status-purple {
                background-color: #dfe2fb;
            }

            .status-grey {
                background-color: #f0f2f5;
            }

            .btn-custom {
                background-color: #007bff;
                color: white;
                border: none;
                padding: 0.5rem 1rem;
                border-radius: 0.25rem;
                cursor: pointer;
                transition: background-color 0.3s;
                text-decoration: none;
            }

            .btn-custom:hover {
                background-color: #0056b3;
            }

            .btn-red {
                background-color: #dc3545;
            }

            .btn-red:hover {
                background-color: #c82333;
            }

            /* Modal Styles */
            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                overflow: auto;
                background-color: rgba(0, 0, 0, 0.4);
            }

            .modal-content {
                background-color: #fefefe;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                max-width: 600px;
                border-radius: 10px;
                position: relative;
            }

            .close {
                color: #aaa;
                float: right;
                font-size: 28px;
                font-weight: bold;
            }

            .close:hover,
            .close:focus {
                color: black;
                text-decoration: none;
                cursor: pointer;
            }

            ::-webkit-scrollbar {
                width: 9px;
                /* Adjust width for vertical scrollbar */
            }

            ::-webkit-scrollbar-thumb {
                background-color: #FF5722;
                /* Color for scrollbar thumb */
                border-radius: 10px;
                /* Rounded corners for scrollbar thumb */
            }

            .home-section {
                max-height: 100vh;
                /* Adjust height as needed */
                overflow-y: auto;
                /* Allow vertical scroll */
                overflow-x: hidden;
                /* Prevent horizontal scroll */
                padding: 20px;
                background-color: #f9f9f9;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                border-radius: 10px;
            }

            .instruction-box {
                background-color: #FFA84C;
                /* Light grey background */
                padding: 15px;
                border-radius: 10px;
                margin-bottom: 20px;
                cursor: pointer;
            }

            .instruction-box h2 {
                font-size: 24px;
                margin-bottom: 15px;
                color: #333;
            }

            .instruction-list {
                list-style-type: none;
                padding-left: 20px;
                margin: 0;
                color: #555;
                /* Text color */
                display: none;
            }

            .instruction-box.active .instruction-list {
                display: block;

            }

            .expand-icon {
                font-size: 24px;
                color: white;
                float: right;
                transition: transform 0.3s ease;
            }

            .active .expand-icon {
                transform: rotate(45deg);
            }

            .button-cute {
                background-color: #f0592e;
                border: 2px solid #f0600e;
                border-radius: 12px;
                padding: 10px 20px;
                cursor: pointer;
                transition: transform 0.3s ease-in-out;
            }

            .button-cute:hover {
                background-color: #f0500e;
                transform: translateY(-3px);
            }

            .button-cute a {
                text-decoration: none;
                color: #fff;
                font-size: 18px;
                transition: color 0.3s ease-in-out;
            }

            .button-cute:hover a {
                color: #ffdeeb;
            }

            .button-cute a::after {
                opacity: 0;
                transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
                transform: translateX(-5px);
            }

            .button-cute:hover a::after {
                opacity: 1;
                transform: translateX(0);
            }

            .pagination {
                display: flex;
                justify-content: center;
                margin-top: 20px;
            }

            .pagination a {
                margin: 0 5px;
                padding: 8px 12px;
                border: 1px solid #ddd;
                text-decoration: none;
                color: #333;
            }

            .pagination a.active {
                background-color: #007bff;
                color: white;
                border-color: #007bff;
            }
        </style>

    </head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>
    <div class="container">
    <button id="updateSelectedBtn" class="btn-custom" style="display:none;">สินค้าที่มีปัญหา</button>
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
            WHERE d.created_by = $user_id AND d.delivery_status = 99";

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
                    if (mysqli_num_rows($result) > 0) {
                        $i = 1;
                        while ($row = mysqli_fetch_assoc($result)) {
                            switch ($row['delivery_status']) {
                                case 1:
                                    $status_text = 'สถานะสินค้าที่คำสั่งซื้อเข้าสู่ระบบ';
                                    $status_class = 'status-blue';
                                    break;
                                case 2:
                                    $status_text = 'สถานะสินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า';
                                    $status_class = 'status-yellow';
                                    break;
                                case 3:
                                    $status_text = 'สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลาย';
                                    $status_class = 'status-grey';
                                    break;
                                case 4:
                                    $status_text = 'สถานะสินค้าที่กำลังนำส่งให้ลูกค้า';
                                    $status_class = 'status-purple';
                                    break;
                                case 5:
                                    $status_text = 'สถานะสินค้าที่ถึงนำส่งให้ลูกค้าสำเร็จ';
                                    $status_class = 'status-green';
                                    break;
                                case 99:
                                    $status_text = 'สถานะสินค้าที่เกิดปัญหา';
                                    $status_class = 'status-red';
                                    break;
                                default:
                                    $status_text = 'Unknown';
                                    break;
                            }

                            // Output the row in the table

                            echo '<tr class="' . $status_class . '">';
                            echo '<td>' . $i . '</td>';
                            echo '<td>' . $row['delivery_number'] . '</td>';
                            echo '<td>' . $row['item_count'] . '</td>';
                            echo '<td>' . $status_text . '</td>';
                            echo '<td>' . $row['delivery_date'] . '</td>';
                            echo '<td>' . $row['transfer_type'] . '</td>';
                            echo '</tr>';

                            $i++;
                        }
                    } else {
                        echo "<tr><td colspan='6'>No delivery bills found.</td></tr>";
                    }
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

    <!-- JavaScript section for modal interaction -->
    <script>
        $(document).ready(function() {
            $("#myTable").DataTable();
        });
    </script>
</body>

</html>