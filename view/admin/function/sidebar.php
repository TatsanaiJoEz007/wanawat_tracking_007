<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">


<style>
    @import url("https://fonts.googleapis.com/css2?family=Kanit:wght@300&display=swap");

:root {
    --header-height: 3rem;
    --nav-width: 68px;
    --first-color: #F0592E;
    --first-color-light: #AFA5D9;
    --white-color: #F7F6FB;
    --body-font: 'Kanit', sans-serif;
    --normal-font-size: 1rem;
    --z-fixed: 100;
}

*,
::before,
::after {
    box-sizing: border-box;
}

body {
    position: relative;
    margin: var(--header-height) 0 0 0;
    padding: 0 1rem;
    font-family: var(--body-font);
    font-size: var(--normal-font-size);
    transition: .5s;
}

a {
    text-decoration: none;
}

.header {
    width: 100%;
    height: var(--header-height);
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1rem;
    background-color: var(--white-color);
    z-index: var(--z-fixed);
    transition: .5s;
}

.header_toggle {
    color: var(--first-color);
    font-size: 1.5rem;
    cursor: pointer;
}

.header_img {
    width: 35px;
    height: 35px;
    display: flex;
    justify-content: center;
    border-radius: 50%;
    overflow: hidden;
}

.header_img img {
    width: 40px;
}

.l-navbar {
    position: fixed;
    top: 0;
    left: -30%;
    width: var(--nav-width);
    height: 100vh;
    background-color: var(--first-color);
    padding: .5rem 1rem 0 0;
    transition: .5s;
    z-index: var(--z-fixed);
}

.nav {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    overflow: hidden;
}

.nav_logo,
.nav_link {
    display: grid;
    grid-template-columns: max-content max-content;
    align-items: center;
    column-gap: 1rem;
    padding: .5rem 0 .5rem 1.5rem;
}

.nav_logo {
    margin-bottom: 2rem;
}

.nav_logo-icon {
    font-size: 1.25rem;
    color: var(--white-color);
}

.nav_logo-name {
    color: var(--white-color);
    font-weight: 700;
}

.nav_link {
    position: relative;
    color: var(--first-color-light);
    margin-bottom: 1.5rem;
    transition: .3s;
}

.nav_link:hover {
    color: var(--white-color);
}

.nav_icon {
    font-size: 1.25rem;
}

.show {
    left: 0;
}

.body-pd {
    padding-left: calc(var(--nav-width) + 1rem);
}

.active {
    color: var(--white-color);
}

.active::before {
    content: '';
    position: absolute;
    left: 0;
    width: 2px;
    height: 32px;
    background-color: var(--white-color);
}

.height-100 {
    height: 50vh;
}

@media screen and (min-width: 768px) {
    body {
        margin: calc(var(--header-height) + 1rem) 0 0 0;
        padding-left: calc(var(--nav-width) + 2rem);
    }

    .header {
        height: calc(var(--header-height) + 1rem);
        padding: 0 2rem 0 calc(var(--nav-width) + 2rem);
    }

    .header_img {
        width: 40px;
        height: 40px;
    }

    .header_img img {
        width: 45px;
    }

    .l-navbar {
        left: 0;
        padding: 1rem 1rem 0 0;
    }

    .show {
        width: calc(var(--nav-width) + 156px);
    }

    .body-pd {
        padding-left: calc(var(--nav-width) + 188px);
    }
}

.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #fff;
    min-width: 160px;
    box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.1);
    z-index: 1;
    border-radius: 8px;
    padding: 8px 0;
    opacity: 0;
    pointer-events: none;
    transform: translateY(-10px);
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.dropdown-content a {
    color: #333;
    padding: 10px 20px;
    display: block;
    transition: 0.3s;
}

.dropdown-content a:hover {
    background-color: #f2f2f2;
}

.dropdown:hover .dropdown-content {
    display: block;
    opacity: 1;
    pointer-events: auto;
    transform: translateY(0);
}


.nav_link.collapsed .dropdown-content {
    opacity: 0;
    pointer-events: none;
    transform: scaleY(0);
}

.nav_link.collapsed + .dropdown:hover .dropdown-content {
    opacity: 1;
    pointer-events: auto;
    transform: scaleY(1);
}
</style>

<body id="body-pd">
    <header class="header" id="header">
        <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
        <div class="header_img"> <img src="https://i.imgur.com/hczKIze.jpg" alt=""> </div>
    </header>
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div> <a href="" class="nav_logo">
                    <i class='bx bx-angry nav_logo-icon'></i>
                    <span class="nav_logo-name">Admin</span>
                </a>

                <div class="nav_list"> <a href="../admin/Dashboard.php" class="nav_link active"> <i
                            class='bx bx-grid-alt nav_icon'></i>
                        <span class="nav_name">Dashboard</span> </a>
                    <a href="../admin/Users.php" class="nav_link"> <i class='bx bx-user nav_icon'></i> <span
                            class="nav_name">Users</span> </a>
                    <a href="../admin/ImportCSV.php" class="nav_link"> <i class='bx bxs-file-import nav_icon'></i> <span
                            class="nav_name">Import CSV</span> </a>

                    <div class="dropdown">
                        <a href="#" class="nav_link">
                            <i class='bx bx-cog nav_icon'></i>
                            <span class="nav_name">Manage Web</span>
                        </a>
                        <div class="dropdown-content">
                            <a href="../admin/banner.php">Banner</a>
                            <a href="../admin/contact.php">Contact</a>
                            <a href="../admin/question.php">Question</a>
                        </div>
                    </div>

                </div>
            </div> <a href="javascript:void(0)" onclick="logout()" class="nav_link">
                <i class='bx bx-log-out nav_icon'></i>
                <span class="nav_name">Sign Out</span>
            </a>

        </nav>
    </div>

    <?php
    require_once ('../../view/admin/function/logout.php');
    ?>

    <!-- Main Content -->
    <div class="bg-white">
        <!-- Your main content goes here -->
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://fastly.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    headerpd = document.getElementById(headerId);

                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener('click', () => {
                        nav.classList.toggle('show');
                        toggle.classList.toggle('bx-x');
                        bodypd.classList.toggle('body-pd');
                        headerpd.classList.toggle('body-pd');
                    });

                    const navLinks = nav.querySelectorAll('.nav_link');
                    navLinks.forEach(link => {
                        link.addEventListener('click', () => {
                            if (!nav.classList.contains('show')) {
                                link.classList.toggle('collapsed');
                            }
                        });
                    });
                }
            };

            showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');

            const linkColor = document.querySelectorAll('.nav_link');

            function colorLink() {
                linkColor.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }

            linkColor.forEach(l => l.addEventListener('click', colorLink));

            const handleDropdownCollapse = () => {
                const navLinks = document.querySelectorAll('.nav_link');
                const sidebarToggle = document.getElementById('header-toggle');

                navLinks.forEach(link => {
                    link.addEventListener('click', () => {
                        if (sidebarToggle.classList.contains('bx-x')) {
                            link.classList.toggle('collapsed');
                        }
                    });
                });
            };

            handleDropdownCollapse();
        });
    </script>
</body>