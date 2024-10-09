<?php
session_start();
?>

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
                            <input type="email" class="form-control" id="signin-email" name="signin-email" value="<?php echo isset($_COOKIE['username']) ? $_COOKIE['username'] : ''; ?>" required placeholder="Enter email">
                        </div>
                        <div class="mb-3">
                            <label for="signin-password" class="form-label"><?php echo $lang_password ?></label>
                            <input type="password" class="form-control" id="signin-password" name="signin-password" value="<?php echo isset($_COOKIE['password']) ? $_COOKIE['password'] : ''; ?>" required placeholder="Password">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember" <?php echo isset($_COOKIE['username']) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="remember">Remember Me</label>
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
    // Login form submit
    $('#login').submit(function(e) {
        e.preventDefault();

        let user_email = $('#signin-email').val();
        let user_pass = $('#signin-password').val();
        let remember = $('#remember').is(':checked');

        $.ajax({
            url: 'function/action_login.php',
            type: 'post',
            contentType: 'application/json',  // Ensure JSON is sent
            data: JSON.stringify({
                user_email: user_email,
                user_pass: user_pass,
                remember: remember,
                login: 1
            }),
            success: function(response) {
                console.log(response); // Log the response for debugging
                
                // Handle different user types
                if (response === 'user' || response === 'admin' || response === 'employee' || response === 'clerk') {
                    if (remember) {
                        document.cookie = "username=" + user_email + "; max-age=" + (86400 * 30) + "; path=/";
                        document.cookie = "password=" + user_pass + "; max-age=" + (86400 * 30) + "; path=/";
                    } else {
                        document.cookie = "username=; max-age=-1; path=/";
                        document.cookie = "password=; max-age=-1; path=/";
                    }

                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เข้าสู่ระบบสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Delay for redirect to main page
                    setTimeout(function() {
                        // Fetch and log session permissions
                        $.ajax({
                            url: 'function/fetch_session_permissions.php',
                            type: 'get',
                            success: function(permissionResponse) {
                                console.log("Session Permissions:", permissionResponse);
                            }
                        });

                        if (response === 'admin') {
                            window.location.href = "../view/admin/permission_admin";
                        } else if (response === 'user') {
                            window.location.href = "../view/mainpage";
                        } else if (response === 'employee') {
                            window.location.href = "../view/employee/dashboard";
                        } else if (response === 'clerk') {
                            window.location.href = "../view/clerk/dashboard";
                        }
                    }, 1500);
                } else if (response === 'failuser') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'ไม่มีบัญชีนี้ในระบบ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#signin-password').val('');
                } else if (response === 'failpass') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'รหัสผ่านไม่ถูกต้อง!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#signin-password').val('');
                } else if (response === 'close') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'บัญชีนี้ถูกระงับการใช้งาน',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    console.log('Unexpected response:', response);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', status, error);
                console.log(xhr.responseText); // Log the actual response
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
                <h5 class="modal-title" id="modalLabel">ลืมรหัสผ่าน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="forgotPasswordForm">
                    <div class="mb-3">
                        <label for="forgot-password-email" class="form-label">อีเมล</label>
                        <input type="email" class="form-control" id="forgot-password-email" name="email" required placeholder="Enter email">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">ยืนยันอีเมล</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                        <label for="new-password" class="form-label">รหัสผ่านใหม่</label>
                        <input type="password" class="form-control" id="new-password" name="new-password" required placeholder="Enter new password">
                    </div>
                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">ยืนยันรหัสผ่านใหม่</label>
                        <input type="password" class="form-control" id="confirm-password" name="confirm-password" required placeholder="Confirm new password">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">ยืนยันการเปลี่ยนรหัสผ่าน</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>
