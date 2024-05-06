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
require_once('function/navbar.php');
?>

<br>
<br>
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title text-center mb-4"><?php echo $lang_register?></h4>
                    <form action="#" id="register" method="post">
                        <div class="mb-3">
                            <label for="register-email" class="form-label"><?php echo $lang_fristname?></label>
                            <input type="text" class="form-control" id="register-name" name="register-name" require placeholder="<?php echo $lang_fristname?>">
                        </div>
                        <div class="mb-3">
                            <label for="register-email" class="form-label"><?php echo $lang_lastname?></label>
                            <input type="text" class="form-control" id="register-name" name="register-name" require placeholder="<?php echo $lang_lastname?>">
                        </div>
                        <div class="mb-3">
                            <label for="register-email" class="form-label"><?php echo $lang_email?></label>
                            <input type="email" class="form-control" id="register-email" name="register-name" require placeholder="<?php echo $lang_email?>">
                        </div>
                        <div class="mb-3">
                            <label for="register-password" class="form-label"><?php echo $lang_password?></label>
                            <input type="password" class="form-control" id="register-password" name="register-password" required placeholder="<?php echo $lang_password?>">
                        </div>
                        <div class="mb-3">
                            <label for="register-password" class="form-label"><?php echo $lang_confirmPassword?></label>
                            <input type="password" class="form-control" id="register-c-password" name="register-c-password" required placeholder="<?php echo $lang_confirmPassword?>">
                        </div>
                        <div class="mb-3">
                            <label for="register-address" class="form-label"><?php echo $lang_address?></label>
                            <input type="text" class="form-control" id="register-address" name="register-address" required placeholder="<?php echo $lang_addressph?>">
                        </div>

                        <div class="mb-3">
                            <label for="register-country" class="form-label"><?php echo $lang_provinces?></label>
                            <select class="form-select" id="register-country" name="register-country" required>
                                <option value="" disabled selected><?php echo $lang_provincesph?></option>
                                <option value="USA">United States</option>
                                <option value="UK">United Kingdom</option>
                                <option value="Canada">Canada</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="register-country" class="form-label"><?php echo $lang_amphures?></label>
                            <select class="form-select" id="register-country" name="register-country" required>
                                <option value="" disabled selected><?php echo $lang_amphuresph?></option>
                                <option value="USA">United States</option>
                                <option value="UK">United Kingdom</option>
                                <option value="Canada">Canada</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="register-country" class="form-label"><?php echo $lang_districts?></label>
                            <select class="form-select" id="register-country" name="register-country" required>
                                <option value="" disabled selected><?php echo $lang_districtsph?></option>
                                <option value="USA">United States</option>
                                <option value="UK">United Kingdom</option>
                                <option value="Canada">Canada</option>
                                <!-- Add more options as needed -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="register-address" class="form-label"><?php echo $lang_zipcode?></label>
                            <input type="text" disabled class="form-control" id="register-address" name="register-address" required placeholder="<?php echo $lang_zipcode?>">
                        </div>

                        <div class="mb-3">
                            <label for="register-tel" class="form-label"><?php echo $lang_tel?></label>
                            <input type="text" class="form-control" id="register-tel" name="register-tel" required placeholder="<?php echo $lang_telph?>">
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" name="register" class="btn btn-custom"><?php echo $lang_register?></button>
                        </div>
                        <p class="mt-3">
                        <?php echo $lang_haveaccount?> <a href="login.php" class="form-link"><?php echo  $lang_login?></a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require_once('function/function_register.php'); ?>
<script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<br><br><br>
</body>





</html>
