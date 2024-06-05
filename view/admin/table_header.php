<?php require_once("../config/connect.php") ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <title>Manage - User</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">

    <style>
        .table-container {
            max-height: 500px; /* Adjust as needed */
            overflow-y: auto;
        }

        .sortable-column {
            text-decoration: none;
            color: inherit;
            cursor: pointer;
        }

        .sortable-column:hover {
            color: #007bff; /* Optional: Change color on hover */
        }

        ::-webkit-scrollbar {
    width: 12px; /* Adjust width for vertical scrollbar */
}

::-webkit-scrollbar-thumb {
    background-color: #FF5722; /* Color for scrollbar thumb */
    border-radius: 10px; /* Rounded corners for scrollbar thumb */
}

/* Container Styling */


    </style>
</head>

<body>
    <?php require_once('function/sidebar.php'); ?>

    <div class="container">
        <h1 class="app-page-title text-center my-4">ตาราง Header ที่เพิ่มแล้ว</h1>
        <div class="row g-4 settings-section">
            <div class="col-12">
                <div class="app-card app-card-settings shadow-sm p-4">
                    <div class="app-card-body">
                        <!-- Button to trigger modal -->
                        <div class="d-flex justify-content-end mb-3">
                            <button type="button" class="btn btn-primary">
                                <a href="importCSV" style="color:white;">เพิ่มบิล</a>
                            </button>
                        </div>
                        <!-- Table of Users -->
                        <div class="table-container">
                            <table class="table table-striped">
                                <thead >
                                    <tr>
                                        <th class="sorting" scope="col" style="text-align: center;">#</th>
                                        <th scope="col" style="text-align: center;">บิลวันที่</th>
                                        <th scope="col" style="text-align: center;">
                                            <a class="sortable-column" href="?sort=bill_number&order=<?php echo isset($_GET['order']) && $_GET['order'] == 'asc' ? 'desc' : 'asc'; ?>">
                                                หมายเลขบิล
                                            </a>
                                        </th>
                                        <th scope="col" style="text-align: center;">รหัสลูกค้า</th>
                                        <th scope="col" style="text-align: center;">ชื่อลูกค้า</th>
                                        <th scope="col" style="text-align: center;">ยอดรวม</th>
                                        <th scope="col" style="text-align: center;">ยกเลิกบิลหรือไม่</th>
                                        <th scope="col" style="text-align: center;">วันที่สร้าง</th>
                                        <th scope="col" style="text-align: center;">เมนู</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php
                                    $i = 1;
                                    $orderBy = 'bill_number';
                                    $order = 'asc';

                                    if (isset($_GET['sort']) && isset($_GET['order'])) {
                                        $orderBy = $_GET['sort'];
                                        $order = $_GET['order'];
                                    }

                                    $sql = "SELECT * FROM tb_header WHERE bill_status = 1 ORDER BY $orderBy $order";
                                    $query = $conn->query($sql);

                                    foreach ($query as $row) :
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td class="align-middle"><?php echo $row['bill_date'] ?></td>
                                            <td class="align-middle"><?php echo $row['bill_number'] ?></td>
                                            <td class="align-middle"><?php echo $row['bill_customer_id'] ?></td>
                                            <td class="align-middle"><?php echo $row['bill_customer_name'] ?></td>
                                            <td class="align-middle"><?php echo $row['bill_total'] ?></td>
                                            <td class="align-middle"><?php echo $row['bill_isCanceled'] ?></td>
                                            <td class="align-middle"><?php echo $row['create_at'] ?></td>
                                            <td class="align-middle">
                                                <a href="#" class="btn btn-sm btn-danger" onclick="confirmDelete(<?php echo $row['bill_id']; ?>)">Delete</a>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script>
        function confirmDelete(billId) {
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this record!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // Perform deletion
                    window.location.href = "function/action_bill/del_bill.php?bill_id=" + billId;
                }
            });
        }
    </script>

</body>

</html>
