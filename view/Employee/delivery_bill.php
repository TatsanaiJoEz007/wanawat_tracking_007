<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DeliveryBill</title>
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
            max-height: 400px; /* กำหนดความสูงคงที่ให้กับตาราง */
        }
        .cart {
            width: 30%;
            background-color: #f9f9f9;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            max-height: 400px; /* กำหนดความสูงคงที่ให้กับ cart */
            overflow: auto; /* กำหนดการเลื่อนภายใน cart */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
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

<?php require_once ('function/sidebar_employee.php'); ?>

    <br><br>
    <div class="container">
        <div class="product-list">
            <h1>สินค้ารอเลือกบิล</h1>
            <div class="product-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>เลขบิล</th>
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
                        $sql = "SELECT * FROM tb_line";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                echo "<tr>
                                    <td>" . $row["line_id"] . "</td>
                                    <td>" . $row["line_bill_number"] . "</td>
                                    <td>" . $row["item_desc"] . "</td>
                                    <td>" . $row["item_quantity"] . "</td>
                                    <td>" . $row["item_unit"] . "</td>
                                    <td>฿" . $row["item_price"] . "</td>
                                    <td>฿" . ((float)$row["line_total"] * (float)$row["item_quantity"]) . "</td>
                                    <td><input type='checkbox' class='product-checkbox' data-name='" . $row["item_desc"] . "' data-price='" . $row["item_price"] . "' data-unit='" . $row["item_unit"] . "'></td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8'>No products found</td></tr>";
                        }
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
            <h7 class="cart-total">ราคารวม: <span id="total-price">฿0</span></h7>
            <button class="create-bill-btn" id="create-bill-btn">สร้างบิล</button>
        </div>
    </div>

    
    <script>
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const cartItems = document.getElementById('cart-items');
        const totalPriceElement = document.getElementById('total-price');
        const createBillBtn = document.getElementById('create-bill-btn');

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const name = checkbox.getAttribute('data-name');
                const price = checkbox.getAttribute('data-price');
                const unit = checkbox.getAttribute('data-unit');

                if (checkbox.checked) {
                    const li = document.createElement('li');
                    li.classList.add('cart-item');
                    li.textContent = `${name}  - ฿${price}- ${unit}`;
                    li.setAttribute('data-price', price);
                    cartItems.appendChild(li);
                } else {
                    const items = cartItems.querySelectorAll('.cart-item');
                    items.forEach(item => {
                        if (item.textContent.includes(name)) {
                            cartItems.removeChild(item);
                        }
                    });
                }
                calculateTotal();
            });
        });

        createBillBtn.addEventListener('click', () => {
    const selectedItems = [];
    const items = cartItems.querySelectorAll('.cart-item');
    items.forEach(item => {
        const name = item.textContent.split(' - ')[0];
        const price = item.getAttribute('data-price');
        selectedItems.push({ name, price });
    });
    const selectedItemsJSON = JSON.stringify(selectedItems);
    const form = document.createElement('form');
    form.setAttribute('method', 'POST');
    form.setAttribute('action', 'statuspage.php');
    const hiddenField = document.createElement('input');
    hiddenField.setAttribute('type', 'hidden');
    hiddenField.setAttribute('name', 'selected_items');
    hiddenField.setAttribute('value', selectedItemsJSON);
    form.appendChild(hiddenField);
    document.body.appendChild(form);
    form.submit();
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

        createBillBtn.addEventListener('click', () => {
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: `คุณต้องการสร้างบิลใช่หรือไม่? ราคารวมทั้งหมดคือ ${totalPriceElement.textContent}`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, สร้างบิล!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire(
                        'สร้างบิลเรียบร้อย!',
                        `บิลของคุณถูกสร้างแล้ว. ราคารวมทั้งหมดคือ ${totalPriceElement.textContent}`,
                        'success'
                    ).then(() => {
                        // รีเซ็ตตะกร้าสินค้า
                        cartItems.innerHTML = '';
                        totalPriceElement.textContent = '฿0';
                        // รีเซ็ต checkbox ทั้งหมด
                        checkboxes.forEach(checkbox => checkbox.checked = false);
                    });
                }
            });
        });
    </script>
</body>
</html>
