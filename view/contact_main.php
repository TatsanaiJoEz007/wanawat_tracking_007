<?php
// เริ่ม output buffering และจัดการภาษา
ob_start();
require_once('function/language.php');
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <?php require_once('function/head.php'); ?>
    <style>
        /* Reset และ base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* Main content area */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content {
            flex: 1;
            padding-bottom: 20px;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 9px;
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-track {
            background-color: #f1f1f1;
            border-radius: 10px;
        }

        /* Footer จะอยู่ด้านล่างเสมอ */
        footer {
            margin-top: auto;
        }

        /* เพิ่ม min-height สำหรับ content เพื่อให้ footer อยู่ด้านล่างเสมอ */
        .contact-content {
            min-height: calc(100vh - 300px); /* ลบความสูงของ navbar และ footer */
            padding: 20px 0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .contact-content {
                min-height: calc(100vh - 250px);
                padding: 15px 0;
            }
        }

        @media (max-width: 576px) {
            .contact-content {
                min-height: calc(100vh - 200px);
                padding: 10px 0;
            }
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <!-- Navigation -->
        <?php require_once('function/navbar.php'); ?>

        <!-- Main Content -->
        <div class="content">
            <div class="contact-content">
                <?php require_once('function/contactcard.php'); ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <?php require_once('function/footer.php'); ?>
    </footer>

    <!-- Scripts -->
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // เช็คความสูงของเนื้อหาและปรับ footer
        function adjustFooter() {
            const body = document.body;
            const html = document.documentElement;
            const height = Math.max(body.scrollHeight, body.offsetHeight, 
                                   html.clientHeight, html.scrollHeight, html.offsetHeight);
            
            if (height < window.innerHeight) {
                document.querySelector('footer').style.position = 'fixed';
                document.querySelector('footer').style.bottom = '0';
                document.querySelector('footer').style.width = '100%';
            }
        }

        // เรียกใช้เมื่อโหลดหน้าเสร็จ
        window.addEventListener('load', adjustFooter);
        window.addEventListener('resize', adjustFooter);
        
        // ตั้งค่าภาษาให้ footer สามารถใช้ได้
        <?php if (isset($lang)): ?>
        window.currentLang = '<?php echo $lang; ?>';
        <?php endif; ?>
    </script>
</body>

</html>