<?php require_once('../config/connect.php');

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

<!DOCTYPE html>
<html lang="th">

<head>
    <title>Manage - admin</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <style>
        /* ปรับแต่ง modal ให้อยู่ตรงกลางจอ */
        .modal-dialog {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0 auto !important;
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
        }

        .modal-backdrop.show {
            position: fixed;
            top: 0 !important;
            left: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
        }

        /* Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            border-radius: 10px;
        }

        /* Container Styling */
        .home-section {
            max-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
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

                        <!-- Button to delete selected users -->
                        <button type="button" class="btn btn-danger" id="deleteSelectedBtn">
                            ลบผู้ใช้ที่เลือก
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

                                        <form id="register" method="post" enctype="multipart/form-data">
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
                                        <th scope="col" style="text-align: center;"></th>
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
                                    if (isset($_SESSION['user_id'])) {
                                        $i = 1;
                                        $sql = "SELECT * FROM tb_user WHERE user_type = 999 ";
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
                                                <td><input type="checkbox" class="userCheckbox" value="<?php echo $row['user_id']; ?>"></td>
                                                <td><?php echo $i++; ?></td>
                                                <td class="align-middle"><img src="<?php echo $imageBase64; ?>" alt="admin Image" style="width: 50px; height: auto;"></td>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            // Handle form submission for adding an admin
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
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ผิดพลาด',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Capture the response to inspect
                        Swal.fire({
                            icon: 'error',
                            title: 'ผิดพลาด',
                            text: 'เกิดข้อผิดพลาดในการดำเนินการ: ' + xhr.responseText // Show response error
                        });
                        console.log('Error details:', xhr.responseText); // Log the actual error to the console for further inspection
                    }
                });
            });

            // Handle delete selected users
            $('#deleteSelectedBtn').click(function() {
                let selectedIds = [];
                $('.userCheckbox:checked').each(function() {
                    selectedIds.push($(this).val());
                });

                if (selectedIds.length > 0) {
                    Swal.fire({
                        title: 'ต้องการลบผู้ใช้ที่เลือกใช่ไหม?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'ตกลง',
                        cancelButtonText: 'ยกเลิก',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: 'function/action_admin/action_deladmin.php',
                                type: 'POST',
                                data: {
                                    ids: selectedIds,
                                    delUser: 1
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        Swal.fire({
                                            title: 'สำเร็จ!',
                                            text: 'ลบผู้ใช้ที่เลือกเรียบร้อยแล้ว',
                                            icon: 'success',
                                            confirmButtonText: 'ตกลง'
                                        }).then(() => {
                                            location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            title: 'ผิดพลาด!',
                                            text: response.message,
                                            icon: 'error',
                                            confirmButtonText: 'ตกลง'
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        title: 'ผิดพลาด!',
                                        text: 'เกิดข้อผิดพลาดในการลบผู้ใช้',
                                        icon: 'error',
                                        confirmButtonText: 'ตกลง'
                                    });
                                }
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'โปรดเลือกผู้ใช้ที่ต้องการลบ',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            });

            // Handle reset password
            let userId = null;
            $(document).on('click', '.reset-password-btn', function() {
                userId = $(this).data('id');
                $('#user_id').val(userId);
            });

            $('#resetPasswordForm').submit(function(e) {
                e.preventDefault();

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

                $.ajax({
                    url: 'function/action_admin/action_adminreset.php',
                    type: 'POST',
                    data: {
                        newPassword: newPassword,
                        user_id: userId
                    },
                    success: function(response) {
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
                    error: function() {
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