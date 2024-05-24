<?php require_once ('../config/connect.php'); ?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>Manage - User</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">

    <style>
        /* ปรับแต่ง modal ให้อยู่ตรงกลางจอ */
        .modal-dialog {
            display: flex;
            justify-content: center;
            /* จัดกลางแนวนอน */
            align-items: center;
            /* จัดกลางแนวตั้ง */
            min-height: 100vh;
            /* ตั้งค่าความสูงขั้นต่ำของ modal dialog */
            margin: 0 auto !important;
            /* ใช้ margin auto และ !important เพื่อให้การจัดกลางแน่นอน */
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
            /* จัดกลาง modal-content ใน modal-dialog */
        }

        .modal-backdrop.show {
            position: fixed;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
        }

        /* Responsive table container */
        .table-responsive {
            max-height: 80vh;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <?php require_once ('function/sidebar.php'); ?>

    <div class="container">
        <h1 class="app-page-title text-center my-4">ตารางบิลที่เพิ่มแล้ว</h1>
        <div class="row g-4 settings-section">
            <div class="col-12">
                <div class="app-card app-card-settings shadow-sm p-4">
                    <div class="app-card-body">
                        <!-- Button to trigger modal -->
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary">
                                <a href="importCSV.php" style="color:white;">เพิ่มบิล</a>
                            </button>
                        </div>
                        <!-- Table of Users -->
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;">#</th>
                                        <th scope="col" style="text-align: center;">บิลวันที่</th>
                                        <th scope="col" style="text-align: center;">หมายเลขบิล</th>
                                        <th scope="col" style="text-align: center;">รหัสลูกค้า</th>
                                        <th scope="col" style="text-align: center;">ชื่อลูกค้า</th>
                                        <th scope="col" style="text-align: center;">ยอดรวม</th>
                                        <th scope="col" style="text-align: center;">ยกเลิกบิล</th>
                                        <th scope="col" style="text-align: center;">วันที่สร้าง</th>
                                        <th scope="col" style="text-align: center;">เมนู</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php
                                    $i = 1;
                                    $sql = "SELECT * FROM tb_bill";
                                    $query = $conn->query($sql);
                                    foreach ($query as $row):
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td class="align-middle"><?php echo $row['bill_date'] ?></td>
                                            <td class="align-middle"><?php echo $row['bill_number'] ?></td>
                                            <td class="align-middle"><?php echo $row['bill_customer_id'] ?></td>
                                            <td class="align-middle"><?php echo $row['bill_customer_name'] ?></td>
                                            <td class="align-middle"><?php echo $row['bill_total'] ?></td>
                                            <td class="align-middle"><?php echo $row['bill_isCanceled'] ?></td>
                                            <td class="align-middle"><?php echo $row['bill_create_at'] ?></td>
                                            <td class="align-middle">
                                                <a href="#" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
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
