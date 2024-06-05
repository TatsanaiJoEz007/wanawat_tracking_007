
<?php require_once ('config/connect.php'); ?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>Oderhistory</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <?php require_once('function/head.php'); ?>
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

     
        ::-webkit-scrollbar {
    width: 9px; /* Adjust width for vertical scrollbar */
}

::-webkit-scrollbar-thumb {
    background-color: #FF5722; /* Color for scrollbar thumb */
    border-radius: 10px; /* Rounded corners for scrollbar thumb */
}

    
    </style>
</head>


<?php require_once('function/navindex.php'); ?>
<body>

    <h1 class="app-page-title">ประวัติการสั่งซื้อ</h1>
    <hr class="mb-4">
    <div class="container">
        <div class="row g-4 settings-section">
            <div class="col-12 col-md-12">
                <div class="app-card app-card-settings shadow-sm p-4">
                    <div class="app-card-body">


                        <!-- Table of Order History -->
                        <div class="table-responsive">
                            <table class="table table-striped" id="Tableall">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;">เลขคำสั่งซื้อ</th>
                                        <th scope="col" style="text-align: center;">สินค้า</th>
                                        <th scope="col" style="text-align: center;">สถานะ</th>
                                        <th scope="col" style="text-align: center;">วันที่สั่งซื้อ</th>
                                        <th scope="col" style="text-align: center;">จัดการ</th>

                                        
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php
                                    $i = 1;
                                    $sql = "SELECT * FROM tb_user WHERE user_type = 999 ";
                                    $query = $conn->query($sql);
                                    foreach ($query as $row):
                                        ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td class="align-middle"><img src="<?php echo $row['user_img']; ?>"
                                                    alt="User Image" style="width: 50px; height: auto;"></td>
                                            <td class="align-middle"><?php echo $row['user_firstname'] ?></td>
                                            <td class="align-middle"><?php echo $row['user_lastname'] ?></td>
                                            <td class="align-middle"><?php echo $row['user_email'] ?></td>

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

