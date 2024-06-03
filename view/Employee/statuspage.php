<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #ff4d4d;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f9f9f9;
            color: #333;
        }

        .total {
            text-align: right;
            font-weight: bold;
            font-size: 18px;
            color: #28a745;
        }

        .card {
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }

        .card-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .card-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Confirmation Page</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Invoice Number</th>
                    <th>Product Code</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Price</th>
                    <th>Total Price</th>
                </tr>
            </thead>
            <tbody>
                <!-- Insert selected items from delivery_bill here -->
            </tbody>
        </table>
        <p class="total">Total Amount: <span id="totalAmount">0</span> THB</p>
        <div class="card">
            <h4>Confirm Your Selection</h4>
            <button class="card-button" id="confirmButton">Confirm Bill</button>
        </div>
    </div>

    <script>
        // JavaScript to calculate total and handle confirmation
        document.addEventListener('DOMContentLoaded', function() {
    const totalAmountElement = document.getElementById('totalAmount');
    const confirmButton = document.getElementById('confirmButton');

    let totalAmount = 0;

    // คำนวณยอดรวมเมื่อหน้าเว็บโหลดเสร็จ
    const calculateTotal = () => {
        const rows = document.querySelectorAll('tbody tr');
        totalAmount = 0;
        rows.forEach(row => {
            const price = parseFloat(row.children[5].textContent.replace('฿', ''));
            totalAmount += price;
        });
        totalAmountElement.textContent = totalAmount.toFixed(2);
    };

    calculateTotal();

    // การคลิกปุ่มยืนยันบิล
    confirmButton.addEventListener('click', function() {
        if (totalAmount > 0) {
            // ส่วนนี้คุณสามารถเพิ่มโค้ดสำหรับการยืนยันบิลได้ตามต้องการ
            // ในที่นี้เราจะแสดง Alert เพื่อแสดงว่าบิลถูกยืนยันเรียบร้อยแล้ว
            alert(`ยืนยันบิลเรียบร้อย! ยอดรวมทั้งหมดคือ ${totalAmount.toFixed(2)} บาท`);
        } else {
            alert('กรุณาเลือกสินค้าก่อนยืนยันบิล');
        }
    });
});


    </script>
</body>
</html>
