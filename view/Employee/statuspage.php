<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StatusBill</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        .container {
            max-width: 1500px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .bill-summary {
            width: 60%;
            padding: 20px;
            box-sizing: border-box;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .cart {
            width: 30%;
            padding: 20px;
            box-sizing: border-box;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 20px;
            margin-bottom: 10px;
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

        button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
            align-self: center;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
<?php require_once('function/sidebar_employee.php'); ?>
    <div class="container">
        <div class="bill-summary">
            <h1>สรุปบิล</h1>
            <div>
                <h2>รายการสินค้าที่เลือก</h2>
                <table>
                    <thead>
                        <tr>
                            <th>สินค้า</th>
                            <th>ราคา</th>
                            <th>หน่วย</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_POST['selected_items'])) {
                            $selectedItems = json_decode($_POST['selected_items'], true);
                            foreach ($selectedItems as $item) {
                                echo "<tr>
                                        <td>{$item['name']}</td>
                                        <td>{$item['price']}</td>";
                                // Check if 'unit' index is set before accessing it
                                if (isset($item['unit'])) {
                                    echo "<td>{$item['unit']}</td>";
                                } else {
                                    echo "<td></td>"; // or provide a default value
                                }
                                echo "</tr>";
                            }                            
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="cart">
            <h2>ราคารวม</h2>
            <ul class="cart-items" id="cart-items">
                <?php
                if (isset($_POST['selected_items'])) {
                    $selectedItems = json_decode($_POST['selected_items'], true);
                    foreach ($selectedItems as $item) {
                        echo "<li data-price='{$item['price']}'>{$item['name']} - ฿{$item['price']}</li>";
                    }
                }
                ?>
            </ul>
            <br>
            <hr>
            <br>
            <h7>ราคารวม: <span id="total-price">฿0</span></h7>
                <form method="POST">
                    <input type="hidden" name="selected_items" value="<?php echo htmlentities($_POST['selected_items']); ?>">
                    <button type="submit">ยืนยันบิล</button>
                </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            calculateTotal();
        });

        function calculateTotal() {
            const cartItems = document.querySelectorAll('.cart-items li');
            let totalPrice = 0;

            cartItems.forEach(item => {
                const price = parseFloat(item.getAttribute('data-price'));
                totalPrice += price;
            });

            document.getElementById('total-price').textContent = `฿${totalPrice.toFixed(2)}`;
        }
    </script>
</body>

</html>
