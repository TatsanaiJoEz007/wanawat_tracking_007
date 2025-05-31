<?php
// เริ่ม output buffering และจัดการภาษา
ob_start();
require_once('function/language.php');

// ตรวจสอบก่อนเริ่ม session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $lang == 'th' ? 'ติดตามสถานะการจัดส่ง' : 'Track Delivery Status'; ?></title>
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
            background-color: #f8f9fa;
        }

        /* Main content wrapper */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .content-area {
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

        /* Tracking content */
        .tracking-content {
            min-height: calc(100vh - 300px);
            padding: 20px 0;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .tracking-content {
                min-height: calc(100vh - 250px);
                padding: 15px 0;
            }
        }

        @media (max-width: 576px) {
            .tracking-content {
                min-height: calc(100vh - 200px);
                padding: 10px 0;
            }
        }
    </style>
</head>

<body>
    <div class="main-wrapper">
        <!-- Navigation -->
        <?php require_once('function/navindex.php'); ?>

        <!-- Main Content -->
        <div class="content-area">
            <div class="tracking-content">
                <?php require_once('function/stepstatus.php'); ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <?php require_once('function/footer.php'); ?>
    </footer>

    <!-- Scripts -->
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