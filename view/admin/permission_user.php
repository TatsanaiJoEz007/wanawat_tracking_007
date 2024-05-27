<?php 
require_once('../config/connect.php'); 
session_start(); // Start the session

// Function to get user profile picture
function Profilepic($conn, $userId)
{
    $sql = "SELECT user_img FROM tb_user WHERE user_id = ?";
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
    // Fetch districts when province is selected
    $('#province').change(function() {
        var province_id = $(this).val();
        $.ajax({
            url: 'function/fetch_amphures.php',
            type: 'post',
            data: { province_id: province_id },
            success: function(response) {
                $('#amphure').html(response);
            }
        });
    });

    // Fetch sub-districts when district is selected
    $('#amphure').change(function() {
        var amphure_id = $(this).val();
        $.ajax({
            url: 'function/fetch_districts.php',
            type: 'post',
            data: { amphure_id: amphure_id },
            success: function(response) {
                $('#district').html(response);
            }
        });
    });

    // Open modal and populate with data for editing
    $(document).on('click', '.edit-btn', function() {
        var user_id = $(this).data('id');
        console.log("Edit button clicked, user_id: ", user_id);

        $.ajax({
            url: 'function/action_edituser/get_user_data.php',
            type: 'post',
            data: { user_id: user_id },
            success: function(response) {
                console.log("Raw server response:", response); // Log the raw response

                try {
                    var user = JSON.parse(response);
                    console.log("Parsed user data:", user);
                    
                    if (user.error) {
                        console.error("Server error:", user.error);
                        return;
                    }

                    $('#adduserModalLabel').text('แก้ไขข้อมูล');
                    $('#adduserForm').attr('action', 'function/action_edituser/edit_user.php');
                    $('#user_id').val(user.user_id);
                    $('#adduser-firstname').val(user.user_firstname);
                    $('#adduser-lastname').val(user.user_lastname);
                    $('#adduser-email').val(user.user_email);
                    $('#adduser-tel').val(user.user_tel);
                    $('#adduser-address').val(user.user_address);
                    $('#province').val(user.province_id).change();
                    $('#amphure').val(user.amphure_id).change();
                    $('#district').val(user.district_id).change();
                    $('#user_status').val(user.user_status);
                    $('#user_img').removeAttr('required');
                    $('#adduserModal').modal('show');
                } catch (e) {
                    console.error("Error parsing JSON:", e);
                    console.log("Response from server:", response);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", status, error);
                console.log("Response from server:", xhr.responseText);
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
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adduserModal">
                            เพิ่มผู้ใช้งานในระบบ
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="adduserModal" tabindex="-1" aria-labelledby="adduserModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="adduserModalLabel">เพิ่มข้อมูล</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="#" id="adduserForm" method="post" enctype="multipart/form-data">
                                            <input type="hidden" id="user_id" name="user_id">
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
                                                <input type="file" class="form-control" id="user_img" name="user_img">
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
                                                    <a href="#" class="btn btn-sm btn-warning edit-btn" data-id="<?php echo $row['user_id']; ?>">Edit</a>
                                                    <a href="#" class="btn btn-sm btn-secondary">Reset Password</a>
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="delUser('<?php echo $row['user_id']; ?>')">Delete</button>
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

    <script>
    
    function delUser(id) {
            let option = {
                url: 'function/action_edituser/del_user.php',
                type: 'post',
                data: {
                    id: id,
                    delUser: 1
                },
                success: function (res) {
                    // Display success message using Swal.fire
                    Swal.fire({
                        title: 'Success!',
                        text: 'ลบผู้ใช้งานสำเร็จ',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // Reload the page after successful deletion
                        }
                    });
                },
                error: function (xhr, status, error) {
                    // Display error message using Swal.fire
                    Swal.fire({
                        title: 'Error!',
                        text: 'เกิดข้อผิดพลาดในการลบผู้ใช้งาน',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                    console.error(xhr.responseText); // Log the error response for debugging
                }
            };

            // Show confirmation dialog before proceeding with the deletion
            Swal.fire({
                title: 'ต้องการลบข้อมูลใช่ไหม?',
                text: "",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with AJAX request to delete the banner
                    $.ajax(option);

                }
            });
        }

    </script>

    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
