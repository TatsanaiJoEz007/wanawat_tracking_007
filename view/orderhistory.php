<?php 
require_once('config/connect.php'); 
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($_SESSION['customer_id'])) {
    die("Customer ID is not set.");
}

$customer_id = $_SESSION['customer_id'];

$query = "SELECT 
            tb_line.line_bill_number, 
            tb_line.item_desc, 
            tb_delivery.delivery_status, 
            tb_header.bill_date
          FROM 
            tb_line 
          JOIN 
            tb_delivery ON tb_line.header_id = tb_delivery.bill_id 
          WHERE 
            tb_header.bill_customer_id = '$customer_id'";

$result = mysqli_query($conn, $query);

if(!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>Order history</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <?php require_once('function/head.php'); ?>
    <style>
        .modal-dialog {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0 auto !important;
        }
        .modal {
            position: fixed;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            width: auto !important;
        }
        .modal-content {
            margin: auto !important;
        }
        .modal-backdrop.show {
            position: fixed;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
        }
        ::-webkit-scrollbar {
            width: 9px;
        }
        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            border-radius: 10px;
        }
    </style>
</head>

<?php require_once('function/navindex.php'); ?>
<body>
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
                                            echo "<tr>";
                                            echo "<td>" . $i++ . "</td>";
                                            echo "<td>" . $row['bill_number'] . "</td>";
                                            echo "<td>" . $row['item_desc'] . "</td>";
                                            echo "<td>" . $row['delivery_status'] . "</td>";
                                            echo "<td>" . $row['bill_date'] . "</td>";
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
