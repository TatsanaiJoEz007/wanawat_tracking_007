<?php
// Your SQL query
$sql = "SELECT DISTINCT tb_header.bill_number, tb_header.bill_customer_name, tb_header.bill_weight ,
                                    tb_line.item_code, tb_line.item_desc, tb_line.item_quantity, 
                                    tb_line.item_unit, tb_line.item_price, tb_line.line_total, tb_line.item_sequence 
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
                "bill_weight" => $row["bill_weight"],
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
        ];
    }
}

foreach ($merged_rows as $row) {
    echo "<tr>";
    echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_number"] . "</td>";
    echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_customer_name"] . "</td>";
    echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_weight"] . "</td>";
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
        if ($index > 0) {
            echo "</tr>";
        }
    }
}

$conn->close();
