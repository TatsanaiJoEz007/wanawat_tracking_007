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
<?php
require_once('function/navacc.php');
?>
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Register</h4>
                    <form action="#" id="register" method="post">
                        <div class="mb-3">
                            <label for="register-email" class="form-label">Username</label>
                            <input type="text" class="form-control" id="register-name" name="register-name" require placeholder="Enter username">
                        </div>
                        <div class="mb-3">
                            <label for="register-email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="register-email" name="register-name" require placeholder="Enter email">
                        </div>
                        <div class="mb-3">
                            <label for="register-password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="register-password" name="register-password" required placeholder="Password">
                        </div>
                        <div class="mb-3">
                            <label for="register-password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="register-c-password" name="register-c-password" required placeholder="Confirm Password">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="register" class="btn btn-custom">Register</button>
                        </div>
                        <p class="mt-3">
                            Already have an account? <a href="login.php" class="form-link">Log in</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once('function/function_register.php'); ?>
<script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
