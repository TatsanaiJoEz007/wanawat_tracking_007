<style>
.navbar.navbar-expand-lg.navbar-light .navbar-nav .nav-link {
    color: white !important;
}
.profile-image {
    width: 50px; /* Adjust size as needed */
    height: 50px;
    border-radius: 50%;
    border: 2px solid white;
    margin-top: 5px; /* Adjust margin as needed */
}
.navbar-right {
    position: absolute;
    right: 20px; /* Right padding when large screen */
    top: 8px; /* Vertical alignment */
    
}

/* Media query for devices with a max-width of 991px (where Bootstrap's navbar toggler is active) */
@media (max-width: 991px) {
    .navbar-right {
        position: static; /* Change from absolute to static */
        margin-top: 10px; /* Add top margin for mobile */
        display: flex; /* Use flexbox */
        justify-content: center; /* Center horizontally */
        align-items: center; /* Center vertically */
    }
    .navbar-toggler {
        clear: both; /* Clear floats */
        margin-top: 10px; /* Ensure space above the toggler */
    }
    .dropdown-menu {
        position: absolute; /* Make dropdown expand within the nav */
    }
}

/* For larger screens */
@media (min-width: 992px) {
    .navbar-right {
        position: absolute;
        right: 20px;
        top: 8px;
    }
}


</style>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-orange" style="position: relative;">
        <div class="container-fluid">
            <a class="navbar-brand" href="../view/mainpage.php">
                <img src="../view/assets/img/logo/logo.png" width="65" height="52" alt="Logo">
            </a>
       
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="../view/freqquestion.php">คำถามที่พบบ่อย ?</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../view/contact.php">ติดต่อเรา</a>
                    </li>
                </ul>
                <div class="navbar-right">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="../view/assets/img/logo/mascot.png" alt="Profile Image" class="profile-image">
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                        <li><a class="dropdown-item" href="#">View Profile</a></li>
                        <li>
                            <button type="logout" class="btn btn-link" onclick="logout()">
                            <a class="dropdown-item" href="#">Logout</a> </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.0.0-beta1/js/bootstrap.min.js"></script>
    <?php require_once('function/function_logout.php'); ?>
</body>
