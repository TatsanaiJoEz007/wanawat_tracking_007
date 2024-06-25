<!DOCTYPE html>
<?php session_start(); ?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Bill</title>
    <!-- //mockup stuff fix it later broooo -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #f0f0f0;
        }

        .container {
            display: flex;
            width: 100%;
            margin: 0 auto;
        }

        .product-list {
            width: 100%;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-right: 20px;
            display: flex;
            flex-direction: column;
        }

        .product-table-container {
            flex-grow: 1;
            overflow: auto;
            overflow-y: auto;
            max-height: 600px;
            /* กำหนดความสูงคงที่ให้กับตาราง */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            padding: 10px;
            border-bottom: 2px solid #ddd;
            text-align: center;
        }

        td {
            padding: 10px;
            border-bottom: 2px solid #ddd;
            text-align: left;
        }

        th {
            background-color: #f9f9f9;
        }

        thead th {
            /* Target table header cells directly */
            position: sticky;
            top: 0;
            /* Stick to the top */
            background-color: #f9f9f9;
            /* Keep the background color */
            z-index: 1;
            /* Ensure it's above the table body when scrolling */
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php
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
    ?>

    <?php require_once('function/sidebar_employee.php'); ?>

    <br><br>


    <div class="container">
        <div class="product-list">
            <h1>บิลที่เพิ่มแล้ว</h1>
            <p style="color:red;">รายการสินค้าที่เพิ่มแล้วจะโชว์ก็ต่อเมื่อเพิ่ม Line และ Header ที่ตรงกันเรียบร้อยแล้ว</p>
            <div class="product-table-container">
                <?php
                // Calculate total unique bill numbers
                $total_bills_query = "SELECT COUNT(DISTINCT bill_number) as total FROM tb_header WHERE bill_status = 1";
                $total_bills_result = $conn->query($total_bills_query);
                $total_bills = $total_bills_result->fetch_assoc()['total'];

                $bills_per_page = 30;
                $total_pages = ceil($total_bills / $bills_per_page);
                $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

                $offset = ($current_page - 1) * $bills_per_page;

                // Your SQL query
                $sql = "SELECT DISTINCT tb_header.bill_number, tb_header.bill_customer_name, 
                                    tb_line.item_code, tb_line.item_desc, tb_line.item_quantity, 
                                    tb_line.item_unit, tb_line.item_price, tb_line.line_total, tb_line.item_sequence , tb_line.line_weight
                                    FROM tb_header
                                    INNER JOIN tb_line ON TRIM(tb_header.bill_number) = TRIM(tb_line.line_bill_number)
                                    WHERE tb_header.bill_status = 1 AND tb_line.line_status = 1
                                    LIMIT $bills_per_page OFFSET $offset";

                $result = $conn->query($sql);

                $merged_rows = [];

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $bill_number = $row["bill_number"];
                        if (!isset($merged_rows[$bill_number])) {
                            $merged_rows[$bill_number] = [
                                "bill_number" => $bill_number,
                                "bill_customer_name" => $row["bill_customer_name"],
                                "item_details" => []
                            ];
                        }
                        // Add item details to the item_details array
                        $merged_rows[$bill_number]["item_details"][] = [
                            "item_sequence" => $row["item_sequence"],
                            "item_code" => $row["item_code"],
                            "item_desc" => $row["item_desc"],
                            "item_quantity" => $row["item_quantity"],
                            "item_unit" => $row["item_unit"],
                            "item_price" => $row["item_price"],
                            "line_total" => $row["line_total"],
                            "item_weight" => $row["line_weight"]
                        ];
                    }
                }
                ?>
                <table class="zebra-striped">
                    <thead>
                        <tr>
                            <th>เลขบิล</th>
                            <th>ชื่อลูกค้า</th>
                            <th>จำนวน</th>
                            <th>รหัสสินค้า</th>
                            <th>รายละเอียด</th>
                            <th>จำนวน</th>
                            <th>หน่วย</th>
                            <th>ราคา</th>
                            <th>ราคารวม</th>
                            <th>น้ำหนัก (กิโลกรัม)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Your SQL query
                        $sql = "SELECT DISTINCT tb_header.bill_number, tb_header.bill_customer_name, 
                                    tb_line.item_code, tb_line.item_desc, tb_line.item_quantity, 
                                    tb_line.item_unit, tb_line.item_price, tb_line.line_total, tb_line.item_sequence , tb_line.line_weight
                                    FROM tb_header
                                    INNER JOIN tb_line ON TRIM(tb_header.bill_number) = TRIM(tb_line.line_bill_number)
                                    WHERE tb_header.bill_status = 1 AND tb_line.line_status = 1";

                        $result = $conn->query($sql);

                        $merged_rows = [];

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $bill_number = $row["bill_number"];
                                if (!isset($merged_rows[$bill_number])) {
                                    $merged_rows[$bill_number] = [
                                        "bill_number" => $bill_number,
                                        "bill_customer_name" => $row["bill_customer_name"],
                                        "item_details" => []
                                    ];
                                }
                                // Add item details to the item_details array
                                $merged_rows[$bill_number]["item_details"][] = [
                                    "item_sequence" => $row["item_sequence"],
                                    "item_code" => $row["item_code"],
                                    "item_desc" => $row["item_desc"],
                                    "item_quantity" => $row["item_quantity"],
                                    "item_unit" => $row["item_unit"],
                                    "item_price" => $row["item_price"],
                                    "line_total" => $row["line_total"],
                                    "item_weight" => $row["line_weight"]
                                ];
                            }
                        }

                        foreach ($merged_rows as $row) {
                            echo "<tr>";
                            echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_number"] . "</td>";
                            echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_customer_name"] . "</td>";
                            // Loop through item_details array to output each item detail
                            foreach ($row["item_details"] as $index => $item) {
                                if ($index > 0) {
                                    echo "<tr>";
                                }
                                echo "<td><center>" . $item["item_sequence"] . "</center></td>";
                                echo "<td>" . $item["item_code"] . "</td>";
                                echo "<td>" . $item["item_desc"] . "</td>";
                                echo "<td><center>" . $item["item_quantity"] . "</center></td>";
                                echo "<td><center>" . $item["item_unit"] . "</center></td>";
                                echo "<td><center>" . $item["item_price"] . "</center></td>";
                                echo "<td><center>" . $item["line_total"] . "</center></td>";
                                echo "<td><center>" . $item["line_weight"] . "</center></td>";
                                if ($index > 0) {
                                    echo "</tr>";
                                }
                            }
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <?php if ($current_page > 1) : ?>
                    <a href="?page=<?php echo ($current_page - 1); ?>" class="btn-custom">&laquo; ก่อนหน้า</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $current_page) ? 'active' : ''; ?>" class="btn-custom"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages) : ?>
                    <a href="?page=<?php echo ($current_page + 1); ?>" class="btn-custom">ถัดไป &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
</body>

</html>