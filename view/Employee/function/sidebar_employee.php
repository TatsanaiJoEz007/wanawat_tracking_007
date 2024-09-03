<?php
require_once('../../view/config/connect.php');


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


// if (!isset($_SESSION['login'])) {
//     echo '<script>location.href="../../view/login.php"</script>';
// }

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
$imageBase64 = !empty($myprofile['user_img']) ? getImageBase64($myprofile['user_img']) : '../../view/assets/img/logo/mascot.png'; // Set your default image path here


?>

<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title>Sidebar Stuff</title>
    <!-- Boxiocns CDN Link -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<style>
    /* Google Fonts Import Link */
    @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300&display=swap');

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Kanit', sans-serif;
    }

    body {
        overflow: hidden;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100%;
        width: 300px;
        /* Increased width */
        background: #F0592E;
        z-index: 100;
        transition: all 0.5s ease;
    }

    .sidebar.close {
        width: 78px;
    }

    .sidebar .logo-details {
        height: 60px;
        width: 100%;
        display: flex;
        align-items: center;
    }

    .sidebar .logo-details i {
        font-size: 30px;
        color: #fff;
        height: 50px;
        min-width: 78px;
        text-align: center;
        line-height: 50px;
    }

    .sidebar .logo-details .logo_name {
        font-size: 22px;
        color: #fff;
        font-weight: 600;
        transition: 0.3s ease;
        transition-delay: 0.1s;
    }

    .sidebar.close .logo-details .logo_name {
        transition-delay: 0s;
        opacity: 0;
        pointer-events: none;
    }

    .sidebar .nav-links {
        height: 100%;
        padding: 30px 0 150px 0;
        overflow: auto;
    }

    .sidebar.close .nav-links {
        overflow: visible;
    }

    .sidebar .nav-links::-webkit-scrollbar {
        display: none;
    }

    .sidebar .nav-links li {
        position: relative;
        list-style: none;
        transition: all 0.4s ease;
    }

    .sidebar .nav-links li:hover {
        background: #F0592E;
    }

    .sidebar .nav-links li .iocn-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sidebar.close .nav-links li .iocn-link {
        display: block;
    }

    .sidebar .nav-links li i {
        height: 50px;
        min-width: 78px;
        text-align: center;
        line-height: 50px;
        color: #fff;
        font-size: 20px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .sidebar .nav-links li.showMenu i.arrow {
        transform: rotate(-180deg);
    }

    .sidebar.close .nav-links i.arrow {
        display: none;
    }

    .sidebar .nav-links li a {
        display: flex;
        align-items: center;
        text-decoration: none;
    }

    .sidebar .nav-links li a .link_name {
        font-size: 18px;
        font-weight: 400;
        color: #fff;
        transition: all 0.4s ease;
    }

    .sidebar.close .nav-links li a .link_name {
        opacity: 0;
        pointer-events: none;
    }

    .sidebar .nav-links li .sub-menu {
        padding: 6px 6px 14px 80px;
        margin-top: -10px;
        background: #F0592E;
        display: none;
    }

    .sidebar .nav-links li.showMenu .sub-menu {
        display: block;
    }

    .sidebar .nav-links li .sub-menu a {
        color: #fff;
        font-size: 15px;
        padding: 5px 0;
        white-space: nowrap;
        opacity: 0.6;
        transition: all 0.3s ease;
    }

    .sidebar .nav-links li .sub-menu a:hover {
        opacity: 1;
    }

    .sidebar.close .nav-links li .sub-menu {
        position: absolute;
        left: 100%;
        top: -10px;
        margin-top: 0;
        padding: 10px 20px;
        border-radius: 0 6px 6px 0;
        opacity: 0;
        display: block;
        pointer-events: none;
        transition: 0s;
    }

    .sidebar.close .nav-links li:hover .sub-menu {
        top: 0;
        opacity: 1;
        pointer-events: auto;
        transition: all 0.4s ease;
    }

    .sidebar .nav-links li .sub-menu .link_name {
        display: none;
    }

    .sidebar.close .nav-links li .sub-menu .link_name {
        font-size: 18px;
        opacity: 1;
        display: block;
    }

    .sidebar .nav-links li .sub-menu.blank {
        opacity: 1;
        pointer-events: auto;
        padding: 3px 20px 6px 16px;
        opacity: 0;
        pointer-events: none;
    }

    .sidebar .nav-links li:hover .sub-menu.blank {
        top: 50%;
        transform: translateY(-50%);
    }

    .sidebar .profile-details {
        position: fixed;
        bottom: 0;
        width: 300px;
        /* Increased width */
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #F0592E;
        padding: 12px 0;
        transition: all 0.5s ease;
    }

    .sidebar.close .profile-details {
        background: none;
    }

    .sidebar.close .profile-details {
        width: 78px;
    }

    .sidebar .profile-details .profile-content {
        display: flex;
        align-items: center;
    }

    .sidebar .profile-details img {
        height: 52px;
        width: 52px;
        object-fit: cover;
        border-radius: 16px;
        margin: 0 14px 0 12px;
        background: #F0592E;
        transition: all 0.5s ease;
    }

    .sidebar.close .profile-details img {
        padding: 10px;
    }

    .sidebar .profile-details .profile_name,
    .sidebar .profile-details .job {
        color: #fff;
        font-size: 18px;
        font-weight: 500;
        white-space: nowrap;
    }

    .sidebar.close .profile-details i,
    .sidebar.close .profile-details .profile_name,
    .sidebar.close .profile-details .job {
        display: none;
    }

    .sidebar .profile-details .job {
        font-size: 12px;
    }

    .home-section {
        position: relative;
        background: #fff;
        height: 100vh;
        left: 300px;
        /* Adjusted to new sidebar width */
        width: calc(100% - 300px);
        /* Adjusted to new sidebar width */
        transition: all 0.5s ease;
        padding: 12px;
    }

    .sidebar.close~.home-section {
        left: 78px;
        width: calc(100% - 78px);
    }

    .home-content {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }

    .home-section .home-content .bx-menu,
    .home-section .home-content .text {
        color: #11101d;
        font-size: 35px;
    }

    .home-section .home-content .bx-menu {
        cursor: pointer;
        margin-right: 10px;
    }

    .home-section .home-content .text {
        font-size: 26px;
        font-weight: 600;
    }

    @media screen and (max-width: 400px) {
        .sidebar {
            width: 240px;
        }

        .sidebar.close {
            width: 78px;
        }

        .sidebar .profile-details {
            width: 240px;
        }

        .sidebar.close .profile-details {
            background: none;
        }

        .sidebar.close .profile-details {
            width: 78px;
        }

        .home-section {
            left: 240px;
            width: calc(100% - 240px);
        }

        .sidebar.close~.home-section {
            left: 78px;
            width: calc(100% - 78px);
        }
    }

    /* Adjustments for sidebar collapse */
    .sidebar .logo-details .logo_name,
    .sidebar .nav-links li .link_name,
    .sidebar .profile-details .profile_name,
    .sidebar .profile-details .job {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transition: all 0.3s ease;
    }

    .sidebar .nav-links li .sub-menu {
        padding: 6px 6px 14px 78px;
    }

    .sidebar .profile-details img {
        height: 50px;
        width: 50px;
        border-radius: 50%;
        margin: 0 10px;
        transition: all 0.5s ease;
    }

    @media screen and (max-width: 768px) {
        .sidebar {
            width: 78px;
        }

        .sidebar .logo-details .logo_name,
        .sidebar .nav-links li .link_name,
        .sidebar .profile-details .profile_name,
        .sidebar .profile-details .job {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar .profile-details img {
            height: 40px;
            width: 40px;
            margin: 0;
        }

        .sidebar .nav-links li .sub-menu {
            padding: 6px 6px 14px 65px;
        }

        .sidebar.close .logo-details .logo_name,
        .sidebar.close .nav-links li .link_name,
        .sidebar.close .profile-details .profile_name,
        .sidebar.close .profile-details .job {
            opacity: 1;
            pointer-events: auto;
        }

        .sidebar.close .profile-details img {
            height: 50px;
            width: 50px;
            margin: 0 14px 0 12px;
        }

        .home-section {
            left: 78px;
            width: calc(100% - 78px);
        }
    }
</style>

<body>
    <div class="sidebar close">
        <div class="logo-details">
            <img src="../../view/assets/img/logo/logo.png" alt="logo of wehome" weight="50px" height="50px" style="padding-left:8px; padding-right:10px;" />
            <span class="logo_name">Employee</span>
        </div>
        <ul class="nav-links">
            <li>
                <a href="../Employee/dashboard.php">
                    <i class="bx bx-grid-alt nav_icon"></i>
                    <span class="link_name">หน้าหลัก</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="../Employee/dashboard.php">Dashboard</a></li>
                </ul>
            </li>
            <li>
                <div class="iocn-link">
                    <a href="#">
                        <i class="bx bx-cloud-upload nav_icon"></i>
                        <span class="link_name">เพิ่มบิลจาก CSV</span>
                    </a>
                    <i class='bx bxs-chevron-down arrow'></i>
                </div>
                <ul class="sub-menu">
                    <li><a href="../Employee/importCSV.php">เพิ่ม CSV</a></li>
                    <li><a href="../Employee/table.php">หัวบิลที่เพิ่มแล้ว</a></li>
                </ul>
            </li>
            <li>
                <a href="../Employee/statusbill.php">
                    <i class="bx bx-send nav_icon"></i>
                    <span class="link_name">สถานะบิล</span>
                </a>

            </li>

            <li>
                <a href="../Employee/preparing.php">
                    <i class="bi bi-archive nav_icon"></i>
                    <span class="link_name">กำลังจัดเตรียม</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="preparing.php">กำลังจัดเตรียม</a></li>
                </ul>
            </li>

            <li>
                <a href="../Employee/sending.php">
                    <i class="bi bi-truck nav_icon"></i>
                    <span class="link_name">กำลังส่ง</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="../Employee/sending.php">กำลังส่ง</a></li>
                </ul>
            </li>

            <li>
                <a href="../Employee/history.php">
                    <i class="bi bi-clock-history nav_icon"></i>
                    <span class="link_name">ประวัติการจัดส่ง</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="../Employee/history.php">ประวัติการจัดส่ง</a></li>
                </ul>
            </li>

            <li>
                <a href="../Employee/problem.php">
                    <i class="bi bi-bag-x nav_icon"></i>
                    <span class="link_name">ปัญหาที่พบ</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="../Employee/problem.php">ปัญหาที่พบ</a></li>
                </ul>
            </li>
            <li>
                <a href="../Employee/delivery_bill.php">
                    <i class="bi bi-basket nav_icon"></i>
                    <span class="link_name">รายละเอียดบิลสินค้า</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="../Employee/delivery_bill.php">รายละเอียดบิลสินค้า</a></li>
                </ul>
            </li>

            <li>
                <div class="profile-details">
                    <div class="profile-content">
                        <img src=<?php echo $imageBase64; ?> alt="profileImg">
                    </div>
                    <div class="name-job">
                        <div class="profile_name"><?php echo $myprofile['user_firstname'] ?> &nbsp; <?php echo $myprofile['user_lastname'] ?></div>
                        <div class="job"><?php echo $myprofile['user_email'] ?></div>
                    </div>
                    <?php require_once "../../view/admin/function/function_logout.php" ?>
                    <i class='bx bx-log-out' onclick="logout()"></i>
                </div>
            </li>
        </ul>
    </div>
    <section class="home-section">
        <div class="home-content">
            <i class='bx bx-menu'>
            </i>
            <span class="text"></span>
        </div>

        <script>
            function logout() {
                let option = {
                    url: 'function/action_logout.php',
                    type: 'post',
                    data: {
                        logout: 1
                    },
                    success: function(res) {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'ออกจากระบบสำเร็จ!!',
                            showConfirmButton: false,
                            timer: 1500
                        })
                        setTimeout(() => {
                            location.href = '../index'
                        }, 900)
                    }
                }
                $.ajax(option)
            }

            let arrow = document.querySelectorAll(".arrow");
            for (var i = 0; i < arrow.length; i++) {
                arrow[i].addEventListener("click", (e) => {
                    let arrowParent = e.target.parentElement.parentElement; // selecting main parent of arrow
                    arrowParent.classList.toggle("showMenu");
                });
            }

            let sidebar = document.querySelector(".sidebar");
            let sidebarBtn = document.querySelector(".bx-menu");
            console.log(sidebarBtn);
            sidebarBtn.addEventListener("click", () => {
                sidebar.classList.toggle("close");
            });
        </script>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://fastly.jsdelivr.net/npm/sweetalert2@11"></script>
</body>

</html>