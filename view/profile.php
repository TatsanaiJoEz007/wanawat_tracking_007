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
            overflow: auto; /* เพิ่ม overflow: auto; ที่นี่ */
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px;
            overflow: auto; /* เพิ่ม overflow: auto; ที่นี่ */
            max-height: 80vh; /* ปรับความสูงของ modal ให้สัมพันธ์กับส่วนที่เปิดเนื้อหาได้ในทุกๆ ความสูงของหน้าจอ */
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
            $sql = "SELECT tb_user.*, 
                    provinces.name_th AS province_name, 
                    amphures.name_th AS amphure_name, 
                    districts.name_th AS district_name ,
                    districts.zip_code AS zipcode FROM tb_user
                    LEFT JOIN provinces ON tb_user.province_id = provinces.id 
                    LEFT JOIN amphures ON tb_user.amphure_id = amphures.id 
                    LEFT JOIN districts ON tb_user.district_id = districts.id 
                    WHERE user_id = '$_SESSION[user_id]'";
            $query = $conn->query($sql);
            $myprofile = $query->fetch_array();
        ?>
    <div class="row">
      <div class="col-lg-4">
        <div class="card mb-4">
          <div class="card-body text-center">
            <img src="../view/admin/assets/img/adminpic/admin.jpg" alt="avatar"
              class="rounded-circle img-fluid" style="width: 150px;">
            <h5 class="my-3"><?php echo $myprofile['user_firstname']?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $myprofile['user_lastname']?></h5>
            <p class="text-muted mb-1"><?php echo $myprofile['user_email']?></p>
            <hr>
            <div class="d-flex justify-content-center mb-2">
              <button class="btn btn-primary" onclick="openModal('editProfileModal')">ตั้งค่าโปรไฟล์</button>
            </div>
          </div>
        </div>


        <div class="card mb-4">
          <div class="card-body text-center">
              <div class="mb-2" >
                  <i class="fas fa-history"></i> ประวัติการสั่งซื้อ
              </div>
              <hr>
              <div class="mb-2" >
                  <i class="fas fa-shipping-fast"></i> กำลังจัดส่ง
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

            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0"><?php echo $lang_provinces?></p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php echo $myprofile['province_name']?></p>
              </div>
            </div>

            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0"><?php echo $lang_amphures?></p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php echo $myprofile['amphure_name']?></p>
              </div>
            </div>
            
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0"><?php echo $lang_districts?></p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php echo $myprofile['district_name']?></p>
              </div>
            </div>
            
            <hr>
            <div class="row">
              <div class="col-sm-3">
                <p class="mb-0"><?php echo $lang_zipcode?></p>
              </div>
              <div class="col-sm-9">
                <p class="text-muted mb-0"><?php echo $myprofile['zipcode']?></p>
              </div>
            </div>
            <hr>
          </div>
        </div>
        
      </div>
    </div>
  </div>
        <br>
        <br>
        <br>
        <br> 
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

<!-- Modal HTML -->
<div id="editProfileModal" class="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ตั้งค่าโปรไฟล์</h5>
                <button type="button" class="close" onclick="closeModal('editProfileModal')">&times;</button>
            </div>
            <div class="modal-body">
                <form action="#" id="profileForm" method="post">
                    <!-- เพิ่มฟอร์มอัปโหลดรูปภาพ -->
                    <div class="form-group">
                        <label for="user_img">อัปโหลดรูปภาพ Avatar</label>
                        <input type="file" class="form-control" id="user_img" name="user_img" onchange="previewImage(this)">
                        <img id="avatar-preview" src="#" alt="Avatar Preview" style="max-width: 100%; max-height: 200px; display: none;">
                    </div>
                    <div class="form-group">
                        <label for="user_firstname">ชื่อ</label>
                        <input type="text" class="form-control" id="user_firstname" name="user_firstname" value="<?php echo $myprofile['user_firstname']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_lastname">นามสกุล</label>
                        <input type="text" class="form-control" id="user_lastname" name="user_lastname" value="<?php echo $myprofile['user_lastname']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_email">อีเมล์</label>
                        <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo $myprofile['user_email']; ?>">
                    </div>
                    <div class="form-group">
                        <label for="user_tel">เบอร์โทรศัพท์</label>
                        <input type="text" class="form-control" id="user_tel" name="user_tel" value="<?php echo $myprofile['user_tel']; ?>">
                    </div>
                    <button type="submit" class="btn btn-success">บันทึกการเปลี่ยนแปลง</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php require_once('function/function_updateprofile.php'); ?>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
                document.getElementById('avatar-preview').style.display = 'block'; // แสดงรูปภาพที่เลือก
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function openModal(modalId) {
        document.getElementById(modalId).style.display = "block";
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }

</script>



</body>

</html>
