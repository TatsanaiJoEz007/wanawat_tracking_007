<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee-Preparing</title>
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
            background-color: #F0592E;
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
    <div class="container">
        <h1>Preparing</h1>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>Delivery Number</th>
                    <th>Bill Number</th>
                    <th>Customer Name</th>
                    <th>Item Code</th>
                    <th>Item Description</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Line Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Your PHP code here

                // Your SQL query to fetch data from tb_delivery and tb_delivery_items
                $sql = "SELECT d.delivery_number, di.bill_number, di.bill_customer_name, 
                di.item_code, di.item_desc, di.item_quantity, 
                di.item_unit, di.item_price, di.line_total
                FROM tb_delivery d
                INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id
                WHERE d.delivery_status = 1";

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
                                "item_details" => []
                            ];
                        }
                        $merged_rows[$delivery_number]["bill_numbers"][] = $row["bill_number"];
                        $merged_rows[$delivery_number]["bill_customer_names"][] = $row["bill_customer_name"];
                        // Add item details to the item_details array
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
                        echo "</tr>";
                    }
                }

                $conn->close();
                ?>

            </tbody>
        </table>
    </div>

    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
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