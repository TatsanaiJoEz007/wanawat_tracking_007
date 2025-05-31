<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
        gap: 20px;
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
        background-color: #F1F1F1;
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

    /* Google Maps Card - ขนาดใหญ่กว่าปกติ */
    .contact-card.map-card {
        width: 1200px; /* กว้างกว่าการ์ดอื่นๆ */
        max-width: 1200px; /* เพิ่ม max-width */
        height: 600px; /* กำหนดความสูงคงที่ */
        cursor: default; /* ไม่ต้องมี pointer cursor */
        padding: 0; /* ไม่ต้องมี padding */
        overflow: hidden; /* ซ่อนส่วนที่เกิน */
    }

    .contact-card.map-card:hover {
        transform: none; /* ไม่ให้ scale เมื่อ hover */
        background-color: #fff; /* คงสีเดิม */
    }

    .contact-card.map-card iframe {
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 10px;
    }

    /* CSS for the text inputs */
    .contact-card input[type="text"],
    .contact-card input[type="email"],
    .contact-card textarea {
        width: 100%;
        margin: 5px 0;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }

    /* CSS for the submit button */
    .contact-card button[type="submit"] {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .contact-card button[type="submit"]:hover {
        background-color: #0056b3;
    }

    /* Increase the size of the widget box for reporting bugs */
    .contact-card.form-widget {
        width: calc(100% - 40px);
        max-width: 600px;
    }

    /* Responsive Design */
    @media only screen and (max-width: 992px) {
        .contact-card.map-card {
            width: calc(100% - 20px); /* เต็มความกว้างในหน้าจอกลาง */
            max-width: none;
        }
    }

    @media only screen and (max-width: 768px) {
        .contact-card {
            width: calc(50% - 20px);
        }
        
        .contact-card.map-card {
            width: calc(100% - 20px);
            height: 300px; /* ลดความสูงในหน้าจอเล็ก */
        }
    }

    @media only screen and (max-width: 480px) {
        .contact-card {
            width: 100%;
        }
        
        .contact-card.map-card {
            width: 100%;
            height: 250px; /* ลดความสูงในหน้าจอเล็กมาก */
        }
    }
</style>

<!-- Contact Us Section -->
<section id="contact">
    <div class="container">
        <h2><?php echo $lang_contact ?></h2>
        <div class="contact-widget">
            <!-- Facebook Card -->
            <div class="contact-card" onclick="window.open('https://www.facebook.com/WanawatGroup/', '_blank')">
                <div class="contact-icon">
                    <i class="bi bi-facebook"></i>
                </div>
                <div class="contact-info">
                    <span class="contact-label"><?php echo $lang_facebook ?></span>
                    <span class="contact-link">https://www.facebook.com/WanawatGroup/</span>
                </div>
            </div>
            
            <!-- Website Card -->
            <div class="contact-card" onclick="window.open('https://wehome.co.th/', '_blank')">
                <div class="contact-icon">
                    <i class="bi bi-browser-safari"></i>
                </div>
                <div class="contact-info">
                    <span class="contact-label"><?php echo $lang_website ?></span>
                    <span class="contact-link">https://wehome.co.th/</span>
                </div>
            </div>
            
            <!-- Call Card -->
            <div class="contact-card" onclick="confirmCall()">
                <div class="contact-icon">
                    <i class="bi bi-phone"></i>
                </div>
                <div class="contact-info">
                    <span class="contact-label"><?php echo $lang_call ?></span>
                    <span class="contact-link">+6674324697</span>
                </div>
            </div>

            <!-- Google Maps Card - ขนาดใหญ่ -->
            <div class="contact-card map-card">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2063.5866429814596!2d100.55404425108453!3d7.1203017333774605!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x304d2dbfc1128073%3A0x37eed52fc2690e21!2z4Lin4Li14LmC4Lou4LihIOC4muC4tOC4p-C5gOC4lOC4reC4o-C5jCDguKrguLLguILguLLguKrguIfguILguKXguLIgKOC4muC4iOC4gS7guKfguJnguLLguKfguLHguJLguJnguYzguKfguLHguKrguJTguLgp!5e0!3m2!1sth!2sth!4v1714657276800!5m2!1sth!2sth"
                    allowfullscreen="" 
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <!-- Bug Report Form (ถ้าต้องการใช้ในอนาคต)
            <div class="contact-card form-widget" style="display: none;">
                <form id="bug-report-form" method="post">
                    <div class="contact-info">
                        <span class="contact-label"><?php echo $lang_askquestion ?></span>
                        <input type="text" name="name" placeholder="<?php echo $lang_yourname ?>" required>
                        <input type="email" name="email" placeholder="<?php echo $lang_youremail ?>" required>
                        <textarea name="description" placeholder="<?php echo $lang_description ?>" required></textarea>
                        <button type="submit" id="submit-button"><?php echo $lang_submit ?></button>
                    </div>
                </form>
            </div> -->
        </div>
    </div>
</section>
<!-- End Contact Us Section -->

<script>
    // Function to handle form submission with SweetAlert confirmation
    if (document.getElementById("bug-report-form")) {
        document.getElementById("bug-report-form").addEventListener("submit", function (event) {
            event.preventDefault(); // Prevent default form submission

            const isThaiLang = typeof window.currentLang !== 'undefined' ? window.currentLang === 'th' : true;
            
            Swal.fire({
                title: isThaiLang ? "ยืนยัน" : "Confirmation",
                text: isThaiLang ? "คุณแน่ใจหรือไม่ที่จะส่งคำถามหรือรายงานปัญหา?" : "Are you sure you want to submit your question or report?",
                icon: "info",
                showCancelButton: true,
                confirmButtonText: isThaiLang ? "ใช่! ส่งคำถามเลย" : "Yes, submit!",
                cancelButtonText: isThaiLang ? "ยกเลิก" : "Cancel",
                confirmButtonColor: '#FF5722',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user confirms, submit the form
                    document.getElementById("bug-report-form").submit();
                }
            });
        });
    }

    // Function to confirm call action using SweetAlert
    function confirmCall() {
        const isThaiLang = typeof window.currentLang !== 'undefined' ? window.currentLang === 'th' : true;
        
        Swal.fire({
            title: isThaiLang ? "ยืนยันการโทร" : "Confirmation",
            text: isThaiLang ? "คุณต้องการจะโทรหาบริษัท วนาวัฒน์ วัสดุ จำกัด หรือไม่" : "Would you like to call Wanawat Hardware Company Limited?",
            icon: "info",
            showCancelButton: true,
            confirmButtonText: isThaiLang ? "ใช่ โทรเลย" : "Yes, call now",
            cancelButtonText: isThaiLang ? "ไม่" : "No",
            confirmButtonColor: '#FF5722',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "tel:+6674324697";
            }
        });
        return false; // Prevent default action of link click
    }

    // AJAX form submission (ถ้าต้องการใช้ในอนาคต)
    $(document).ready(function() {
        if ($('#bug-report-form').length) {
            $('#bug-report-form').submit(function(event) {
                // Prevent default form submission
                event.preventDefault();
                
                // Serialize form data
                var formData = $(this).serialize();
                
                // Submit form data via AJAX
                $.ajax({
                    type: 'POST',
                    url: 'function/action_contact.php',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Display success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then((result) => {
                                // Reload the page after success
                                location.reload();
                            });
                        } else {
                            // Display error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Display error message
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while submitting the form: ' + error
                        });
                    }
                });
            });
        }
    });
</script>