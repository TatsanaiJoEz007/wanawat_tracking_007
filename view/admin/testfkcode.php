<?php
session_start();
require_once('../config/connect.php'); // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลจากตาราง provinces
$sql = "SELECT * FROM provinces";
$query = mysqli_query($conn, $sql);
if (!$query) {
    die('Query Error: ' . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .btn-custom {
            background-color: #F0592E;
            border: none;
        }
        .btn-custom:hover {
            background-color: #c9471e;
        }
        .form-link {
            color: #F0592E;
        }
    </style>
</head>
<body>
    <?php require_once('function/sidebar.php'); ?>

    <br>
    <br>
    <div class="container">
        <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title text-center mb-4">Register</h4>
                        <form action="#" id="registerForm" method="post">
                            <div class="mb-3">
                                <label for="register-firstname" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="register-firstname" name="register-firstname" required placeholder="First Name">
                            </div>
                            <div class="mb-3">
                                <label for="register-lastname" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="register-lastname" name="register-lastname" required placeholder="Last Name">
                            </div>
                            <div class="mb-3">
                                <label for="register-email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="register-email" name="register-email" required placeholder="Email">
                            </div>
                            <div class="mb-3">
                                <label for="register-password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="register-password" name="register-password" required placeholder="Password">
                            </div>
                            <div class="mb-3">
                                <label for="register-c-password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="register-c-password" name="register-c-password" required placeholder="Confirm Password">
                            </div>
                            <div class="mb-3">
                                <label for="register-address" class="form-label">Address</label>
                                <textarea class="form-control" id="register-address" name="register-address" required placeholder="Address"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="register-country" class="form-label">Province</label>
                                <select class="form-select" id="province" name="province_id" required>
                                    <option value="" disabled selected>Select Province</option>
                                    <?php
                                    while ($result = mysqli_fetch_assoc($query)) {
                                        echo "<option value='{$result['id']}'>{$result['name_th']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="register-country" class="form-label">Amphures</label>
                                <select class="form-select" id="amphure" name="amphure_id" required>
                                    <option value="" disabled selected>Select Amphure</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="register-country" class="form-label">Districts</label>
                                <select class="form-select" id="district" name="district_id" required>
                                    <option value="" disabled selected>Select District</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="register-tel" class="form-label">Telephone</label>
                                <input type="text" class="form-control" id="register-tel" name="register-tel" required placeholder="Telephone">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" name="register" class="btn btn-custom">Register</button>
                            </div>
                            <p class="mt-3">
                                Already have an account? <a href="login.php" class="form-link">Login</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../view/assets/js/script.js"></script>
    <br><br><br>
</body>
</html>

<?php
mysqli_close($conn);
?>
