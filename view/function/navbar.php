<meta name="viewport" content="width=device-width, initial-scale=1">

<?php
require_once('language.php');
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}
?>

<head>
  <style>
    @keyframes slideDown {
      from {
        transform: translateY(-100%);
      }

      to {
        transform: translateY(0);
      }
    }

    .navbar.navbar-expand-lg.navbar-light .navbar-nav .nav-link {
      color: white !important;
    }

    /* Style for language switcher icons */
    .language-switcher {
      display: auto;
      align-items: center;
      margin-left: 1250px;
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
      /* Change background color for fixed state */

    }

    .navbar-brand img {
      position: absolute;
      /* Position the logo absolutely */
      left: 5%;

      margin-top: 10px;
      /* Center the logo horizontally */
      transform: translate(-50%, 0);
      /* Center the logo vertically */
      width: 120px;
      /* Adjust the logo width as needed */
      height: auto;
      /* Maintain aspect ratio */
      top: -10px;
      /* Position the logo above the navbar */
      z-index: 9999;

      margin-right: 30px;
    }

    .navbar-nav {
      margin-left: 120px;
      /* Make space for the logo */
    }


    /* Responsive logo width */
    
    @media (max-width: 810px) {
      .navbar-brand img {
        width: 100px;
        left: 70px;
        top: 5px;
      }
      .language-switcher {
        margin-left: 0;
      }
      
    }

    /* Responsive logo width */
    @media (max-width: 768px) {
      .navbar-brand img {
        width: 90px;
        left: 70px;
        top: 5px;
      }

      .language-switcher {
        margin-left: 0;
      }
    }

    @media (max-width: 576px) {
      .navbar-brand img {
        width: 90px;
        left: 70px;
        top: 5px;
      }

      .language-switcher {
        margin-left: 0;
      }
    }
  </style>
</head>

<body>

  <nav id="navbar" class="navbar navbar-expand-lg navbar-light bg-orange">
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
            <a class="nav-link active" aria-current="page" href="../view/freqquestion_main"><?php echo $lang_question ?> ?</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="../view/contact_main"><?php echo $lang_contact ?></a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <?php echo $lang_login ?> / <?php echo $lang_register ?>
            </a>

            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <li><a class="dropdown-item" href="../view/login"><?php echo $lang_login ?> </a></li>
              <li><a class="dropdown-item" href="../view/register"><?php echo $lang_register ?></a></li>  
            </ul>
          </li>
          <div class="language-switcher ">
              <a href="?lang=th"><img src="../view/assets/img/logo/thai.png" alt="<?php echo $lang_th_language ?>"></a>
              <a href="?lang=en"><img src="../view/assets/img/logo/eng.png" alt="<?php echo $lang_en_language ?>"></a>
        </div>
        </ul>
      </div>
      
    </div>
  </nav>

  <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta1/js/bootstrap.bundle.min.js"></script>
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

  </body>