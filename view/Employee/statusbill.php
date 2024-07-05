<?php
        require_once('../../view/config/connect.php');

        $user_id = $_SESSION['user_id'];
    ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parcel Sending System</title>
    <!-- Ensure you have jQuery and Bootstrap included -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Include DataTables CSS and JS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <link rel="stylesheet" href="function/statusbill/css/style.css">
</head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>
    <div class="container">
        <button id="updateSelectedBtn" class="btn-custom" style="display:none;">อัพเดทสถานะที่เลือก</button>
        <!-- Search bar -->
        <div class="search-bar">
            <h2>สถานะการขนส่ง</h2>
            <?php require_once "function/instruction.php" ?>

            <form method="GET" action="">
                <input class="insearch" type="text" name="search" placeholder="Search by delivery number" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="search">Search</button>
            </form>
        </div>
        <?php require_once "function/statusbill/searchterm.php" ?>

        <div id="action-buttons" style="display: none;">
            <button class="btn-custom" id="manageAllBtn">Manage</button>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', (event) => {
                const checkboxes = document.querySelectorAll('input[name="select"]');
                const actionButtons = document.getElementById('action-buttons');

                checkboxes.forEach((checkbox) => {
                    checkbox.addEventListener('change', () => {
                        const anyChecked = Array.from(checkboxes).some(chk => chk.checked);
                        actionButtons.style.display = anyChecked ? 'block' : 'none';
                    });
                });
            });

            function handleSelectedItems() {
                const selectedItems = [];
                const checkboxes = document.querySelectorAll('input[name="select"]:checked');

                checkboxes.forEach((checkbox) => {
                    selectedItems.push(checkbox.value);
                });

                // ตรวจสอบค่า selectedItems ว่ามีค่าหรือไม่
                if (selectedItems.length === 0) {
                    alert('กรุณาเลือกการจัดส่งอย่างน้อยหนึ่งรายการ');
                    return;
                }

                // เรียกใช้งาน openModal2 หลังจากตรวจสอบค่า selectedItems แล้ว
                openModal('Bulk Update', selectedItems.join(','));
            }
        </script>


        <div class="table-container">
            <table id="myTable">
                <thead>
                    <tr>
                        <th>เลือก</th>
                        <th>#</th>
                        <th>เลขบิล</th>
                        <th>จำนวน</th>
                        <th>สถานะ</th>
                        <th>วันที่สร้างบิล</th>
                        <th>ประเภทการขนย้าย</th>
                        <!-- <th>จัดการสถานะ</th> -->
                </thead>
                <tbody>
                    <?php require_once "function/statusbill/fetchdelivery.php" ?>
                </tbody>
            </table>
        </div>

        <script>
            function toggleNewButton(checkboxId, buttonId) {
                var checkbox = document.getElementById(checkboxId);
                var button = document.getElementById(buttonId);
                if (checkbox.checked) {
                    button.style.display = 'inline-block';
                } else {
                    button.style.display = 'none';
                }
            }

            function newButtonAction(deliveryId) {
                // Define the action for the new button here
                alert('New button clicked for delivery ID: ' + deliveryId);
            }
        </script>


        <div class="pagination">
            <?php if ($current_page > 1) : ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" class="btn-custom">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="btn-custom <?php echo ($i == $current_page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages) : ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" class="btn-custom">Next &raquo;</a>
            <?php endif; ?>
        </div>

    </div>

    <!-- Include SweetAlert for modal notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

    <!-- Modal section -->
    <div class="modal" id="manageModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Status</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Modal body content -->
                </div>
                <div class="modal-footer">
                    <button id="updateStatusBtn" class="btn-custom">อัพเดทสถานะการจัดส่งสินค้า</button> 
                    <p>&nbsp;</p> 
                    <p>&nbsp;</p> 
                    <p>&nbsp;</p>
                    <button id="reportProblemBtn" class="btn-custom btn-red">แจ้งว่าสินค้ามีปัญหา</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- Include Bootstrap JS -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Include DataTables JS -->

    <script src="function/statusbill/js/modal.js"></script>

    <script src="function/statusbill/js/updatestatusbtn.js"></script>

    <script src="function/statusbill/js/reportstatusbtn.js"></script>
</body>

</html>