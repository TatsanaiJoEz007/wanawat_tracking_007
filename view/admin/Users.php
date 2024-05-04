<?php require_once('../config/connect.php'); ?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>Manage - User</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <!-- เรียกใช้ Bootstrap CSS จาก CDN -->
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
</head>

<body>

    <?php require_once('function/sidebar.php');  ?>

    

    <h1 class="app-page-title"><br>ตารางข้อมูลผู้ใช้งาน</br></h1>

    <hr class="mb-4">
    <div class="container">
        <div class="row g-4 settings-section">

            <div class="col-12 col-md-12">
                <div class="app-card app-card-settings shadow-sm p-4">

                    <div class="app-card-body">
                        <a href ="#"> เพิ่มข้อมูลผู้ดูแลระบบ</a><br><br>
                        <div class="table-responsive">
                            <div style="overflow-x: auto;">
                                <table class="table table-striped" id="Tableall">
                                    <thead>
                                        <tr>
                                            <th scope="col" style="text-align: center;">#</th>
                                            <th scope="col" style="text-align: center;">รูปภาพ</th>
                                            <th scope="col" style="text-align: center;">ชื่อ - นามสกุล</th>
                                            <th scope="col" style="text-align: center;">Email</th>
                                            <th scope="col" style="text-align: center;">รหัสผ่าน</th>
                                            <th scope="col" style="text-align: center;">สถานะ</th>
                                            <th scope="col" style="text-align: center;">เมนู</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                    <?php
                                    $i = 1;
                                    $sql = "SELECT * FROM tb_user WHERE user_type = 999 ";
                                    $query = $conn->query($sql);
                                    foreach($query as $row):
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td class="align-middle"><?php echo $row['user_img']?></td>
                                            <td class="align-middle"><?php echo $row['username']?></td>
                                            <td class="align-middle"><?php echo $row['user_username']?></td>
                                            <td class="align-middle"><?php echo md5($row['user_pass']); ?></td>
                                            <td class="align-middle"><?php if ($row['status']==1){ echo "ยังอยู่ในระบบ";} else{echo "ไม่ได้อยู่ในระบบ"; } ?></td>
                                            <td class="align-middle">
                                                <a href="#" class="btn btn-sm btn-warning">แก้ไข</a>
                                                <a href="#" class="btn btn-sm btn-secondary">รีเซ็ตรหัสผ่าน</a>
                                                <a href="#" class="btn btn-sm btn-danger"> ลบ</a>
                                            </td>
                                        </tr>
                                        <?php endforeach;?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!--//table-responsive-->
                    </div><!--//app-card-body-->

                </div><!--//app-card-->
            </div>
        </div><!--//row-->
    </div><!--//container-->

</body>
<script type="text/javascript">
    let table = new DataTable('#Tableall');
</script>

</html>
