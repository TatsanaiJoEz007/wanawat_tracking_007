<?php 
session_start();

if ($_SESSION['user_type'] != 'admin') {
    header('Location: ../index.php');
}

?>    


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="icon" type="image/x-icon" href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
</head>
<body>


<?php require_once('function/sidebar.php');  ?>


<script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>