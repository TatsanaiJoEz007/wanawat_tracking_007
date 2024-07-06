<?php
require_once('../config/connect.php'); 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'];

// Fetch distinct delivery dates
$sql = "SELECT DISTINCT DATE(delivery_date) as delivery_date FROM tb_delivery ORDER BY delivery_date DESC";
$result = $conn->query($sql);

$dates = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $dates[] = $row["delivery_date"];
    }
}
$dates_json = json_encode($dates);

$selected_date = null;
$data = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $selected_date = $_POST['selected_date'];
    // Fetch data based on the selected date
    $sql = "SELECT * FROM tb_delivery WHERE DATE(delivery_date) = ? AND created_by = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("si", $selected_date, $user_id);
        $stmt->execute();
        $data = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        error_log("Failed to prepare statement: " . $conn->error);
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูข้อมูลย้อนหลัง</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="style/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/css/datepicker3.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.3.0/js/locales/bootstrap-datepicker.th.min.js"></script>
    <style>
        .datepicker table tr td.enabled-date {
            background-color: #dff0d8;
            color: #3c763d;
            cursor: pointer;
        }
        .datepicker table tr td.disabled-date {
            background-color: #f2dede;
            color: #a94442;
            cursor: not-allowed;
        }
        .status-red {
            background-color: #ffcccc;
        }
        .status-green {
            background-color: #ccffcc;
        }
        .status-yellow {
            background-color: #ffffcc;
        }
        .status-blue {
            background-color: #cce5ff;
        }
        .status-purple {
            background-color: #dfe2fb;
        }
        .status-grey {
            background-color: #f0f2f5;
        }
    </style>
</head>
<body>
    <?php require_once('function/sidebar_employee.php'); ?>
    <div class="container">
        <h1>ดูข้อมูลย้อนหลัง</h1>
        <div class="form-container">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="datepicker"><strong>เลือกวันที่:</strong></label>
                    <div class="input-group date">
                        <input id="datepicker" name="selected_date" class="form-control datepicker" placeholder="เลือกวันที่" autocomplete="off">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="fa fa-calendar"></i>
                            </span>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">ดูข้อมูล</button>
            </form>
        </div>
        <?php if ($data): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>เลขบิล</th>
                            <th>สถานะ</th>
                            <th>วันที่สร้างบิล</th>
                            <th>ประเภทการขนย้าย</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $index => $row): ?>
                            <?php
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
                                    $status_text = 'สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลาย';
                                    $status_class = 'status-grey';
                                    break;
                                case 4:
                                    $status_text = 'สถานะสินค้าที่กำลังนำส่งให้ลูกค้า';
                                    $status_class = 'status-purple';
                                    break;
                                case 5:
                                    $status_text = 'สถานะสินค้าที่ถึงนำส่งให้ลูกค้าสำเร็จ';
                                    $status_class = 'status-green';
                                    break;
                                case 99:
                                    $status_text = 'สถานะสินค้าที่เกิดปัญหา';
                                    $status_class = 'status-red';
                                    break;
                                default:
                                    $status_text = 'Unknown';
                                    $status_class = '';
                                    break;
                            }
                            ?>
                            <tr class="<?php echo $status_class; ?>">
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($row['delivery_number']); ?></td>
                                <td><?php echo $status_text; ?></td>
                                <td><?php echo htmlspecialchars($row['delivery_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_by']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <h5 class="mt-4">ไม่มีข้อมูลสำหรับวันที่: <?php echo htmlspecialchars($selected_date); ?></h5>
        <?php endif; ?>
    </div>

    <script type="text/javascript">
        jQuery(document).ready(function($){
            var enabledDates = <?php echo $dates_json; ?>;

            jQuery.fn.datepicker.defaults.language = 'th';
            jQuery('#datepicker').datepicker({
                autoclose: true,
                todayHighlight: true,
                format: 'yyyy-mm-dd',
                beforeShowDay: function(date){
                    var d = date.getFullYear() + "-" + ('0' + (date.getMonth()+1)).slice(-2) + "-" + ('0' + date.getDate()).slice(-2);
                    if (enabledDates.includes(d)) {
                        return {classes: 'enabled-date'};
                    } else {
                        return {classes: 'disabled-date'};
                    }
                }
            }).datepicker('update', new Date());
        });
    </script>
</body>
</html>