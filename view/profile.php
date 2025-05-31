<?php
// เริ่ม output buffering และจัดการภาษา
ob_start();
require_once('function/language.php');

// ตรวจสอบก่อนเริ่ม session (language.php จัดการ session ให้แล้ว)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once('config/connect.php');

if (!isset($_SESSION['login'])) {
  //echo '<script>location.href="login"</script>';
}

function fetchUserProfile($conn, $userId)
{
  $sql = "SELECT tb_user.*, 
            provinces.name_th AS province_name, 
            amphures.name_th AS amphure_name, 
            districts.name_th AS district_name,
            districts.zip_code AS zipcode 
            FROM tb_user
            LEFT JOIN provinces ON tb_user.province_id = provinces.id 
            LEFT JOIN amphures ON tb_user.amphure_id = amphures.id 
            LEFT JOIN districts ON tb_user.district_id = districts.id 
            WHERE user_id = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  return $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
}

function getImageBase64($imageData)
{
  return 'data:image/jpeg;base64,' . base64_encode($imageData);
}

$userId = $_SESSION['user_id'];
$myprofile = fetchUserProfile($conn, $userId);
$imageBase64 = !empty($myprofile['user_img']) ? getImageBase64($myprofile['user_img']) : 'path/to/default/avatar.png';
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Profile</title>
  <?php require_once('function/head.php'); ?>

  <style>
    /* Reset และ base styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    html, body {
      height: 100%;
    }

    body {
      display: flex;
      flex-direction: column;
      min-height: 100vh;
      background-color: #f8f9fa;
    }

    /* Main content wrapper */
    .main-wrapper {
      flex: 1;
      display: flex;
      flex-direction: column;
    }

    .content-area {
      flex: 1;
      padding-bottom: 20px;
    }

    /* Profile specific styles */
    .profile-container {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      margin: 50px 20px;
      padding: 20px;
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .profile-details {
      max-width: calc(100% - 200px);
    }

    .profile-picture {
      width: 200px;
      height: 200px;
      border-radius: 50%;
      overflow: hidden;
      border: 1px solid #fff;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      transition: transform 0.3s ease-in-out;
      object-fit: cover;
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
      max-width: 800px;
      padding: 20px;
      background-color: #e9ecef;
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
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
      background-color: #fefefe;
      margin: 5% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 90%;
      max-width: 600px;
      border-radius: 10px;
      overflow: auto;
      max-height: 80vh;
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

    /* Custom scrollbar */
    ::-webkit-scrollbar {
      width: 9px;
    }

    ::-webkit-scrollbar-thumb {
      background-color: #FF5722;
      border-radius: 10px;
    }

    ::-webkit-scrollbar-track {
      background-color: #f1f1f1;
      border-radius: 10px;
    }

    .highlighted-text {
      font-family: 'Kanit', sans-serif !important;
      font-size: 16px;
      color: #F0592E !important;
      text-decoration: none;
    }

    .highlighted-text:hover {
      color: #FF5722;
      text-decoration: none;
      font-family: 'Kanit', sans-serif !important;
    }

    .highlighted-text a:link,
    .highlighted-text a:visited,
    .highlighted-text a:hover,
    .highlighted-text a:active {
      color: #F0592E !important;
      text-decoration: none !important;
      font-family: 'Kanit', sans-serif !important;
    }

    /* Footer จะอยู่ด้านล่างเสมอ */
    footer {
      margin-top: auto;
    }

    /* Profile content min-height */
    .profile-content {
      min-height: calc(100vh - 300px);
      padding: 20px 0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
      .profile-container {
        flex-direction: column;
        align-items: center;
        text-align: center;
      }

      .profile-details {
        max-width: 100%;
        margin-top: 20px;
      }

      .profile-details p i {
        margin-right: 0;
      }

      .profile-content {
        min-height: calc(100vh - 250px);
        padding: 15px 0;
      }
    }

    @media (max-width: 576px) {
      .profile-content {
        min-height: calc(100vh - 200px);
        padding: 10px 0;
      }
    }
  </style>
</head>

<body>
  <div class="main-wrapper">
    <!-- Navigation -->
    <nav>
      <?php require_once('function/navindex.php'); ?>
    </nav>

    <!-- Main Content -->
    <div class="content-area">
      <div class="profile-content">
        <div class="container py-5">
          <div class="row">
            <div class="col-lg-4">
              <div class="card mb-4">
                <div class="card-body text-center">
                  <img src="<?php echo $imageBase64; ?>" alt="avatar" class="profile-picture">
                  <h5 class="my-3"><?php echo $myprofile['user_firstname'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $myprofile['user_lastname'] ?></h5>
                  <p class="text-muted mb-1"><?php echo $myprofile['user_email'] ?></p>
                  <hr>
                  <div class="d-flex justify-content-center mb-2">
                    <button class="btn btn-primary" onclick="openModal('editProfileModal')"><?php echo $lang == 'th' ? 'ตั้งค่าโปรไฟล์' : 'Profile Settings'; ?></button>
                  </div>
                </div>
              </div>

              <div class="card mb-4">
                <div class="card-body text-center">
                  <div class="mb-2 highlighted-text">
                    <i class="fas fa-truck">
                    <a href="orderhistory.php?tab=ongoing">&nbsp; <?php echo $lang == 'th' ? 'กำลังจัดส่ง' : 'On Delivery'; ?></a>
                    </i>
                  </div>
                  <hr>
                  <div class="mb-2 highlighted-text">
                    <i class="fas fa-history">
                    <a href="orderhistory.php?tab=history">&nbsp; <?php echo $lang == 'th' ? 'ประวัติการสั่งซื้อ' : 'Order History'; ?></a>
                    </i>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-lg-8">
              <div class="card mb-4">
                <div class="card-body">
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0"><?php echo $lang_fullname ?></p>
                    </div>
                    <div class="col-sm-9">
                      <p class="text-muted mb-0"><?php echo $myprofile['user_firstname'] ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $myprofile['user_lastname'] ?></p>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0"><?php echo $lang_email ?></p>
                    </div>
                    <div class="col-sm-9">
                      <p class="text-muted mb-0"><?php echo $myprofile['user_email'] ?></p>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0"><?php echo $lang_mobile ?></p>
                    </div>
                    <div class="col-sm-9">
                      <p class="text-muted mb-0"><?php echo $myprofile['user_tel'] ?></p>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0"><?php echo $lang_address ?></p>
                    </div>
                    <div class="col-sm-9">
                      <p class="text-muted mb-0"><?php echo $myprofile['user_address'] ?></p>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0"><?php echo $lang_provinces ?></p>
                    </div>
                    <div class="col-sm-9">
                      <p class="text-muted mb-0"><?php echo $myprofile['province_name'] ?></p>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0"><?php echo $lang_amphures ?></p>
                    </div>
                    <div class="col-sm-9">
                      <p class="text-muted mb-0"><?php echo $myprofile['amphure_name'] ?></p>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0"><?php echo $lang_districts ?></p>
                    </div>
                    <div class="col-sm-9">
                      <p class="text-muted mb-0"><?php echo $myprofile['district_name'] ?></p>
                    </div>
                  </div>
                  <hr>
                  <div class="row">
                    <div class="col-sm-3">
                      <p class="mb-0"><?php echo $lang_zipcode ?></p>
                    </div>
                    <div class="col-sm-9">
                      <p class="text-muted mb-0"><?php echo $myprofile['zipcode'] ?></p>
                    </div>
                  </div>
                  <hr>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <?php require_once('function/footer.php'); ?>
  </footer>

  <!-- Modal HTML -->
  <div id="editProfileModal" class="modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"><?php echo $lang == 'th' ? 'ตั้งค่าโปรไฟล์' : 'Profile Settings'; ?></h5>
          <button type="button" class="close" onclick="closeModal('editProfileModal')">&times;</button>
        </div>
        <div class="modal-body">
          <form action="#" id="profileForm" method="post" enctype="multipart/form-data">
            <div class="form-group">
              <label for="user_img"><?php echo $lang == 'th' ? 'อัพโหลดรูปโปรไฟล์' : 'Upload Profile Picture'; ?></label>
              <input type="file" class="form-control" id="user_img" name="user_img" onchange="previewImage(this)">
              <img id="avatar-preview" src="#" alt="Avatar Preview" style="max-width: 100%; max-height: 200px; display: none;">
            </div>
            <div class="form-group">
              <label for="user_firstname"><?php echo $lang_fristname; ?></label>
              <input type="text" class="form-control" id="user_firstname" name="user_firstname" value="<?php echo $myprofile['user_firstname']; ?>">
            </div>
            <div class="form-group">
              <label for="user_lastname"><?php echo $lang_lastname; ?></label>
              <input type="text" class="form-control" id="user_lastname" name="user_lastname" value="<?php echo $myprofile['user_lastname']; ?>">
            </div>
            <div class="form-group">
              <label for="user_email"><?php echo $lang_email; ?></label>
              <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo $myprofile['user_email']; ?>">
            </div>
            <div class="form-group">
              <label for="user_tel"><?php echo $lang_tel; ?></label>
              <input type="text" class="form-control" id="user_tel" name="user_tel" value="<?php echo $myprofile['user_tel']; ?>">
            </div>
            <button type="submit" class="btn btn-success"><?php echo $lang == 'th' ? 'บันทึกการเปลี่ยนแปลง' : 'Save Changes'; ?></button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <?php require_once('function/function_updateprofile.php'); ?>

  <!-- Scripts -->
  <script src="https://kit.fontawesome.com/a076d05399.js"></script>
  <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function previewImage(input) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          document.getElementById('avatar-preview').src = e.target.result;
          document.getElementById('avatar-preview').style.display = 'block';
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

    // เช็คความสูงของเนื้อหาและปรับ footer
    function adjustFooter() {
      const body = document.body;
      const html = document.documentElement;
      const height = Math.max(body.scrollHeight, body.offsetHeight, 
                             html.clientHeight, html.scrollHeight, html.offsetHeight);
      
      if (height < window.innerHeight) {
        document.querySelector('footer').style.position = 'fixed';
        document.querySelector('footer').style.bottom = '0';
        document.querySelector('footer').style.width = '100%';
      }
    }

    // เรียกใช้เมื่อโหลดหน้าเสร็จ
    window.addEventListener('load', adjustFooter);
    window.addEventListener('resize', adjustFooter);
  </script>
</body>

</html>