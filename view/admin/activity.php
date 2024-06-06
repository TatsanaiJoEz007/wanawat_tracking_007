<?php 
require_once('../config/connect.php'); 

// Determine sorting order
$sort_order = isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'asc' : 'desc';
$new_sort_order = $sort_order == 'asc' ? 'desc' : 'asc';
$icon = $sort_order == 'asc' ? 'fa-sort-up' : 'fa-sort-down';
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>Activity Logs</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
            width: 12px;
            /* Adjust width for vertical scrollbar */
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            /* Color for scrollbar thumb */
            border-radius: 10px;
            /* Rounded corners for scrollbar thumb */
        }

        /* Container Styling */
        .home-section {
            max-height: 100vh;
            /* Adjust height as needed */
            overflow-y: auto;
            /* Allow vertical scroll */
            overflow-x: hidden;
            /* Prevent horizontal scroll */
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        /* Hide underline for clickable header */
        .sortable-column a {
            text-decoration: none;
            color: inherit;
        }
    </style>

</head>

<body>
    <?php require_once('function/sidebar.php'); ?>
    <br>
    <h1 class="app-page-title">&nbsp;<i class="bx bx-user"></i> Admin Activity Log</h1>
    <hr class="mb-4">
    <div class="container">
        <div class="row g-4 settings-section">
            <div class="col-12 col-md-12">
                <div class="app-card app-card-settings shadow-sm p-4">
                    <div class="app-card-body">
                        <!-- Table of Users -->
                        <div class="table-responsive">
                            <table class="table table-striped" id="Tableall">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;">#</th>
                                        <th scope="col" style="text-align: center;">รหัสผู้ใช้</th>
                                        <th scope="col" style="text-align: center;">การกระทำ</th>
                                        <th scope="col" style="text-align: center;">รูปแบบ</th>
                                        <th scope="col" style="text-align: center;">รหัสรูปแบบ</th>
                                        <th scope="col" style="text-align: center;" class="sortable-column">
                                            <a href="?sort=<?php echo $new_sort_order; ?>">กระทำเมื่อ <i class="fas <?php echo $icon; ?>"></i></a>
                                        </th>
                                        <th scope="col" style="text-align: center;">ข้อมูลเพิ่มเติม</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php
                                    $i = 1;
                                    $sql = "SELECT * FROM admin_activity_log ORDER BY create_at $sort_order";
                                    $query = $conn->query($sql);
                                    foreach ($query as $row) :
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td class="align-middle"><?php echo $row['userId'] ?></td>
                                            <td class="align-middle"><?php echo $row['action'] ?></td>
                                            <td class="align-middle"><?php echo $row['entity'] ?></td>
                                            <td class="align-middle"><?php echo $row['entity_id']; ?></td>
                                            <td class="align-middle"><?php echo $row['create_at'] ?></td>
                                            <td class="align-middle"><?php echo $row['additional_info'] ?></td>
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
