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
                                        <h5 class="modal-title" id="adduserModalLabel">แก้ไขข้อมูล</h5>
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
                                                <input type="password" class="form-control" id="adduser-cpassword" name="adduser-cpassword" optional>
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
                                                    <?php while ($result = mysqli_fetch_assoc($query)) : ?>
                                                        <option value="<?= $result['id'] ?>"><?= $result['name_th'] ?></option>
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
                                        <th scope="col" style="text-align: center;">รหัสลูกค้า</th>
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
                                                <td class="align-middle"><?php echo $row['customer_id'] ?></td>
                                                <td class="align-middle"><?php echo $row['user_email'] ?></td>
                                                <td class="align-middle"><?php echo $row['user_tel'] ?></td>
                                                <td class="align-middle"><?php echo ($row['user_status'] == 1) ? "อยู่ในระบบ" : "ไม่อยู่ในระบบ"; ?></td>
                                                <td class="align-middle">
                                                    <a href="#" class="btn btn-warning btn-sm editCustomerID" data-user-id="<?php echo $row['user_id']; ?>"><i class="fa fa-edit"></i> เพิ่ม Customer ID</a>
                                                    <button type="button" class="btn btn-sm btn-secondary reset-password-btn" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-id="<?php echo $row['user_id']; ?>">Reset Password</button>
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
        $(document).ready(function() {
            window.fetchAndPopulateUserData = function(userId) {
                $.ajax({
                    url: 'function/action_edituser/get_user_data.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        user_id: userId
                    },
                    success: function(response) {
                        $('#user_id').val(response.user_id);
                        $('#edituser-firstname').val(response.user_firstname);
                        $('#edituser-lastname').val(response.user_lastname);
                        $('#edituser-email').val(response.user_email);
                        $('#edituser-tel').val(response.user_tel);
                        $('#edituser-address').val(response.user_address);
                        $('#edit_province').val(response.province.id);
                        $('#edit_province').change();
                        // After amphures are loaded, set the user's amphure and fetch districts
                        $.ajax({
                            url: 'function/fetch_amphures.php',
                            type: 'post',
                            data: {
                                province_id: response.province.id
                            },
                            success: function(amphureResponse) {
                                $('#edit_amphure').html(amphureResponse);
                                $('#edit_amphure').val(response.amphure.id).change(); // Trigger change to fetch districts

                                $.ajax({
                                    url: 'function/fetch_districts.php',
                                    type: 'post',
                                    data: {
                                        amphure_id: response.amphure.id
                                    },
                                    success: function(districtResponse) {
                                        $('#edit_district').html(districtResponse);
                                        $('#edit_district').val(response.district.id);
                                    }
                                });
                            }
                        });


                        $('#user_status').val(response.user_status);
                        $('#profile_picture').attr('src', response.user_img); // Assuming you have an image element with id "profile_picture"

                        $('#edituserModal').modal('show');
                        console.log(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching user data:', error); // Log the error for debugging
                    }
                });
            }; // End of fetchAndPopulateUserData function

            // Attach the click event handler once fetchAndPopulateUserData is defined
            $('.edit-btn').click(function() {
                var userId = $(this).data('id');
                window.fetchAndPopulateUserData(userId); // Call using window.
            });
        });


        function handleProvinceChange(provinceSelectId, amphureSelectId, districtSelectId) {
            var selectedProvinceId = $("#" + provinceSelectId).val();
            console.log("Selected Province ID:", selectedProvinceId);

            // Clear amphure and district dropdowns before fetching new data
            $("#" + amphureSelectId).html('<option value="" disabled selected>เลือกอำเภอ</option>');
            $("#" + districtSelectId).html('<option value="" disabled selected>เลือกตำบล</option>');

            if (selectedProvinceId && selectedProvinceId != "") { // Check for valid province ID
                $.ajax({
                    url: 'function/fetch_amphures.php',
                    type: 'post',
                    data: {
                        province_id: selectedProvinceId
                    },
                    success: function(response) {
                        $("#" + amphureSelectId).html(response);
                        console.log("Amphures loaded:", response);
                        // If it's the edit modal, trigger amphure change to load districts
                        if (amphureSelectId === 'edit_amphure' && response.trim() != "") {
                            $("#edit_amphure").trigger('change');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching amphures:", error);
                    }
                });
            }
        }

        // Attach the change event handler to the edit province dropdown
        $("#edit_province").change(function() {
            var provinceId = $(this).val();
            var targetAmphureId = 'edit_amphure';
            var targetDistrictId = 'edit_district';
            handleProvinceChange("edit_province", targetAmphureId, targetDistrictId);
        });

        function handleAmphureChange(amphureSelectId, districtSelectId) {
            var selectedAmphureId = $("#" + amphureSelectId).val();
            console.log("Selected Amphure ID:", selectedAmphureId);

            // Clear district dropdown before fetching new data
            $("#" + districtSelectId).html('<option value="" disabled selected>เลือกตำบล</option>');

            if (selectedAmphureId && selectedAmphureId != "") { // Check for valid amphure ID
                $.ajax({
                    url: 'function/fetch_districts.php',
                    type: 'post',
                    data: {
                        amphure_id: selectedAmphureId
                    },
                    success: function(response) {
                        $("#" + districtSelectId).html(response);
                        console.log("Districts loaded:", response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching districts:", error);
                    }
                });
            }
        }

        // Attach change event handlers to province dropdowns
        $("#province").change(function() {
            handleProvinceChange("province", "amphure", "district");
        });
        $("#edit_province").change(function() {
            handleProvinceChange("edit_province", "edit_amphure", "edit_district");
        });

        // Attach change event handlers to amphure dropdowns
        $("#amphure").change(function() {
            handleAmphureChange("amphure", "district");
        });
        $("#edit_amphure").change(function() {
            handleAmphureChange("edit_amphure", "edit_district");
        });

        $('#adduserModal, #edituserModal').on('hidden.bs.modal', function() {
            $('#amphure, #district, #edit_amphure, #edit_district').html('<option value="" disabled selected>อำเภอ/ตำบล</option>');
        });

        // Combined change event handlers for both modals
        $('#province, #edit_province').change(function() {
            var provinceId = $(this).val();
            var targetAmphureId = $(this).attr('id') === 'province' ? 'amphure' : 'edit_amphure';
            $.ajax({
                url: 'function/fetch_amphures.php',
                type: 'post',
                data: {
                    province_id: provinceId
                },
                success: function(response) {
                    console.log("Response from fetch_amphures.php:", response); // Check the response
                    $('#' + targetAmphureId).html(response);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching amphures:", error);
                }
            });
        });

        $('#amphure, #edit_amphure').change(function() {
            var amphureId = $(this).val();
            var targetDistrictId = $(this).attr('id') === 'amphure' ? 'district' : 'edit_district';
            $.ajax({
                url: 'function/fetch_districts.php',
                type: 'post',
                data: {
                    amphure_id: amphureId
                },
                success: function(response) {
                    $('#' + targetDistrictId).html(response);
                }
            });
        });
    </script>

    <!-- Modal for Editing Customer ID -->
    <div class="modal fade" id="editCustomerIDModal" tabindex="-1" aria-labelledby="editCustomerIDModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerIDModalLabel">แก้ไข Customer ID</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCustomerIDForm">
                        <input type="hidden" id="edit_user_id" name="edit_user_id">
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Customer ID</label>
                            <input type="text" class="form-control" id="customer_id" name="customer_id" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Trigger editCustomerIDModal on click
            $(document).on('click', '.editCustomerID', function() {
                var userId = $(this).data('user-id');
                $('#edit_user_id').val(userId);
                $('#editCustomerIDModal').modal('show');
            });

            // Submit the form to save the Customer ID
            $('#editCustomerIDForm').on('submit', function(e) {
                e.preventDefault();
                var formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'function/action_edituser/edit_user.php',
                    data: formData,
                    success: function(response) {
                        $('#editCustomerIDModal').modal('hide');

                        // Use SweetAlert to show success message
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Customer ID saved successfully!',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload(); // Reload the page to reflect changes
                            }
                        });
                    },
                    error: function() {
                        // Use SweetAlert to show error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to save Customer ID. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            });
        });
    </script>

    </div>
    </div>
    </div>
    </div>
    </div>

    <script>
        $(document).ready(function() {
            let userId = null;
            let userIdSet = false;

            if ($('#user_id').val()) {
                userId = $('#user_id').val();
                userIdSet = true;
            }

            $(document).on('click', '.reset-password-btn', function() {
                if (!userIdSet) {
                    userId = $(this).data('id');
                    console.log('User ID retrieved from button:', userId);
                    $('#user_id').val(userId);
                    console.log('User ID set in hidden input:', $('#user_id').val());
                    userIdSet = true;
                }
                console.log('User ID set:', userIdSet);
            });

            $('#resetPasswordForm').submit(function(e) {
                e.preventDefault();

                console.log('Submitting form with User ID:', userId); // Debug log
                $('#user_id').val(userId); // Set user ID in hidden input

                let newPassword = $('#new-password').val();
                let confirmPassword = $('#confirm-password').val();

                if (newPassword !== confirmPassword) {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'รหัสผ่านไม่ตรงกัน',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    return;
                }
                console.log('New Password');
                $.ajax({
                    url: 'function/action_edituser/reset_password.php',
                    type: 'post',
                    dataType: 'json', // Expect JSON response
                    data: {
                        newPassword: newPassword,
                        user_id: userId
                    },
                    success: function(response) {
                        console.log('Response from server:', response); // Debug log
                        if (response.status === 'success') {
                            $('#resetPasswordModal').modal('hide');
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'เปลี่ยนรหัสผ่านสำเร็จ',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                position: 'center',
                                icon: 'error',
                                title: response.message || 'เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        Swal.fire({
                            position: 'center',
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาดในการเปลี่ยนรหัสผ่าน',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });
        });
    </script>

    <!-- Reset Password Modal -->
    <div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel">เปลี่ยนรหัสผ่าน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="resetPasswordForm">
                        <div class="mb-3">
                            <input type="hidden" id="user_id" name="user_id" value="">
                        </div>

                        <div class="mb-3">
                            <label for="new-password" class="form-label">รหัสผ่านใหม่</label>
                            <input type="password" class="form-control" id="new-password" name="newPassword" required placeholder="Enter new password">
                        </div>
                        <div class="mb-3">
                            <label for="confirm-password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                            <input type="password" class="form-control" id="confirm-password" name="confirmPassword" required placeholder="Confirm new password">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="reset-password-btn btn btn-success">ยืนยันการเปลี่ยนรหัสผ่าน</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function delUser(id) {
            let option = {
                url: 'function/action_edituser/del_user.php',
                type: 'post',
                dataType: 'json',
                data: {
                    id: id,
                    delUser: 1
                },
                success: function(res) {
                    if (res.status === 'success') {
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
                    } else {
                        // Display error message using Swal.fire
                        Swal.fire({
                            title: 'Error!',
                            text: res.message,
                            icon: 'error',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                },
                error: function(xhr, status, error) {
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
                    // Proceed with AJAX request to delete the user
                    $.ajax(option);
                }
            });
        }
    </script>

    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>