<?php require_once('th_eng.php'); ?>
<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta1/css/bootstrap.min.css">
    <style>
        .navbar.navbar-expand-lg.navbar-light .navbar-nav .nav-link {
            color: white !important;    
        }
        /* Style for language switcher icons */
        .language-switcher {
            display: flex;
            align-items: center;
        }
        .language-switcher img {
            width: 30px;
            margin-left: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>




<nav class="navbar navbar-expand-lg navbar-light bg-orange">
    <div class="container-fluid">
        <a class="navbar-brand" href="../view/index">
            <img src="../view/assets/img/logo/logo.png" width="65" height="52" alt="Logo">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link active" aria-current="page" href="../view/freqquestion_main">คำถามที่พบบ่อย ?</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../view/contact_main">ติดต่อเรา</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo $lang_login ?>  / <?php echo $lang_register ?>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                        <li><a class="dropdown-item" href="../view/login">เข้าสู่ระบบ </a></li>
                        <li><a class="dropdown-item" href="../view/register">ลงทะเบียน</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- Language Switcher -->
        <div class="language-switcher">
        
             
            <a href="index.php?lang=th"><img src="../view/assets/img/logo/thai.png" alt="<?php echo $lang_th_language ?>"></a>

            
            <a href="index.php?lang=en"><img src="../view/assets/img/logo/eng.png" alt="<?php echo $lang_en_language ?>"></a>

        </div>
    </div>
</nav>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta1/js/bootstrap.bundle.min.js"></script>
</body>