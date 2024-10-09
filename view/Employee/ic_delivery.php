<?php
require_once('../../view/config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Bill</title>
    <link rel="stylesheet" href="function/delivery_bill/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php require_once('function/sidebar_employee.php'); ?>

    <br><br>
    <div class="instruction-box">
        <h2>วิธีการใช้งานระบบการเลือกบิล</h2>
        <ol>
            <li>กด <b>เลือกจำนวนสินค้าที่ต้องการส่ง</b> ที่ Dropdown <i style="color:red;">(ต้องกดเลือกก่อน ถึงแม้จะเอาทั้งหมด)</i></li>
            <li>กด <b>เลือกสินค้าที่ต้องการส่ง</b> ที่ Checkbox <i style="color:red;">(เลือกได้สูงสุด 15 รายการ)</i></li>
            <li>เช็คความถูกต้องจาก <b>ตะกร้าสินค้า</b></li>
            <li>กด <b>สร้างบิล</b> เพื่ออัปโหลดบิลการจัดส่งสินค้า</li>
        </ol>
    </div>

        <div class="container">
            <?php
             
            ?>
            <div class="product-list">
                <h1>สินค้ารอเลือกบิล</h1>
                <label for="bill-number">Select Bill Number:</label>
        <!-- <select id="bill-number" name="bill_number">
            <option value="ic123">IC</option>
            <option value="ic456">IV</option>

        </select> -->
            <div class="product-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>เลขบิล</th>
                            <th>ชื่อลูกค้า</th>
                            <th>น้ำหนักบิล</th>
                            <th>รหัสสินค้า</th>
                            <th>รายละเอียด</th>
                            <th>จำนวน</th>
                            <th>หน่วย</th>
                            <th>ราคา</th>
                            <th>ราคารวม</th>
                            <th>เลือก</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Your SQL Query
                        $sql = "SELECT DISTINCT tb_header.bill_number, tb_header.bill_customer_name, tb_header.bill_customer_id, tb_header.bill_weight,
                                tb_line.item_code, tb_line.item_desc, tb_line.item_quantity, 
                                tb_line.item_unit, tb_line.item_price, tb_line.line_total, tb_line.item_sequence
                                FROM tb_header
                                INNER JOIN tb_line ON TRIM(tb_header.bill_number) = TRIM(tb_line.line_bill_number)
                                WHERE tb_header.bill_status = 1 AND tb_line.line_status = 1 AND tb_line.line_bill_number LIKE '%ic%'";

                        $result = $conn->query($sql);

                        $merged_rows = [];

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $bill_number = $row["bill_number"];
                                if (!isset($merged_rows[$bill_number])) {
                                    $merged_rows[$bill_number] = [
                                        "bill_number" => $bill_number,
                                        "bill_customer_name" => $row["bill_customer_name"],
                                        "bill_customer_id" => $row["bill_customer_id"],
                                        "bill_weight" => number_format((float)$row["bill_weight"] ?? 0, 2),  // Force 2 decimal places for weight
                                        "item_details" => []
                                    ];
                                }

                                // Add item details to the item_details array
                                $merged_rows[$bill_number]["item_details"][] = [
                                    "item_code" => $row["item_code"],
                                    "item_desc" => $row["item_desc"],
                                    "item_quantity" => (float)$row["item_quantity"],  // Store as float for dropdown generation
                                    "item_unit" => $row["item_unit"],
                                    "item_price" => number_format((float)$row["item_price"], 2),  // Force 2 decimal places for price
                                    "line_total" => number_format((float)$row["line_total"], 2),  // Force 2 decimal places for total
                                    "item_sequence" => $row["item_sequence"]
                                ];
                            }
                        }

                        // Output the data
                        foreach ($merged_rows as $row) {
                            echo "<tr>";
                            echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_number"] . "</td>";
                            echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_customer_name"] . "</td>";
                            echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_weight"] . "</td>";
                            echo "<input type='hidden' name='bill_customer_id' value='" . $row["bill_customer_id"] . "'>";

                            foreach ($row["item_details"] as $index => $item) {
                                if ($index > 0) {
                                    echo "<tr>";
                                }
                                echo "<td>" . $item["item_code"] . "</td>";
                                echo "<td>" . $item["item_desc"] . "</td>";

                                // Adjusted dropdown logic for reducing the number of options
                                echo "<td>";
                                echo "<select class='quantity-dropdown' data-item-code='" . $item["item_code"] . "'>";
                                $maxQuantity = (float)$item["item_quantity"];
                                $step = 1;  // Step size increased to reduce the number of options
                                
                                // Use integer steps for whole quantities and show the exact number for non-integer values
                                for ($i = $step; $i <= ceil($maxQuantity); $i++) {
                                    // If it's a float value, use two decimal places; if it's an integer, display it as an integer
                                    $displayValue = (fmod($i, 1) == 0) ? (int)$i : number_format($i, 2);
                                    $selected = (abs($i - $maxQuantity) < 0.01) ? "selected" : "";  // Tolerance for floating point comparison
                                    echo "<option value='" . number_format($i, 2) . "' $selected>" . $displayValue . "</option>";
                                }
                                echo "</select>";
                                echo "</td>";

                                echo "<td>" . $item["item_unit"] . "</td>";
                                echo "<td>" . $item["item_price"] . "</td>";
                                echo "<td>" . $item["line_total"] . "</td>";
                                echo "<input type='hidden' name='item_sequence[]' value='" . $index . "'>";
                                echo "<td>";
                                echo "<center>";
                                echo "<input type='checkbox' class='product-checkbox' 
                                    data-bill-number='" . $row["bill_number"] . "' 
                                    data-bill-weight='" . $row["bill_weight"] . "' 
                                    data-bill-customer='" . $row["bill_customer_name"] . "' 
                                    data-bill-customer-id='" . $row["bill_customer_id"] . "' 
                                    data-item-code='" . $item["item_code"] . "' 
                                    data-name='" . $item["item_desc"] . "' 
                                    data-quantity='" . $item["item_quantity"] . "' 
                                    data-unit='" . $item["item_unit"] . "' 
                                    data-price='" . $item["item_price"] . "' 
                                    data-total='" . $item["line_total"] . "' 
                                    data-item-sequence='" . $item["item_sequence"] . "'>";
                                echo "</center>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        }

                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="cart">
            <h2 class="cart-title">ตะกร้าสินค้า</h2>
            <ul class="cart-items" id="cart-items">
                <!-- Selected items will be shown here -->
            </ul>
            <hr>

            <h7 class="cart-total">ราคารวม: <span id="total-price">฿0.00</span></h7>

            <div>
                <label><input type="radio" name="transfer_type" value="Human" checked> Human</label>
                <label><input type="radio" name="transfer_type" value="Forklift"> Forklift</label>
            </div>

            <button class="create-bill-btn" id="create-bill-btn">สร้างบิล</button>
        </div>
    </div>

    <script src="function/delivery_bill/js/selectbill.js"></script>

</body>
</html>