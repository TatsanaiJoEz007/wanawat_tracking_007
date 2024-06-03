<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Join Tables without Dropdown</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <table>
        <thead>
            <tr>
                <th>Bill Number</th>
                <th>Customer Name</th>
                <th>Total</th>
                <th>Item Details</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Database connection
            $servername = "localhost";
            $username = "root"; // replace with your database username
            $password = ""; // replace with your database password
            $dbname = "wanawat_tracking"; // replace with your database name

            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            // Your provided SQL query
            $sql = "SELECT *
                    FROM tb_header
                    INNER JOIN tb_line ON TRIM(tb_header.bill_number) = TRIM(tb_line.line_bill_number)";

            $result = $conn->query($sql);

            $merged_rows = [];

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $bill_number = $row["bill_number"];
                    if (!isset($merged_rows[$bill_number])) {
                        $merged_rows[$bill_number] = [
                            "bill_number" => $bill_number,
                            "bill_customer_name" => $row["bill_customer_name"],
                            "bill_total" => $row["bill_total"],
                            "item_details" => ""
                        ];
                    }
                    $merged_rows[$bill_number]["item_details"] .= "Item Code: " . $row["item_code"] . "<br>";
                    $merged_rows[$bill_number]["item_details"] .= "Item Description: " . $row["item_desc"] . "<br> <hr>";
                }
            }

            foreach ($merged_rows as $row) {
                echo "<tr>";
                echo "<td>" . $row["bill_number"] . "</td>";
                echo "<td>" . $row["bill_customer_name"] . "</td>";
                echo "<td>" . $row["bill_total"] . "</td>";
                echo "<td>" . $row["item_details"] . "</td>";
                echo "</tr>";
            }

            $conn->close();
            ?>
        </tbody>
    </table>
</body>
</html>
