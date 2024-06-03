<!DOCTYPE html>
<html>
<head>
<title>statuspage</title>
<style>
body {
    font-family: sans-serif;
}
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}
h2 {
    text-align: center;
    margin-bottom: 20px;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}
th, td {
    padding: 10px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
th {
    background-color: #f2f2f2;
}
.total {
    text-align: right;
    font-weight: bold;
}
button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    cursor: pointer;
}
button:hover {
    opacity: 0.8;
}
</style>
</head>
<body>
<div class="container">
    <h2>สรุปสินค้าที่เลือก</h2>

    <table>
        <thead>
            <tr>
                <th>รหัสสินค้า</th>
                <th>ชื่อสินค้า</th>
                <th>จำนวน</th>
                <th>ราคาต่อหน่วย</th>
                <th>ราคารวม</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($selectedItems as $item): ?>
                <tr>
                    <td><?= $item['code'] ?></td>
                    <td><?= $item['name'] ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= $item['price'] ?></td>
                    <td><?= $item['total'] ?></td>
                </tr>
                <?php $totalAmount += $item['total']; ?>
            <?php endforeach; ?>
        </tbody>
    </table>

    <p class="total">ยอดรวมทั้งหมด: <?= $totalAmount ?> บาท</p>

    <button onclick="history.back()">แก้ไขรายการที่เลือก</button>
    <button onclick="window.location.href='confirm_order.php'">ยืนยันบิล</button>
</div>
</body>
</html>
