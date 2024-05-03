<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- Font Awesome for icons -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet"> <!-- Bootstrap CSS -->
    <style>
        footer {
            position: relative;
            background-color: #F0592E;
            color: #fff;
            padding: 50px 0;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-logo img {
            max-width: 100px;
        }

        .social-icons a {
            color: #fff;
            font-size: 24px;
            margin-right: 15px;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            transform: scale(1.2);
            color: #ff5722;
        }

        .powered-by a {
            color: #fff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .powered-by a:hover {
            color: #ff5722;
        }

        .footer-text {
            font-size: 14px;
            margin-top: 20px;
        }

    </style>
</head>


<body>
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="../view/assets/img/logo/logo.png" alt="Company Logo">
                </div>
                <div class="social-icons">
                    <a href="https://www.facebook.com/WeHomeOnline" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://maps.app.goo.gl/d4iug4bQ4Z5tMJAC9" target="_blank"><i class="fas fa-map-marker-alt"></i></a>
                    <a href="https://wehome.co.th" target="_blank"><i class="fas fa-globe"></i></a>
                    <a href="#" onclick="confirmCall()"><i class="fas fa-phone-alt"></i></a>
                </div>
            </div> <br> <br>
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
</html>