<?php require_once('../config/connect.php'); ?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>Manage - admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, admin-scalable=0, minimal-ui">
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
    </style>



</head>

<body>
    <?php require_once('function/sidebar.php'); ?>

    <h1 class="app-page-title">ตารางข้อมูลผู้ใช้งาน - Admin</h1>
    <hr class="mb-4">
    <div class="container">
        <div class="row g-4 settings-section">
            <div class="col-12 col-md-12">
                <div class="app-card app-card-settings shadow-sm p-4">
                    <div class="app-card-body">
                        <!-- Button to trigger modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            เพิ่มผู้ดูแลระบบ
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

                                        <form action="#" id="register" method="post" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="admin_firstname" class="form-label">ชื่อ</label>
                                                <input type="text" class="form-control" id="admin_firstname" name="admin_firstname" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="admin_lastname" class="form-label">นามสกุล</label>
                                                <input type="text" class="form-control" id="admin_lastname" name="admin_lastname" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="admin_email" class="form-label">อีเมล</label>
                                                <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="admin_pass" class="form-label">รหัสผ่าน</label>
                                                <input type="password" class="form-control" id="admin_pass" name="admin_pass" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="admin_img" class="form-label">รูปภาพ</label>
                                                <input type="file" class="form-control" id="admin_img" name="admin_img">
                                            </div>
                                            <div class="mb-3">
                                                <label for="admin_status" class="form-label">สถานะ</label>
                                                <select class="form-select" id="admin_status" name="admin_status" required>
                                                    <option value="1">อยู่ในระบบ</option>
                                                    <option value="0">ไม่อยู่ในระบบ</option>
                                                </select>
                                            </div>
                                        </form>


                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                        <button type="submit" form="register" class="btn btn-primary">บันทึกข้อมูล</button>
                                    </div>

                                </div>
                            </div>
                        </div>

                        <!-- Table of admins -->
                        <div class="table-responsive">
                            <table class="table table-striped" id="Tableall">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;">#</th>
                                        <th scope="col" style="text-align: center;">รูปภาพ</th>
                                        <th scope="col" style="text-align: center;">ชื่อ</th>
                                        <th scope="col" style="text-align: center;">นามสกุล</th>
                                        <th scope="col" style="text-align: center;">อีเมล</th>
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
                                    foreach ($query as $row) :
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td class="align-middle"><img src="<?php echo $row['user_img']; ?>" alt="admin Image" style="width: 50px; height: auto;"></td>
                                            <td class="align-middle"><?php echo $row['user_firstname'] ?></td>
                                            <td class="align-middle"><?php echo $row['user_lastname'] ?></td>
                                            <td class="align-middle"><?php echo $row['user_email'] ?></td>
                                            <td class="align-middle"><?php echo md5($row['user_pass']); ?></td>
                                            <td class="align-middle"><?php echo ($row['user_status'] == 1) ? "อยู่ในระบบ" : "ไม่อยู่ในระบบ"; ?></td>
                                            <td class="align-middle">
                                                <a href="#" class="btn btn-sm btn-secondary reset-password-btn" data-bs-toggle="modal" data-bs-target="#resetPasswordModal" data-id="<?php echo $row['user_id']; ?>">Reset Password</a>
                                                <a href="#" onclick="delUser('<?php echo $row['user_id']; ?>')" class="btn btn-sm btn-danger">Delete</a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $('#register').submit(function(event) {
                event.preventDefault();

                $.ajax({
                    url: 'function/action_admin/action_addadmin.php',
                    type: 'POST',
                    data: new FormData(this),
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'สำเร็จ',
                                text: response.message
                            }).then(() => {
                                $('#exampleModal').modal('hide');
                                location.reload();
                                // Optionally: refresh your table or add the new row dynamically
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ผิดพลาด',
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด',
                            text: 'เกิดข้อผิดพลาดในการดำเนินการ'
                        });
                    }
                });
            });
        });

        function delUser(id) {
            let option = {
                url: 'function/action_admin/action_deladmin.php',
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
                    url: 'function/action_admin/action_adminreset.php',
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
</body>

</html>