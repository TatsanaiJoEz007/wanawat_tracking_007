<?php 
require_once('../config/connect.php'); 
session_start(); // Start the session

// Function to get user profile picture
function Profilepic($conn, $userId)
{
    $sql = "SELECT user_img
            FROM tb_user
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
}

// Function to convert image data to base64
function base64img($imageData)
{
    return 'data:image/jpeg;base64,' . base64_encode($imageData);
}
?>

<?php 
    $sql = "SELECT * FROM provinces";
    $query = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>Manage - User</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">

    <style>
        /* CSS Styles */
    </style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // ดึงข้อมูลอำเภอเมื่อเลือกจังหวัด
        $('#province').change(function() {
            var province_id = $(this).val();
            $.ajax({
                url: 'function/fetch_amphures.php',
                type: 'post',
                data: {province_id: province_id},
                success: function(response) {
                    $('#amphure').html(response);
                }
            });
        });

        // ดึงข้อมูลตำบลเมื่อเลือกอำเภอ
        $('#amphure').change(function() {
            var amphure_id = $(this).val();
            $.ajax({
                url: 'function/fetch_districts.php',
                type: 'post',
                data: {amphure_id: amphure_id},
                success: function(response) {
                    $('#district').html(response);
                }
            });
        });
    });
</script>


</head>

<body>
    <?php require_once('function/sidebar.php'); ?>

    <h1 class="app-page-title">ตารางข้อมูลผู้ใช้งาน - User</h1>
    <hr class="mb-4">
    <div class="container">
        <div class="row g-4 settings-section">
            <div class="col-12 col-md-12">
                <div class="app-card app-card-settings shadow-sm p-4">
                    <div class="app-card-body">
                        <!-- Button to trigger modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            เพิ่มผู้ใช้งานในระบบ
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">เพิ่มข้อมูล</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        
                                        <form action="#" id="adduserForm" method="post" enctype="multipart/form-data">

                                            
                                            <div class="mb-3">
                                                <label for="adduser-firstname" class="form-label">ชื่อ</label>
                                                <input type="text" class="form-control" id="adduser-firstname" name="adduser-firstname" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="adduser-lastname" class="form-label">นามสกุล</label>
                                                <input type="text" class="form-control" id="adduser-lastname" name="adduser-lastname" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="adduser-email" class="form-label">อีเมล</label>
                                                <input type="email" class="form-control" id="adduser-email" name="adduser-email" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="adduser-password" class="form-label">รหัสผ่าน</label>
                                                <input type="password" class="form-control" id="adduser-password" name="adduser-password" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="adduser-password" class="form-label">ยืนยันรหัสผ่าน</label>
                                                <input type="password" class="form-control" id="adduser-cpassword" name="adduser-cpassword" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="adduser-tel" class="form-label">เบอร์โทรศัพท์</label>
                                                <input type="text" class="form-control" id="adduser-tel" name="adduser-tel" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="adduser-address" class="form-label">ที่อยู่</label>
                                                <input type="text" class="form-control" id="adduser-address" name="adduser-address" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="province" class="form-label">จังหวัด</label>
                                                <select class="form-select" id="province" name="province_id" required>
                                                    <option value="" disabled selected>จังหวัด</option>
                                                    <?php while($result = mysqli_fetch_assoc($query)): ?>
                                                        <option value="<?=$result['id']?>"><?=$result['name_th']?></option>
                                                    <?php endwhile; ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="amphure" class="form-label">อำเภอ</label>
                                                <select class="form-select" id="amphure" name="amphure_id" required>
                                                    <option value="" disabled selected>อำเภอ</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="district" class="form-label">ตำบล</label>
                                                <select class="form-select" id="district" name="district_id" required>
                                                    <option value="" disabled selected>ตำบล</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="user_img" class="form-label">รูปภาพ</label>
                                                <input type="file" class="form-control" id="user_img" name="user_img" required>
                                            </div>    
                                            <div class="mb-3">
                                                <label for="user_status" class="form-label">สถานะ</label>
                                                <select class="form-select" id="user_status" name="user_status" required>
                                                    <option value="1">อยู่ในระบบ</option>
                                                    <option value="0">ไม่อยู่ในระบบ</option>
                                                </select>
                                            </div>
                                        </form>
                                        <?php require_once('function/function_adduser.php'); ?>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                        <button type="submit" form="adduserForm" class="btn btn-primary">บันทึกข้อมูล</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table of Users -->
                        <div class="table-responsive">
                            <table class="table table-striped" id="Tableall">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;">#</th>
                                        <th scope="col" style="text-align: center;">รูปภาพ</th>
                                        <th scope="col" style="text-align: center;">ชื่อ</th>
                                        <th scope="col" style="text-align: center;">นามสกุล</th>
                                        <th scope="col" style="text-align: center;">อีเมล</th>
                                        <th scope="col" style="text-align: center;">เบอร์โทรศัพท์</th>
                                        <th scope="col" style="text-align: center;">สถานะ</th>
                                        <th scope="col" style="text-align: center;">เมนู</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php
                                    // Check if session variable is set
                                    if (isset($_SESSION['user_id'])) {
                                        $i = 1;
                                        $sql = "SELECT * FROM tb_user WHERE user_type = 0";
                                        $query = $conn->query($sql);
                                        while ($row = $query->fetch_assoc()) {
                                            // Get user profile picture
                                            $myprofile = Profilepic($conn, $row['user_id']);
                                            // Set default avatar path
                                            $defaultAvatarPath = '../../view/assets/img/logo/mascot.png';
                                            // Check if user image is set
                                            if (!empty($myprofile['user_img'])) {
                                                $imageBase64 = base64img($myprofile['user_img']);
                                            } else {
                                                $imageBase64 = $defaultAvatarPath;
                                            }
                                    ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td class="align-middle"><img src="<?php echo $imageBase64; ?>" alt="User Image" style="width: 50px; height: 50px;"></td>
                                                <td class="align-middle"><?php echo $row['user_firstname'] ?></td>
                                                <td class="align-middle"><?php echo $row['user_lastname'] ?></td>
                                                <td class="align-middle"><?php echo $row['user_email'] ?></td>
                                                <td class="align-middle"><?php echo $row['user_tel'] ?></td>
                                                <td class="align-middle"><?php echo ($row['user_status'] == 1) ? "อยู่ในระบบ" : "ไม่อยู่ในระบบ"; ?></td>
                                                <td class="align-middle">
                                                    <a href="#" class="btn btn-sm btn-warning">Edit</a>
                                                    <a href="#" class="btn btn-sm btn-secondary">Reset Password</a>
                                                    <a href="#" class="btn btn-sm btn-danger">Delete</a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
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
