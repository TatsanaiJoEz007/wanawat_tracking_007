<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<style>
    footer {
        position: relative;
        background: linear-gradient(45deg, #F0592E, #FF4B2B);
        color: #fff;
        padding: 50px 0px;
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

    .wedev-logo {
        width: 55px;
        height: 50px;
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
        text-shadow: 0 0 5px rgba(255, 255, 255, 0.5);
        margin: 0;
    }

    .footer-text-center {
        text-align: center;
    }

    @media (max-width: 768px) {
        .footer-content {
            text-align: center;
            flex-direction: column;
            gap: 20px;
        }

        .wedev-logo {
            margin: 0;
        }
    }

    @media (max-width: 576px) {
        .footer-content {
            gap: 15px;
        }
    }
</style>

<footer>
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <img src="../view/assets/img/logo/logo.png" alt="Company Logo">
            </div>
            
            <img src="../view/assets/img/wedev.png" class="wedev-logo" alt="WEDEV Logo">
            
            <div class="footer-text-center">
                <div class="powered-by">
                    <span>พัฒนาโดย:</span> <a href="https://www.facebook.com/profile.php?id=61558770879804">WE.DEV</a>
                </div>
                <div class="footer-text">
                    <p>&copy; 2024 Wanawat Hardware Company Limited. สงวนสิทธิ์ทุกประการ.</p>
                </div>
            </div>
            
            <div class="social-icons">
                <a href="https://www.facebook.com/WeHomeOnline" target="_blank"><i class="fab fa-facebook-f"></i></a>
                <a href="https://maps.app.goo.gl/d4iug4bQ4Z5tMJAC9" target="_blank"><i class="fas fa-map-marker-alt"></i></a>
                <a href="https://wehome.co.th" target="_blank"><i class="fas fa-globe"></i></a>
                <a href="#" onclick="confirmCall()"><i class="fas fa-phone-alt"></i></a>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
function confirmCall() {
    // ตรวจสอบว่ามีตัวแปร lang หรือไม่
    const isThaiLang = typeof window.currentLang !== 'undefined' ? window.currentLang === 'th' : 
                      (typeof PHP_LANG !== 'undefined' ? PHP_LANG === 'th' : true);
    
    const confirmTitle = isThaiLang ? 'ยืนยันการโทร' : 'Confirmation';
    const confirmText = isThaiLang ? 'คุณต้องการโทรไปที่ WeHome Co., Ltd. หรือไม่?' : 'Would you like to call WeHome Co., Ltd.?';
    const confirmButton = isThaiLang ? 'โทรเลย' : 'Yes, call now';
    const cancelButton = isThaiLang ? 'ยกเลิก' : 'No';

    Swal.fire({
        title: confirmTitle,
        text: confirmText,
        icon: "info",
        showCancelButton: true,
        confirmButtonText: confirmButton,
        cancelButtonText: cancelButton,
        confirmButtonColor: '#FF5722',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "tel:+6674324697";
        }
    });
    return false;
}

// ตั้งค่าภาษาจาก PHP
// window.currentLang = 'th'; // ตัวอย่างการตั้งค่า
</script>