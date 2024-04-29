<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="../view/assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Kanit', sans-serif;
        font-style: bold;
    }
    .navbar.navbar-dark .navbar-nav .nav-link {
        color: white !important;  /* Ensures override of other styles */
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-orange">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarsExample08" aria-controls="navbarsExample08" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <a href="../view/index.php" class="logo"><img class="logo" src="../view/assets/img/logo/logo.png" alt="Logo Image" width="65" height="52"></a>
    <div class="collapse navbar-collapse justify-content-md-center" id="navbarsExample08">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="../view/freqquestion.php">คำถามที่พบบ่อย?</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../view/contact.php">ติดต่อเรา</a>
            </li>
            <!-- Dropdown menu -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    เข้าสู่ระบบ/ลงทะเบียน
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="../view/login.php">เข้าสู่ระบบ</a></li>
                    <li><a class="dropdown-item" href="../view/register.php">ลงทะเบียน</a></li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

