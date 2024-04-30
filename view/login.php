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
                            <a href="#" class="form-link">Forgot password?</a>
                        </p>
                        <p>
                            Don't have an account? <a href="#" class="form-link">Sign up</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('function/function_login.php'); ?>
</body>
</html>
