<?php require_once("../config/connect.php") ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <title>Delivery Bills</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="icon" type="image/x-icon" href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300&display=swap" rel="stylesheet">
    
    <style>
        h1 {
            font-size: 36px;
            color: #333;
            text-align: center;
            margin-top: 50px;
        }

        .container {
            max-width: 1500px;
            margin: 30px auto;
        }

        h1 {
            color: #343a40;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        table th,
        table td {
            padding: 12px 15px;
            border-bottom: 1px solid #dee2e6;
            color: #343a40;
        }

        table th {
            background-color: #F0592E !important;
            color: #fff;
            text-align: left;
            text-transform: uppercase;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
            border-radius: 10%;
            transition: 0.3s;
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

        ::-webkit-scrollbar {
            width: 9px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            border-radius: 10px;
        }

        .home-section {
            max-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        @media only screen and (max-width: 600px) {
            .container {
                margin: 15px auto;
            }

            table {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>
    <?php
    // db.php
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
    ?>

    <?php require_once('function/sidebar.php'); ?>
    <div class="container">
        <h1>บิล Delivery ทั้งหมด</h1>
        <br>
        <div class="search-bar">
            <form method="GET" action="">
                <input class="insearch" type="text" name="search" placeholder="Search by delivery number" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit" class="search">Search</button>
            </form>
        </div>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>เลขการจัดส่ง</th>
                    <th>เลขบิล</th>
                    <th>ชื่อลูกค้า</th>
                    <th>รหัสสินค้า</th>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวนสินค้า</th>
                    <th>หน่วย</th>
                    <th>ราคา</th>
                    <th>ราคารวม</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
            <?php
                $search_term = isset($_GET['search']) ? $_GET['search'] : '';

                // Modify query to include search term
                $sql = "SELECT d.delivery_number, di.bill_number, di.bill_customer_name, 
                di.item_code, di.item_desc, di.item_quantity, 
                di.item_unit, di.item_price, di.line_total, d.delivery_status
                FROM tb_delivery d
                INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id";

                if ($search_term) {
                    $sql .= " WHERE d.delivery_number LIKE '%" . mysqli_real_escape_string($conn, $search_term) . "%'";
                }

                $result = $conn->query($sql);

                $merged_rows = [];

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $delivery_number = $row["delivery_number"];
                        if (!isset($merged_rows[$delivery_number])) {
                            $merged_rows[$delivery_number] = [
                                "delivery_number" => $delivery_number,
                                "bill_numbers" => [],
                                "bill_customer_names" => [],
                                "item_details" => [],
                                "delivery_status" => $row["delivery_status"]
                            ];
                        }
                        $merged_rows[$delivery_number]["bill_numbers"][] = $row["bill_number"];
                        $merged_rows[$delivery_number]["bill_customer_names"][] = $row["bill_customer_name"];
                        $merged_rows[$delivery_number]["item_details"][] = [
                            "item_code" => $row["item_code"],
                            "item_desc" => $row["item_desc"],
                            "item_quantity" => $row["item_quantity"],
                            "item_unit" => $row["item_unit"],
                            "item_price" => $row["item_price"],
                            "line_total" => $row["line_total"]
                        ];
                    }
                }

                foreach ($merged_rows as $delivery_number => $row) {
                    echo "<tr>";
                    echo "<td rowspan='" . count($row["bill_numbers"]) . "'>" . $delivery_number . "</td>";
                    for ($i = 0; $i < count($row["bill_numbers"]); $i++) {
                        if ($i > 0) {
                            echo "<tr>";
                        }
                        echo "<td>" . $row["bill_numbers"][$i] . "</td>";
                        echo "<td>" . $row["bill_customer_names"][$i] . "</td>";
                        $item = $row["item_details"][$i];
                        echo "<td>" . $item["item_code"] . "</td>";
                        echo "<td>" . $item["item_desc"] . "</td>";
                        echo "<td>" . $item["item_quantity"] . "</td>";
                        echo "<td>" . $item["item_unit"] . "</td>";
                        echo "<td>" . $item["item_price"] . "</td>";
                        echo "<td>" . $item["line_total"] . "</td>";
                        if ($i == 0) {
                            // Add custom text for delivery status
                            $status_text = '';
                            switch ($row["delivery_status"]) {
                                case 1:
                                    $status_text = 'กำลังจัดเตรียม';
                                    break;
                                case 2:
                                    $status_text = 'จัดส่งไปยังศูนย์กระจายสินค้า';
                                    break;
                                case 3:
                                    $status_text = 'ถึงศูนย์กระจายสินค้า';
                                    break;
                                case 4:
                                    $status_text = 'กำลังนำส่งให้ลูกค้า';
                                    break;
                                case 5:
                                    $status_text = 'จัดส่งสำเร็จ';
                                    break;
                                case 99:
                                    $status_text = 'มีปัญหาในการจัดส่ง';
                                    break;
                                default:
                                    $status_text = 'Status Not Available';
                            }
                            echo "<td rowspan='" . count($row["bill_numbers"]) . "'>" . $status_text . "</td>";
                        }
                        echo "</tr>";
                    }
                }
                    
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#sidebarCollapse').on('click', function() {
                $('#sidebar').toggleClass('active');
            });
        });

        function myFunction() {
            alert("Preparing");
        }
    </script>
</body>

</html>