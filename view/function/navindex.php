<?php
require_once('language.php');
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

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

        .navbar .navbar-nav .nav-link {
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

        .language-switcher {
            display: flex;
            align-items: center;
        }

        .language-switcher img {
            width: 30px;
            margin-left: 10px;
            cursor: pointer;
            margin-top: 5px;
        }

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

        @media (max-width: 810px) {
            .navbar-brand img {
                width: 70px;
                top: 9px;
                left: 5px;
            }

            .language-switcher {
                margin-left: -8px;
                margin-top: 10px;
                justify-content: center;
                width: 100%;
                order: 1;
            }

            .navbar-nav {
                margin-left: 0;
                text-align: center;
            }

            .profile-dropdown {
                max-width: fit-content;
                margin-left: 367px;
                margin-right: auto;
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
        }

        @media (max-width: 576px) {
            .navbar-brand img {
                width: 70px;
                top: 9px;
                left: 5px;
            }

            .language-switcher {
                margin-left: 0;
                margin-top: 10px;
                justify-content: center;
                width: 100%;
                order: 1;
            }

            .navbar-nav {
                margin-left: 0;
                text-align: center;
            }

            .profile-dropdown {
                max-width: fit-content;
                margin-left: 173px;
                margin-right: auto;
            }
        }

        .profile-dropdown {
            position: relative;
            margin-right: 10px;
        }

        .profile-dropdown .profile-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }

        .profile-dropdown .dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 50px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            width: 150px;
        }

        .profile-dropdown .dropdown-menu a {
            display: block;
            padding: 10px 20px;
            color: #333;
            text-decoration: none;
        }

        .profile-dropdown .dropdown-menu a:hover {
            background-color: #f0f0f0;
        }

        .profile-dropdown.active .dropdown-menu {
            display: block;
        }

        .language-switcher {
            display: flex;
            align-items: center;
            margin-right: -20;
        }

        .language-switcher img {
            width: 30px;
            cursor: pointer;
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
                <div class="profile-dropdown">
                    <img src="<?php echo $imageBase64; ?>" alt="Profile" class="profile-icon" onclick="toggleDropdown(event)">
                    <div class="dropdown-menu">
                        <a href="profile.php"><?php echo $lang_profile ?></a>
                        <a onclick="logout()"><?php echo $lang_logout?></a>
                    </div>
                </div>
                <div class="language-switcher">
                    <img src="../view/assets/img/logo/thai.png" alt="<?php echo $lang_th_language ?>" onclick="switchLanguage('th')">
                    <img src="../view/assets/img/logo/eng.png" alt="<?php echo $lang_en_language ?>" onclick="switchLanguage('en')">
                </div>
            </div>
        </div>
    </nav>

    <script>

        function switchLanguage(lang) {
            $.post('function/language.php', { lang: lang }, function(data) {
                if (data.success) {
                location.reload();
                }
            }, 'json');
            }

        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            const scrollY = window.scrollY;

            if (scrollY > 0) {
                navbar.classList.add('fixed-top');
            } else {
                navbar.classList.remove('fixed-top');
            }
        });

        function toggleDropdown(event) {
            event.stopPropagation();
            const dropdown = document.querySelector('.profile-dropdown');
            const menu = dropdown.querySelector('.dropdown-menu');
            dropdown.classList.toggle('active');

            // Adjust dropdown position if it overflows the viewport
            const rect = menu.getBoundingClientRect();
            if (rect.right > window.innerWidth) {
                menu.style.right = '0';
                menu.style.left = 'auto';
            } else {
                menu.style.right = '';
                menu.style.left = '';
            }
        }

        document.addEventListener('click', function(e) {
            const dropdown = document.querySelector('.profile-dropdown');
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    </script>

    <?php require_once('function/function_logout.php'); ?>
</body>

</html>