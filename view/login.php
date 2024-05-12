<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once('function/head.php'); ?>

    <style>
        body {
            background-color: #f8f9fa; /* Light grey background */
        }
        .btn-custom {
            background-color: #F0592E; /* Custom orange color */
            border: none;
        }
        .btn-custom:hover {
            background-color: #c9471e; /* Darker shade for hover */
        }
        .form-link {
            color: #F0592E; /* Custom orange color for links */
        }
    </style>
</head>
<body>

<?php require_once('function/navbar.php'); ?>

<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh;">
        <div class="col-md-4 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4"><?php echo $lang_login ?></h4>
                    <form id="login" method="post">
                        <div class="mb-3">
                            <label for="signin-email" class="form-label"><?php echo $lang_email ?></label>
                            <input type="email" class="form-control" id="signin-email" name="signin-email" required placeholder="Enter email">
                        </div>
                        <div class="mb-3">
                            <label for="signin-password" class="form-label"><?php echo $lang_password ?></label>
                            <input type="password" class="form-control" id="signin-password" name="signin-password" required placeholder="Password">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-custom" name="login"><?php echo $lang_login ?></button>
                        </div>
                        <p class="mt-3">
                            <a href="#forgotPasswordModal" class="form-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal"><?php echo $lang_forgotpassword ?></a>
                        </p>
                        <p class="mb-0">
                            <?php echo $lang_donthaveaccount ?> <a href="register.php" class="form-link"><?php echo $lang_signup ?></a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#login').submit(function(e) {
        e.preventDefault();

        // รับค่าจากฟอร์ม
        let user_email = $('#signin-email').val();
        let user_pass = $('#signin-password').val();

        // ส่งค่าผ่าน AJAX ไปยัง action_login.php
        $.ajax({
            url: 'function/action_login.php',
            type: 'post',
            data: {
                user_email: user_email,
                user_pass: user_pass,
                login: 1
            },
            success: function(response) {
                // ตรวจสอบค่าที่ส่งกลับจาก action_login.php
                if (response === 'user') {
                    // เข้าสู่ระบบสำเร็จ
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เข้าสู่ระบบสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        window.location.href = "../view/mainpage"; // // Redirect ไปยังหน้า index.php หรือหน้าที่ต้องการ
                    }, 1500);
                }else if (response === 'admin') {
                    // เข้าสู่ระบบสำเร็จ
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เข้าสู่ระบบสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        window.location.href = "../view/admin/index"; // // Redirect ไปยังหน้า index.php หรือหน้าที่ต้องการ
                    }, 1500);
                }else if (response === 'employee') {
                    // เข้าสู่ระบบสำเร็จ
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เข้าสู่ระบบสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        window.location.href = "../view/employee/index"; // // Redirect ไปยังหน้า index.php หรือหน้าที่ต้องการ
                    }, 1500);
                }else if (response === 'clerk') {
                    // เข้าสู่ระบบสำเร็จ
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เข้าสู่ระบบสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        window.location.href = "../view/clerk/index"; // // Redirect ไปยังหน้า index.php หรือหน้าที่ต้องการ
                    }, 1500);
                }else if (response === 'failuser') {
                    // ไม่มีบัญชีนี้ในระบบ
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'ไม่มีบัญชีนี้ในระบบ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#signin-password').val('');
                } else if (response === 'failpass') {
                    // รหัสผ่านไม่ถูกต้อง
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'รหัสผ่านไม่ถูกต้อง!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#signin-password').val('');
                } else if (response === 'close') {
                    // บัญชีนี้ถูกระงับการใช้งาน
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'บัญชีนี้ถูกระงับการใช้งาน',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            }
        });
    });
});
</script>
<!-- Forgot Password Modal -->
<div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">กรุณาติดต่อแอดมิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="forgotPasswordForm">
                    <div class="mb-3">
                        <h1>Line ID : @admin</h1>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary bg-orange" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
