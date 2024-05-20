<!DOCTYPE html>
<html lang="en">
<head>
<?php require_once('function/head.php'); ?>
<?php 
    require_once('config/connect.php'); 
    $sql = "SELECT * FROM provinces";
    $query = mysqli_query($conn, $sql);
?>
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
                            <label for="register-firstname" class="form-label"><?php echo $lang_fristname?></label>
                            <input type="text" class="form-control" id="register-firstname" name="register-firstname" require placeholder="<?php echo $lang_fristname?>">
                        </div>
                        <div class="mb-3">
                            <label for="register-lastname" class="form-label"><?php echo $lang_lastname?></label>
                            <input type="text" class="form-control" id="register-lastname" name="register-lastname" require placeholder="<?php echo $lang_lastname?>">
                        </div>
                        <div class="mb-3">
                            <label for="register-email" class="form-label"><?php echo $lang_email?></label>
                            <input type="email" class="form-control" id="register-email" name="register-email" require placeholder="<?php echo $lang_email?>">
                        </div>
                        <div class="mb-3">
                            <label for="register-password" class="form-label"><?php echo $lang_password?></label>
                            <input type="password" class="form-control" id="register-password" name="register-password" required placeholder="<?php echo $lang_password?>">
                        </div>
                        <div class="mb-3">
                            <label for="register-c-password" class="form-label"><?php echo $lang_confirmPassword?></label>
                            <input type="password" class="form-control" id="register-c-password" name="register-c-password" required placeholder="<?php echo $lang_confirmPassword?>">
                        </div>
                        <div class="mb-3">
                            <label for="register-address" class="form-label"><?php echo $lang_address?></label>
                            <textarea type="text" class="form-control" id="register-address" name="register-address" required placeholder="<?php echo $lang_addressph?>"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="register-country" class="form-label"><?php echo $lang_provinces?></label>
                            <select class="form-select" id="province" name="province_id" required>
                                <option value="" disabled selected><?php echo $lang_provincesph?></option>
                                <?php while($result = mysqli_fetch_assoc($query)): ?>
                                    <option value="<?=$result['id']?>"><?=$result['name_th']?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="register-country" class="form-label"><?php echo $lang_amphures?></label>
                            <select class="form-select" id="amphure" name="amphure_id" required>
                                <option value="" disabled selected><?php echo $lang_amphuresph?></option>
        
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="register-country" class="form-label"><?php echo $lang_districts?></label>
                            <select class="form-select" id="district" name="district_id" required>
                                <option value="" disabled selected><?php echo $lang_districtsph?></option>
                            </select>
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
<?php require_once('function/function_register2.php'); ?>
<script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="../view/assets/js/script.js"></script>
<br><br><br>
</body>
</html>

<?php
mysqli_close($conn);
?>