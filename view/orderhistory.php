<?php 
require_once('config/connect.php'); 
session_start();

if (!isset($_SESSION['customer_id'])) {
    die("Customer ID is not set.");
}

$customer_id = $_SESSION['customer_id'];

$query = "SELECT 
    TRIM(d.delivery_number) AS delivery_number,
    TRIM(d.delivery_date) AS delivery_date,
    TRIM(d.delivery_status) AS delivery_status,
    TRIM(di.bill_number) AS bill_number,
    TRIM(di.item_desc) AS item_desc
FROM 
    tb_delivery d
JOIN 
    tb_delivery_items di ON d.delivery_id = di.delivery_id
WHERE 
    TRIM(di.bill_customer_id) = '$customer_id' AND d.delivery_status = 5;";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>Order History</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <?php require_once('function/head.php'); ?>
    <style>
        ::-webkit-scrollbar {
            width: 9px;
        }
        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            border-radius: 10px;
        }
    </style>
</head>

<body>
<?php require_once('function/navindex.php'); ?>
<br>
<br>
 <h1 class="app-page-title">    &nbsp;&nbsp;ประวัติการสั่งซื้อ</h1>
    <hr class="mb-4">
    <div class="container">
        <div class="row g-4 settings-section">
            <div class="col-12 col-md-12">
                <div class="app-card app-card-settings shadow-sm p-4">
                    <div class="app-card-body">

                        <div class="table-responsive">
                            <table class="table table-striped" id="Tableall">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;">#</th>
                                        <th scope="col" style="text-align: center;">หมายเลขบิล</th>
                                        <th scope="col" style="text-align: center;">สินค้า</th>
                                        <th scope="col" style="text-align: center;">สถานะ</th>
                                        <th scope="col" style="text-align: center;">วันที่สั่งซื้อ</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php 
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        $i = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $status_text = 'สถานะสินค้าที่ถึงนำส่งให้ลูกค้าสำเร็จ'; // Since delivery_status is 5
                                            echo "<tr>";
                                            echo "<td>" . $i++ . "</td>";
                                            echo "<td>" . htmlspecialchars($row['bill_number']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['item_desc']) . "</td>";
                                            echo "<td>" . $status_text . "</td>";
                                            echo "<td>" . htmlspecialchars($row['delivery_date']) . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='5'>ไม่มีประวัติการสั่งซื้อ</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>