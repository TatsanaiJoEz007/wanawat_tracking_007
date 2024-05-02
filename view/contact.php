<!DOCTYPE html>
<html lang="en">

<head>
    <?php require_once('function/head.php'); ?>

    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 50px;
        }

        .contact-widget {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .contact-card {
            width: calc(30% - 20px);
            max-width: 300px;
            margin-bottom: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out, background-color 0.5s ease;
            cursor: pointer;
        }

        .contact-card:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            background-color: #feede8;
            /* Change to desired hover background color */
        }


        .contact-card .contact-icon {
            text-align: center;
            margin-top: 20px;
        }

        .contact-card .contact-icon i {
            font-size: 48px;
            color: #007bff;
        }

        .contact-card .contact-info {
            text-align: center;
            padding: 20px 10px;
        }

        .contact-card .contact-label {
            font-size: 18px;
            color: #333;
            font-weight: bold;
        }

        .contact-card .contact-link {
            font-size: 14px;
            color: #666;
        }

        .contact-card .contact-link:hover {
            color: #007bff;
        }

        @media only screen and (max-width: 768px) {
            .contact-card {
                width: calc(50% - 20px);
            }
        }

        @media only screen and (max-width: 480px) {
            .contact-card {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <?php require_once('function/navindex.php'); ?>

    <!-- Contact Us Section -->
    <br><br>
    <section id="contact">
        <div class="container">
            <h2>ติดต่อเรา</h2>
            <div class="contact-widget">
                <!-- Facebook Card -->
                <div class="contact-card" onclick="window.open('https://www.facebook.com/WanawatGroup/', '_blank')">
                    <div class="contact-icon">
                        <i class="bi bi-facebook"></i>
                    </div>
                    <div class="contact-info">
                        <span class="contact-label">Facebook</span>
                        <span class="contact-link">https://www.facebook.com/WanawatGroup/</span>
                    </div>
                </div>
                <!-- Email Card -->
                <div class="contact-card" onclick="window.open('https://wehome.co.th/')">
                    <div class="contact-icon">
                        <i class="bi bi-browser-safari"></i>
                    </div>
                    <div class="contact-info">
                        <span class="contact-label">Website</span>
                        <span class="contact-link">https://wehome.co.th/</span>
                    </div>
                </div>
                <!-- Call Card -->
                <div class="contact-card" onclick="confirmCall()">
                    <div class="contact-icon">
                        <i class="bi bi-phone"></i>
                    </div>
                    <div class="contact-info">
                        <span class="contact-label">Call</span>
                        <span class="contact-link">+6674324697</span>
                    </div>
                </div>
                <!-- Google Maps Card -->
                <div class="contact-card">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2063.5866429814596!2d100.55404425108453!3d7.1203017333774605!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x304d2dbfc1128073%3A0x37eed52fc2690e21!2z4Lin4Li14LmC4Liu4LihIOC4muC4tOC4p-C5gOC4lOC4reC4o-C5jCDguKrguLLguILguLLguKrguIfguILguKXguLIgKOC4muC4iOC4gS7guKfguJnguLLguKfguLHguJLguJnguYzguKfguLHguKrguJTguLgp!5e0!3m2!1sth!2sth!4v1714657276800!5m2!1sth!2sth" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>
    <!-- End Contact Us Section -->

    <footer>
        <?php require_once('function/footer.php'); ?>
    </footer>

    <script>
        // Function to confirm call action using SweetAlert
        function confirmCall() {
            Swal.fire({
                title: "Confirmation",
                text: "Would you like to make a call?",
                icon: "info",
                showCancelButton: true,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "tel:+6674324697";
                }
            });
            return false; // Prevent default action of link click
        }
    </script>
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>