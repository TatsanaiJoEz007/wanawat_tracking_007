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
<!-- Include SweetAlert library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body>
<?php require_once('function/navbar.php'); ?>
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Forgot the Password?</h4>
                    <form action="#" id="register" method="post">
                        <div class="mb-3">
                            <label for="register-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="register-email" name="register-name" require placeholder="Enter the account email">
                        </div>
                        <div class="d-grid gap-2">
                            <!-- Add onclick event to trigger SweetAlert -->
                            <button type="button" name="register" onclick="showResetPasswordPrompt()" class="btn btn-custom">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once('function/function_register.php'); ?>
<script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Function to show SweetAlert prompt for resetting password
    function showResetPasswordPrompt() {
        Swal.fire({
            title: 'Reset Password',
            html:
                '<input id="swal-input1" class="swal2-input" placeholder="New Password" type="password">' +
                '<input id="swal-input2" class="swal2-input" placeholder="Confirm New Password" type="password">',
            focusConfirm: false,
            preConfirm: () => {
                const newPassword = Swal.getPopup().querySelector('#swal-input1').value;
                const confirmNewPassword = Swal.getPopup().querySelector('#swal-input2').value;
                if (!newPassword || !confirmNewPassword || newPassword !== confirmNewPassword) {
                    Swal.showValidationMessage('Passwords do not match');
                    return false;
                }
                // Example: document.getElementById('register').submit();
                return { newPassword: newPassword, confirmNewPassword: confirmNewPassword };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // You can handle form submission here if needed
                // Example: document.getElementById('register').submit();
                Swal.fire('Submitted!', 'New password: ' + result.value.newPassword + ', Confirm new password: ' + result.value.confirmNewPassword, 'success');
            }
        });
    }
</script>
</body>
</html>
