<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../view/assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Kanit', sans-serif;
        font-weight: bold; /* Changed from font-style to font-weight */
    }
    .navbar.navbar-expand-lg.navbar-light .navbar-nav .nav-link {
    color: white !important;    
    }
</style>


<nav class="navbar navbar-expand-lg navbar-light bg-orange"> <!-- Check or define bg-orange -->
        <div class="container-fluid">
            <a class="logo" href="../view/index.php"><img src="../view/assets/img/logo/logo.png" width="65" height="52"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../view/freqquestion.php">คำถามที่พบบ่อย ?</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../view/contact.php">ติดต่อเรา</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            เข้าสู่ระบบ / ลงทะเบียน
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="../view/login.php">เข้าสู่ระบบ</a></li>
                            <li><a class="dropdown-item" href="../view/register.php">ลงทะเบียน</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
