<?php
session_start();

require_once('config/connect.php');

if (!isset($_SESSION['login'])) {
    //echo '<script>location.href="login"</script>';
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <!-- Assuming 'function/head.php' includes necessary meta tags, stylesheets, etc. -->
    <?php require_once('function/head.php'); ?>

    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa; /* Set light gray background */
        }

        .profile-container {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-grow: 1;
            margin: 50px 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-details {
            max-width: calc(100% - 200px); /* Adjust as needed */
        }

        .profile-picture {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            overflow: hidden;
            border: 5px solid #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease-in-out;
        }

        .profile-picture:hover {
            transform: scale(1.05);
        }

        .profile-picture img {
            width: 100%;
            height: auto;
            border-radius: 50%;
        }

        .profile-details h2 {
            margin-top: 0;
            color: #333;
        }

        .profile-details p {
            margin: 5px 0;
            color: #555;
        }

        .profile-details p i {
            margin-right: 350px;
        }

        .parcel-bill-list {
            max-width: 800px; /* Adjust as needed */
            padding: 20px;
            background-color: #e9ecef; /* Set light gray background */
            border-radius: 10px;
            

        }

        .parcel-bill-list h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #555;
            
           
        }

        .bill-card {
            background-color: #fff;
            border-radius: 10px;
            margin-bottom: 10px;
            cursor: pointer;
            transition: box-shadow 0.3s ease;
        }

        .bill-card:hover {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .bill-card table {
            width: 100%;
            border-collapse: collapse;
        }

        .bill-card th,
        .bill-card td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .bill-card th {
            background-color: #f2f2f2;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <nav>
        <?php require_once('function/navindex.php'); ?>
    </nav>

    <div class="container py-5">

                            <?php 
                            $sql = "SELECT * FROM tb_user WHERE user_id = '$_SESSION[user_id]'";
                            $query = $conn->query($sql);
                            $myprofile = $query->fetch_array();
                            ?>
    <div class="row">
      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-body text-center">
            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-chat/ava3.webp" alt="avatar"
              class="rounded-circle img-fluid" style="width: 150px;">
            <h5 class="my-3"><?php echo $myprofile['user_firstname']?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $myprofile['user_lastname']?></h5>
            <p class="text-muted mb-1">--</p>
            <p class="text-muted mb-4">---</p>
            <div class="d-flex justify-content-center mb-2">
             
            </div>
          </div>
        </div>
       
      </div>
      <div class="col-lg-8">
        <div class="card mb-4">
          <div class="card-body">
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0"><?php echo $lang_fullname?></p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php echo $myprofile['user_firstname']?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $myprofile['user_lastname']?></p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0"><?php echo $lang_email?></p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php echo $myprofile['user_email']?></p>
              </div>
            </div>
            
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0"><?php echo $lang_mobile?></p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php echo $myprofile['user_tel']?></p>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0"><?php echo $lang_address?></p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php echo $myprofile['user_address']?></p>
              </div>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
 
    <footer>
        <?php require_once('function/footer.php'); ?>
    </footer>
    <!-- Ensure you have Font Awesome and Bootstrap libraries included for icons and styles -->
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }
    </script>
</body>

</html>
