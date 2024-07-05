<!DOCTYPE html>
<?php session_start(); ?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Bill</title>
    <!-- //mockup stuff fix it later broooo -->
    <link rel="stylesheet" href="function/delivery_bill/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php
        require_once('../../view/config/connect.php');
    ?>

    <?php require_once('function/sidebar_employee.php'); ?>

    <br><br>
    <div class="instruction-box">
        <h2>วิธีการใช้งานระบบการเลือกบิล</h2>
        <ol>
            <li>กด <b>เลือกสินค้าที่ต้องการส่ง</b> ที่ Checkbox <i style="color:red;">(เลือกได้สูงสุด 15 รายการ)</i></li>
            <li>เช็คความถูกต้องจาก <b>ตะกร้าสินค้า</b></li>
            <li>กด <b>สร้างบิล</b> เพื่ออัปโหลดบิลการจัดส่งสินค้า</li>
        </ol>
    </div>

    <div class="container">
        <div class="product-list">
            <h1>สินค้ารอเลือกบิล</h1>
            <div class="product-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>เลขบิล</th>
                            <th>ชื่อลูกค้า</th>
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

                        // Your SQL query
                        $sql = "SELECT DISTINCT tb_header.bill_number, tb_header.bill_customer_name, tb_header.bill_customer_id,
                                tb_line.item_code, tb_line.item_desc, tb_line.item_quantity, 
                                tb_line.item_unit, tb_line.item_price, tb_line.line_total
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
                                        "bill_customer_id" => $row["bill_customer_id"],

                                        "item_details" => []
                                    ];
                                }
                                // Add item details to the item_details array
                                $merged_rows[$bill_number]["item_details"][] = [
                                    "item_code" => $row["item_code"],
                                    "item_desc" => $row["item_desc"],
                                    "item_quantity" => $row["item_quantity"],
                                    "item_unit" => $row["item_unit"],
                                    "item_price" => $row["item_price"],
                                    "line_total" => $row["line_total"]
                                ];
                            }
                        }

                        foreach ($merged_rows as $row) {
                            echo "<tr>";
                            echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_number"] . "</td>";
                            echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_customer_name"] . "</td>";
                            echo "<input type='hidden' name='bill_customer_id' value='" . $row["bill_customer_id"] . "'>";
                            // echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_total"] . "</td>";
                            // Loop through item_details array to output each item detail
                            foreach ($row["item_details"] as $index => $item) {
                                if ($index > 0) {
                                    echo "<tr>";
                                }
                                echo "<td>" . $item["item_code"] . "</td>";
                                echo "<td>" . $item["item_desc"] . "</td>";
                                echo "<td>" . $item["item_quantity"] . "</td>";
                                echo "<td>" . $item["item_unit"] . "</td>";
                                echo "<td>" . $item["item_price"] . "</td>";
                                echo "<td>" . $item["line_total"] . "</td>";

                                echo "<input type='hidden' name='item_sequence[]' value='" . $index . "'>";
                                echo "<td>";
                                echo "<center>";
                                echo "<input type='checkbox' class='product-checkbox' 
                                    data-bill-number='" . $row["bill_number"] . "'
                                    data-bill-customer='" . $row["bill_customer_name"] . "'
                                    data-bill-customer-id='" . $row["bill_customer_id"] . "'
                                    data-item-code='" . $item["item_code"] . "'
                                    data-name='" . $item["item_desc"] . "' 
                                    data-quantity='" . $item["item_quantity"] . "'
                                    data-unit='" . $item["item_unit"] . "' 
                                    data-price='" . $item["item_price"] . "'
                                    data-total='" . $item["line_total"] . "' 
                                    data-item-sequence='" . $item["line_sequence"] . "'>";
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
                <!-- สินค้าที่เลือกจะปรากฏที่นี่ -->
            </ul>
            <hr>

            <h7 class="cart-total">ราคารวม: <span id="total-price">฿0</span></h7>

            <!-- เพิ่ม radio buttons สำหรับเลือกประเภทการขนส่ง -->
            <div>
                <label><input type="radio" name="transfer_type" value="Human" checked> Human</label>
                <label><input type="radio" name="transfer_type" value="Forklift"> Forklift</label>
            </div>

            <button class="create-bill-btn" id="create-bill-btn">สร้างบิล</button>
        </div>
    </div>

    <script src="function/delivery_bill/js/selectbill.js"></script>
    <script src="function/delivery_bill/js/updateStatusToZero.js"></script>



</body>

</html>