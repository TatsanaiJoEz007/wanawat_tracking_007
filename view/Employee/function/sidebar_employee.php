<!DOCTYPE html>
<!-- Designined by CodingLab | www.youtube.com/codinglabyt -->
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <title> Drop Down Sidebar Menu | CodingLab </title>
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
        width: 260px;
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
        display: block
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
        width: 260px;
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
        left: 260px;
        width: calc(100% - 260px);
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
</style>

<body>
    <div class="sidebar close">
        <div class="logo-details">
            <img src="../../view/assets/img/logo/logo.png" alt="logo of wehome" weight="50px" height="50px"
                style="padding-left:8px; padding-right:10px;" />
            <span class="logo_name">Employee</span>
        </div>
        <ul class="nav-links">

            <li>
                <a href="../employee/sendingbill.php">
                    <i class="bx bx-send nav_icon"></i>
                    <span class="link_name">Sending Bill</span>
                </a>
                
            </li>

            <li>
                <a href="../employee/preparing.php">
                    <i class="bi bi-archive nav_icon"></i>
                    <span class="link_name">Preparing</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="preparing.php">Preparing</a></li>
                </ul>
            </li>

            <li>
                <a href="../employee/sending.php">
                    <i class="bi bi-truck nav_icon"></i>
                    <span class="link_name">Sending</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="../employee/sending.php">Sending</a></li>
                </ul>
            </li>

            <li>
                <a href="../employee/history.php">
                    <i class="bi bi-clock-history nav_icon"></i>
                    <span class="link_name">History</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="../employee/history.php">History</a></li>
                </ul>
            </li>

            <li>
                <a href="../employee/problem.php">
                    <i class="bi bi-bag-x nav_icon"></i>
                    <span class="link_name">Problem</span>
                </a>
                <ul class="sub-menu blank">
                    <li><a class="link_name" href="../employee/problem.php">Problem</a></li>
                </ul>
            </li>



            <li>
                <div class="profile-details">
                    <div class="profile-content">
                        <img src="../../view/admin/assets/img/adminpic/admin.jpg" alt="profileImg">
                    </div>
                    <div class="name-job">
                        <div class="profile_name">Employee</div>
                        <div class="job">คนเก็บขี้ปืน</div>
                    </div>
                    <?php require_once "../../view/function/function_logout.php" ?>
                    <button type="logout" style="background-color:#F0592E;" onclick="logout()"><i class='bx bx-log-out'>
                        </i></button>

                </div>
            </li>
        </ul>
    </div>
    <section class="home-section">
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text"></span>
        </div>




        <script>
            let arrow = document.querySelectorAll(".arrow");
            for (var i = 0; i < arrow.length; i++) {
                arrow[i].addEventListener("click", (e) => {
                    let arrowParent = e.target.parentElement.parentElement;//selecting main parent of arrow
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

</body>

</html>