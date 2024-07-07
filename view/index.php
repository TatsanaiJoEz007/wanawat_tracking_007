<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <?php require_once('function/head.php'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <style>
        .modal {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            color: black;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: slideInUp 0.5s ease-out;
        }

        .modal-content {
            max-width: 600px;
            width: 100%;
            padding: 20px;
            background-color: #fff;
            color: #000;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes slideInUp {
            from {
                transform: translateY(100%);
            }
            to {
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .modal h2 {
            margin-top: 0;
            color: #333;
            font-size: 24px;
        }

        .modal p {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
        }

        .modal button {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .modal button#acceptCookie {
            background-color: #4CAF50;
            color: white;
        }

        .modal button#acceptCookie:hover {
            background-color: #45a049;
        }

        .modal button#rejectCookie {
            background-color: #f44336;
            color: white;
        }

        .modal button#rejectCookie:hover {
            background-color: #e63929;
        }

        .modal .cookie-emoji {
            font-size: 48px;
            margin-bottom: 10px;
            animation: bounce 1s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(-10px);
            }
        }

        ::-webkit-scrollbar {
            width: 9px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            border-radius: 10px;
        }

        .home-section {
            max-height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
    </style>
</head>

<body>
    <div>
        <?php require_once('function/navbar.php'); ?>
        <?php require_once('function/banner.php'); ?>
        <br>
        <br>
        <br>
        <?php require_once('function/tracking.php'); ?>
        <br>
        <br>
        <br>
        <br>
        <br>
        <br>
    </div>

    <!-- Cookie Consent Modal -->
    <div id="cookieModal" class="modal">
        <div class="modal-content">
            <div class="cookie-emoji">🍪</div>
            <h2>ยอมรับคุกกี้</h2>
            <p>เราใช้คุกกี้เพื่อให้แน่ใจว่าคุณได้รับประสบการณ์ที่ดีที่สุดบนเว็บไซต์ของเรา</p>
            <button id="acceptCookie" onclick="acceptCookie()">ยอมรับ</button>
            <button id="rejectCookie" onclick="rejectCookie()">ไม่ยอมรับ</button>
        </div>
    </div>

    <footer>
        <?php require_once('function/footer.php'); ?>
    </footer>

    <script>
        window.onload = function () {
            var cookieAccepted = getCookie("cookieAccepted");
            if (!cookieAccepted) {
                var modal = document.getElementById("cookieModal");
                modal.style.display = "flex";
            }
        };

        function setCookie(cname, cvalue, exdays) {
            var d = new Date();
            d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
            var expires = "expires=" + d.toUTCString();
            document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
        }

        function getCookie(cname) {
            var name = cname + "=";
            var decodedCookie = decodeURIComponent(document.cookie);
            var ca = decodedCookie.split(';');
            for (var i = 0; i < ca.length; i++) {
                var c = ca[i];
                while (c.charAt(0) == ' ') {
                    c = c.substring(1);
                }
                if (c.indexOf(name) == 0) {
                    return c.substring(name.length, c.length);
                }
            }
            return "";
        }

        function acceptCookie() {
            setCookie("cookieAccepted", "true", 365);
            var modal = document.getElementById("cookieModal");
            modal.style.display = "none";
        }

        function rejectCookie() {
            swal({
                title: 'แจ้งเตือน',
                text: 'คุณจะไม่ได้รับประสบการณ์การใช้งานฟังก์ชั่นในการทำงานต่างๆจากหน้าเว็ป',
                icon: 'warning',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed) {
                    var modal = document.getElementById("cookieModal");
                    modal.style.display = "flex";
                }
            });
        }
    </script>
</body>

</html>