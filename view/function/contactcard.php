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
        /* Ensure padding is included in the width */
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
        /* Adjust width to fit the entire width of the container */
        max-width: 600px;
        /* Limit the maximum width to prevent it from becoming too wide */
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
            <!-- Email Card -->
            <div class="contact-card" onclick="window.open('https://wehome.co.th/')">
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

            <!-- Bug Report Form
            <div class="contact-card form-widget" hidden>
                <form id="bug-report-form" method="post">
                    <div class="contact-info" >
                        <span class="contact-label" ><?php echo $lang_askquestion ?></span>
                        <input type="text" name="name" placeholder="<?php echo $lang_yourname ?>" required >
                        <input type="email" name="email" placeholder="<?php echo $lang_youremail ?>" required >
                        <textarea name="description" placeholder="<?php echo $lang_description ?>" required ></textarea>
                        <button type="submit" id="submit-button" disabled hidden><?php echo $lang_submit ?></button>
                    </div>
                </form>
            </div> -->

            <!-- Google Maps Card -->
            <div class="contact-card">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2063.5866429814596!2d100.55404425108453!3d7.1203017333774605!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x304d2dbfc1128073%3A0x37eed52fc2690e21!2z4Lin4Li14LmC4Liu4LihIOC4muC4tOC4p-C5gOC4lOC4reC4o-C5jCDguKrguLLguILguLLguKrguIfguILguKXguLIgKOC4muC4iOC4gS7guKfguJnguLLguKfguLHguJLguJnguYzguKfguLHguKrguJTguLgp!5e0!3m2!1sth!2sth!4v1714657276800!5m2!1sth!2sth"
                    width="100%" height="250px" style="border:3;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</section>
<!-- End Contact Us Section -->

<!-- End Contact Us Section -->

<script>
    // Function to handle form submission with SweetAlert confirmation
    document.getElementById("bug-report-form").addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent default form submission

        Swal.fire({
            title: "Confirmation",
            text: "คุณแน่ใจหรือไม่ที่จะส่งคำถามหรือรายงานปัญหา?",
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "ใช่! ส่งคำถามเลย",
            cancelButtonText: "ยกเลิก",
        }).then((result) => {
            if (result.isConfirmed) {
                // If user confirms, submit the form
                document.getElementById("bug-report-form").submit();
            }
        });
    });

    // Function to confirm call action using SweetAlert
    function confirmCall() {
        Swal.fire({
            title: "Confirmation",
            text: "คุณต้องการจะโทรหาบริษัท วนาวัฒน์ วัสดุ จำกัด หรือไม่",
            icon: "info",
            showCancelButton: true,
            confirmButtonText: "ใช่ โทรเลย",
            cancelButtonText: "ไม่",
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "tel:+6674324697";
            }
        });
        return false; // Prevent default action of link click
    }

    $(document).ready(function() {
        $('#bug-report-form').submit(function(event) {
            // Prevent default form submission
            event.preventDefault();
            
            // Serialize form data
            var formData = $(this).serialize();
            
            // Submit form data via AJAX
            $.ajax({
                type: 'POST',
                url: 'function/action_contact.php', // Change the URL to your PHP script
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
    });


</script>