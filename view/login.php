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
                    <h4 class="card-title text-center mb-4">Login</h4>
                    <form action="#" method="post" id="login">
                        <div class="mb-3">
                            <label for="signin-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="signin-email" name="signin-email" required placeholder="Enter email">
                        </div>
                        <div class="mb-3">
                            <label for="signin-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="signin-password" name="signin-password" required placeholder="Password">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="login" class="btn btn-custom">Log In</button>
                        </div>
                        <p class="mt-3">
                            <a href="#forgotPasswordModal" class="form-link" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot password?</a>
                        </p>
                        <p class="mb-0">
                            Don't have an account? <a href="register.php" class="form-link">Sign up</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php require_once('function/function_login.php'); ?>
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
