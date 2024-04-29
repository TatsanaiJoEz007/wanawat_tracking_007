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
                <a class="nav-link" href="#">คำถามที่พบบ่อย?</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#">ติดต่อเรา</a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="dropdown08" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">เข้าสู่ระบบ/สมัครสมาชิก</a>
                <div class="dropdown-menu" aria-labelledby="dropdown08">
                    <a class="dropdown-item" href="../view/login.php">เข้าสู่ระบบ</a>
                    <a class="dropdown-item" href="../view/register.php">สมัครสมาชิก</a>
                </div>
            </li>
        </ul>
    </div>
</nav>
