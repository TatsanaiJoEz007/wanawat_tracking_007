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
    TRIM(d.delivery_id) AS delivery_id,
    TRIM(di.bill_number) AS bill_number,
    TRIM(di.item_desc) AS item_desc
FROM 
    tb_delivery d
JOIN 
    tb_delivery_items di ON d.delivery_id = di.delivery_id
WHERE 
    TRIM(di.bill_customer_id) = '$customer_id' AND d.delivery_status IN (1, 2, 3, 4, 99);";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>On Delivery</title>
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
        .status-blue {
            background-color: #cce5ff;
        }
        .status-yellow {
            background-color: #ffffcc;
        }
        .status-grey {
            background-color: #f0f2f5;
        }
        .status-purple {
            background-color: #dfe2fb;
        }
        .status-red {
            background-color: #ffcccc;
        }
    </style>
</head>

<body>
<?php require_once('function/navindex.php'); ?>
<br>
<br>
 <h1 class="app-page-title">    &nbsp;&nbsp;กำลังจัดส่ง</h1>
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
                                        <th scope="col" style="text-align: center;">ติดตาม</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php 
                                    if ($result && mysqli_num_rows($result) > 0) {
                                        $i = 1;
                                        while ($row = mysqli_fetch_assoc($result)) {
                                            $status_text = '';
                                            $status_class = '';
                                            switch ($row['delivery_status']) {
                                                case 1:
                                                    $status_text = 'สถานะสินค้าที่คำสั่งซื้อเข้าสู่ระบบ';
                                                    $status_class = 'status-blue';
                                                    break;
                                                case 2:
                                                    $status_text = 'สถานะสินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า';
                                                    $status_class = 'status-yellow';
                                                    break;
                                                case 3:
                                                    $status_text = 'สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลายทาง';
                                                    $status_class = 'status-grey';
                                                    break;
                                                case 4:
                                                    $status_text = 'สถานะสินค้าที่กำลังนำส่งให้ลูกค้า';
                                                    $status_class = 'status-purple';
                                                    break;
                                                case 99:
                                                    $status_text = 'สถานะสินค้าที่เกิดปัญหา';
                                                    $status_class = 'status-red';
                                                    break;
                                                default:
                                                    $status_text = 'Unknown';
                                                    break;
                                            }

                                            echo "<tr class='{$status_class}'>";
                                            echo "<td>" . $i++ . "</td>";
                                            echo "<td>" . htmlspecialchars($row['bill_number']) . "</td>";
                                            echo "<td>" . htmlspecialchars($row['item_desc']) . "</td>";
                                            echo "<td>" . $status_text . "</td>";
                                            echo "<td>" . htmlspecialchars($row['delivery_date']) . "</td>";
                                            echo "<td><a href='tracking_mainpage.php?trackingId=" . htmlspecialchars($row['delivery_number']) . "' class='btn btn-primary'>ติดตาม</a></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>ไม่มีประวัติการสั่งซื้อ</td></tr>";
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