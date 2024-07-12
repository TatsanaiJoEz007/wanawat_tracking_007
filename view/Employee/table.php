<!DOCTYPE html>
<?php session_start(); ?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Bill</title>
    <!-- //mockup stuff fix it later broooo -->
    <link rel="stylesheet" href="function/table/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php
    require_once('../../view/config/connect.php');
    ?>

    <?php require_once('function/sidebar_employee.php'); ?>

    <br><br>


    <div class="container">
        <div class="product-list">
            <h1>บิลที่เพิ่มแล้ว</h1>
            <p style="color:red;">รายการสินค้าที่เพิ่มแล้วจะโชว์ก็ต่อเมื่อเพิ่ม Line และ Header ที่ตรงกันเรียบร้อยแล้ว</p>
            <div class="product-table-container">
                <?php require_once "function/table/fetch_table.php" ?>
                <table class="zebra-striped">
                    <thead>
                        <tr>
                            <th>เลขบิล</th>
                            <th>ชื่อลูกค้า</th>
                            <th>น้ำหนัก (กิโลกรัม)</th>
                            <th>จำนวน</th>
                            <th>รหัสสินค้า</th>
                            <th>รายละเอียด</th>
                            <th>จำนวน</th>
                            <th>หน่วย</th>
                            <th>ราคา</th>
                            <th>ราคารวม</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php require_once "function/table/deliverybill.php" ?>
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <?php if ($current_page > 1) : ?>
                    <a href="?page=<?php echo ($current_page - 1); ?>" class="btn-custom">&laquo; ก่อนหน้า</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo ($i == $current_page) ? 'active' : ''; ?>" class="btn-custom"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages) : ?>
                    <a href="?page=<?php echo ($current_page + 1); ?>" class="btn-custom">ถัดไป &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
</body>

</html>