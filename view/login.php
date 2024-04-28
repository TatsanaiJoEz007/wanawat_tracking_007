<?php
require_once('function/navbar.php');
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="icon" type="image/x-icon" href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
<title>Login Page</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
<div class="container">
    <div class="row justify-content-center align-items-center" style="height: 100vh;">
        <div class="col-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4">Login</h4>
                    <form>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" placeholder="Enter username">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" placeholder="Password">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-custom">Log In</button>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
