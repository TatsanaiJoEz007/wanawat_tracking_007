<?php
require_once('language.php');
session_start();

require_once('../view/config/connect.php');

if (!isset($_SESSION['login'])) {
    // Uncomment the next line to enable redirection to the login page
    // echo '<script>location.href="login"</script>';
}

function Profilepic($conn, $userId)
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

function base64img($imageData)
{
    return 'data:image/jpeg;base64,' . base64_encode($imageData);
}

$defaultAvatarPath = '../view/assets/img/logo/mascot.png'; // Path to your default avatar image
$userId = $_SESSION['user_id'];
$myprofile = Profilepic($conn, $userId);

if (!empty($myprofile['user_img'])) {
    $imageBase64 = base64img($myprofile['user_img']);
} else {
    $imageBase64 = $defaultAvatarPath; // Use default avatar image path if user image is empty
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Image</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        @keyframes slideDown {
            from {
                transform: translateY(-100%);
            }

            to {
                transform: translateY(0);
            }
        }

        .navbar {
            background-color: #F0592E;
            padding: 10px 20px;
            position: relative;
            z-index: 1000;
            /* High z-index to stay on top */
        }

        .navbar.navbar-expand-lg.navbar-light .navbar-nav .nav-link {
            color: white !important;
            font-size: 16px;
            font-weight: bold;
        }

        .navbar .navbar-toggler {
            border: none;
        }

        .navbar .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='30' height='30' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 1%29' stroke-width='2' linecap='round' linejoin='round' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Style for language switcher icons */
        .language-switcher {
            display: flex;
            align-items: center;
            margin-left: auto;
        }

        .language-switcher img {
            width: 30px;
            margin-left: 5px;
            cursor: pointer;
            margin-top: 5px;
        }

        /* New style for fixed navbar */
        .navbar.fixed-top {
            animation: slideDown 0.5s forwards;
            background-color: #F0592E;
        }

        .navbar-brand img {
            width: 90px;
            height: auto;
            transition: width 0.3s;
            position: absolute;
            top: -1.9px;
            left: 20px;
            z-index: 1001;
            /* Higher z-index than navbar */
        }

        .navbar-nav {
            margin-left: 150px;
        }

        /* Profile image style */
        .profile-image {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid white;
            margin-top: 8px;
            object-fit: fill;
            cursor: pointer;
        }

        .navbar-right {
            position: absolute;
            right: 20px;
            top: 8px;
        }

        .navbar-right .dropdown-menu {
            position: absolute;
            right: 0;
            top: calc(100% + 10px);
            transform: translateX(-50%);
        }

        .navbar-right .dropdown-toggle::after {
            color: #F0592E;
        }

        /* Responsive adjustments */
        @media (max-width: 810px) {
            .navbar-brand img {
                width: 100px;
                top: 9px;
                left: 10px;
            }

            .navbar-nav {
                margin-left: 120px;
            }

            .dropdown-menu {
                position: static;
                /* Change to static for responsive view */
                float: none;
                margin: 0 auto;
                /* Center align */
                text-align: center;
                /* Center align text */
                top: auto;
                /* Reset top */
                transform: none;
                /* Reset transform */
                display: block;
                /* Ensure dropdown is fully visible */
            }
        }

        @media (max-width: 768px) {
            .navbar-brand img {
                width: 90px;
                top: 9px;
                left: 10px;
            }

            .navbar-nav {
                margin-left: 100px;
            }

            .dropdown-menu {
                position: static;
                /* Change to static for responsive view */
                float: none;
                margin: 0 auto;
                /* Center align */
                text-align: center;
                /* Center align text */
                top: auto;
                /* Reset top */
                transform: none;
                /* Reset transform */
                display: block;
                /* Ensure dropdown is fully visible */
            }
        }

        @media (max-width: 576px) {
            .navbar-brand img {
                width: 70px;
                top: 9px;
                left: 5px;
            }

            .language-switcher {
                margin-left: 0;
                /* Reset margin-left */
                margin-top: 10px;
                /* Add margin-top to separate from other elements */
                justify-content: center;
                /* Center align the switcher */
                width: 100%;
                /* Full width to center align */
                order: 1;
                /* Ensure it appears first in flex order */
            }

            .navbar-nav {
                margin-left: 0;
                text-align: center;
            }

            .navbar-right {
                position: static;
                margin-top: 10px;
                display: flex;
                justify-content: center;
                align-items: center;
                flex-direction: column;
                width: 100%;
                /* Full width to center align */
            }

            .profile-image {
                margin-bottom: 10px;
                /* Separate profile image from dropdown */
            }

            .dropdown-menu {
                position: static;
                /* Change to static for responsive view */
                float: none;
                margin: 0 auto;
                /* Center align */
                text-align: center;
                /* Center align text */
                top: auto;
                /* Reset top */
                transform: none;
                /* Reset transform */
                display: block;
                /* Ensure dropdown is fully visible */
            }
        }

        @media (min-width: 992px) {
            .navbar-right {
                position: absolute;
                right: 20px;
                top: 0px;
            }
        }

        .language-switcher {
            display: flex;
            align-items: center;
            margin-right: 75px;
        }

        .language-switcher img {
            width: 30px;
            cursor: pointer;
        }

        .navbar-right .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            transform: translateX(-50%);
        }

        .navbar-right .dropdown-menu.show {
            color: #F0592E;
        }

        .dropdown-menu {
                position: static;
                /* Change to static for responsive view */
                float: none;
                margin: 0 auto;
                /* Center align */
                text-align: center;
                /* Center align text */
                top: auto;
                /* Reset top */
                transform: none;
                /* Reset transform */
                display: block;
                /* Ensure dropdown is fully visible */
            }
    </style>





</head>

<body>
    <nav id="navbar" class="navbar navbar-expand-lg navbar-light bg-orange">
        <div class="container-fluid">
            <a class="navbar-brand" href="../view/mainpage">
                <img src="../view/assets/img/logo/logo.png" width="65" height="52" alt="Logo">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../view/freqquestion"><?php echo $lang_question ?> </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../view/contact"><?php echo $lang_contact ?></a>
                    </li>
                </ul>
                <div class="language-switcher">
                    <a href="?lang=th"><img src="../view/assets/img/logo/thai.png" alt="<?php echo $lang_th_language ?>"></a>
                    <a href="?lang=en"><img src="../view/assets/img/logo/eng.png" alt="<?php echo $lang_en_language ?>"></a>
                </div>
                <div class="navbar-right">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo $imageBase64; ?>" alt="Profile Image" class="profile-image">
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="../view/profile"><?php echo $lang_profile ?></a></li>
                        <li><a class="dropdown-item" onclick="logout()"><?php echo $lang_logout ?></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <script>
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            const scrollY = window.scrollY;

            if (scrollY > 0) {
                navbar.classList.add('fixed-top');
            } else {
                navbar.classList.remove('fixed-top');
            }
        });
    </script>
    <?php require_once('function/function_logout.php'); ?>
</body>

</html>