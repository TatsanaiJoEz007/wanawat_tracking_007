<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome for icons -->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
<style>
    footer {
        position: relative;
        background: linear-gradient(45deg, #F0592E, #FF4B2B);
        color: #fff;
        padding: 50px 0;
        box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.3);
    }

    .footer-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        animation: slideInUp 1s ease-out;
    }

    @keyframes slideInUp {
        from {
            transform: translateY(100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .footer-logo img {
        max-width: 120px;
        transition: transform 0.3s ease, filter 0.3s ease;
    }

    .social-icons a {
        color: #fff;
        font-size: 26px;
        margin-right: 20px;
        transition: transform 0.3s ease, color 0.3s ease, filter 0.3s ease;
    }

    .social-icons a:hover {
        color: #ffe600;
        transform: scale(1.2);
    }

    .powered-by a {
        color: #fff;
        text-decoration: none;
        font-weight: bold;
        font-size: 16px;
        transition: color 0.3s ease, text-shadow 0.3s ease;
        text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
    }

    .powered-by a:hover {
        color: #ffe600;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.8);
    }

    .footer-text {
        font-size: 16px;
        margin-top: 20px;
        text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
    }

    .wedev-logo {
        width: 55px;
        height: 50px;
        /* Adjust size as needed */
        margin-top: 20px;

        margin-right: 700px;
        /* Add margin to separate from other elements */
    }

    @media (max-width: 576px) {
        .footer-content {
            text-align: center;
            flex-direction: column;
        }

        .social-icons {
            margin-top: 20px;
        }

        .wedev-logo {
            margin-top: 20px;
            /* Adjust margin for smaller screens */
        }
    }
</style>

<body>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="../view/assets/img/logo/logo.png" alt="Company Logo">
                </div>
                <img src="../view/assets/img/wedev.png" class="wedev-logo" alt="WEDEV Logo"> <!-- Move WEDEV logo here -->
                <div class="social-icons">
                    <a href="https://www.facebook.com/WeHomeOnline" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://maps.app.goo.gl/d4iug4bQ4Z5tMJAC9" target="_blank"><i class="fas fa-map-marker-alt"></i></a>
                    <a href="https://wehome.co.th" target="_blank"><i class="fas fa-globe"></i></a>
                    <a href="#" onclick="confirmCall()"><i class="fas fa-phone-alt"></i></a>
                </div>
            </div>
            <div class="powered-by text-center">
                <p>Powered By: <a href="https://www.facebook.com/profile.php?id=61558770879804">WE.DEV</a></p>
            </div>
            <div class="footer-text text-center">
                <p>&copy; 2024 Wanawat Hardware Company Limited. All rights reserved.</p>
            </div>
        </div>
    </footer>


    <!-- Bootstrap JS (optional) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        function confirmCall() {
            Swal.fire({
                title: "Confirmation",
                text: "Would you like to call WeHome Co., Ltd.?",
                icon: "info",
                showCancelButton: true,
                confirmButtonText: "Yes, call now",
                cancelButtonText: "No",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "tel:+6674324697";
                }
            });
            return false;
        }
    </script>
</body>