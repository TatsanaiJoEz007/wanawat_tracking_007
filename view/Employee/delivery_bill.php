<!DOCTYPE html>
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
            width: 70%;
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

        .cart {
            width: 30%;
            background-color: #f9f9f9;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            max-height: 600px;
            /* กำหนดความสูงคงที่ให้กับ cart */
            overflow: auto;
            /* กำหนดการเลื่อนภายใน cart */
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
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

        .cart-title {
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .cart-items {
            list-style-type: none;
            padding: 0;
            flex-grow: 1;
        }

        .create-bill-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
            align-self: center;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .create-bill-btn:hover {
            background-color: #45a049;
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
                            <th>Select</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        // Your SQL query
                        $sql = "SELECT DISTINCT tb_header.bill_number, tb_header.bill_customer_name, 
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
                                echo "<td><center><input type='checkbox' class='product-checkbox' 
                                    data-bill-number='" . $row["bill_number"] . "'
                                    data-bill-customer= '" . $row["bill_customer_name"] . "'
                                    data-item-code= '" . $item["item_code"] . "'
                                    data-name='" . $item["item_desc"] . "' 
                                    data-quantity= '" . $item["item_quantity"] . "'
                                    data-unit='" . $item["item_unit"] . "' 
                                    data-price='" . $item["item_price"] . "'
                                    data-total='" . $item["line_total"] . "' 
                                    data-item-sequence='" . $item["line_sequence"] . "'>
                                </center></td>";
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
            <button class="create-bill-btn" id="create-bill-btn">สร้างบิล</button>
        </div>
    </div>


    <script>
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const cartItems = document.getElementById('cart-items');
        const totalPriceElement = document.getElementById('total-price');
        const createBillBtn = document.getElementById('create-bill-btn');
        let itemCounter = 1;
        const maxItems = 15;
        let selectedItems = []; // Array to store selected items

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                if (checkbox.checked && itemCounter > maxItems) {
                    Swal.fire('เกิดข้อผิดพลาด!', 'เลือกสินค้าได้มากที่สุด 15 ชิ้นต่อการขนส่ง 1 ครั้ง', 'error');
                    checkbox.checked = false; // Uncheck the box
                    return;
                }

                const billnum = checkbox.getAttribute('data-bill-number');
                const billcus = checkbox.getAttribute('data-bill-customer');
                const itemcode = checkbox.getAttribute('data-item-code');
                const name = checkbox.getAttribute('data-name');
                const quantity = checkbox.getAttribute('data-quantity');
                const unit = checkbox.getAttribute('data-unit');
                const price = checkbox.getAttribute('data-price');
                const total = checkbox.getAttribute('data-total');

                if (checkbox.checked) {
                    const li = document.createElement('li');
                    li.classList.add('cart-item');
                    li.textContent = `${itemCounter}. ${name} - ฿${price} - ${unit}`; // Add item number
                    li.setAttribute('data-price', price);
                    li.setAttribute('data-unit', unit);
                    cartItems.appendChild(li);
                    itemCounter++; // Increment the counter
                    // Add the selected item to the array
                    selectedItems.push({
                        name,
                        price,
                        unit,
                        billnum,
                        itemcode,
                        quantity,
                        total,
                        billcus
                    });
                } else {
                    cartItems.querySelectorAll('.cart-item').forEach(item => {
                        if (item.textContent.includes(name)) {
                            cartItems.removeChild(item);
                            itemCounter--; // Decrement when unchecked
                            // Re-number the remaining items:
                            cartItems.querySelectorAll('.cart-item').forEach((item, index) => {
                                item.textContent = `${index + 1}. ${item.textContent.substring(item.textContent.indexOf(".") + 2)}`;
                            });
                        }
                    });
                    // Remove the unselected item from the array
                    selectedItems = selectedItems.filter(item => item.name !== name);
                }

                calculateTotal();
            });
        });

        createBillBtn.addEventListener('click', () => {
            // Prepare the summary message
            let summary = '<ul>';
            selectedItems.forEach(item => {
                summary += `<li>${item.name} - ฿${item.price} - ${item.unit}</li>`;
            });
            summary += '</ul>';

            // Show the SweetAlert with the summary
            Swal.fire({
                title: 'ยืนยันการสร้างบิล',
                html: 'คุณแน่ใจหรือไม่ที่ต้องการสร้างบิลดังต่อไปนี้:<br>' + summary,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with form submission
                    const selectedItemsJSON = JSON.stringify(selectedItems);
                    const form = document.createElement('form');
                    form.setAttribute('method', 'POST');
                    form.setAttribute('action', 'function/function_adddelivery.php');
                    const hiddenField = document.createElement('input');
                    hiddenField.setAttribute('type', 'hidden');
                    hiddenField.setAttribute('name', 'selected_items');
                    hiddenField.setAttribute('value', selectedItemsJSON);
                    form.appendChild(hiddenField);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

        function calculateTotal() {
            const cartItems = document.querySelectorAll('#cart-items .cart-item');
            let totalPrice = 0;

            cartItems.forEach(item => {
                const price = parseFloat(item.getAttribute('data-price'));
                totalPrice += price;
            });
            totalPriceElement.textContent = `฿${totalPrice}`;
        }

        document.addEventListener('DOMContentLoaded', calculateTotal);
    </script>



</body>

</html>