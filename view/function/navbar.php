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
                        เข้าสู่ระบบ / ลงทะเบียน
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
        <?php require_once('function/head.php'); ?>
            <img src="../view/assets/img/logo/thai.png" alt="Thai Flag"  <?php echo $lang_th_language ?> >
            <img src="../view/assets/img/logo/eng.png" alt="British Flag"  <?php echo $lang_en_language ?>  >
        </div>
    </div>
</nav>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta1/js/bootstrap.bundle.min.js"></script>

<script>
    function changeLanguage(languageCode) {
        // Implement logic to change language
        // For example, you can reload the page with a query parameter indicating the selected language
        // window.location.href = `../view/index?lang=${languageCode}`;
    }
</script>

</body>