<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .tracking-input-group {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        margin-top: 50px;
        background: #fff;
        border-radius: 50px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        height: 80px;
        padding: 0 20px;
        opacity: 0;
        transform: translateY(-50px);
        transition: all 0.5s ease;
    }

    .tracking-input-group.show {
        opacity: 1;
        transform: translateY(0);
    }

    .track-button {
        border: none;
        height: 60px;
        background-color: #F0592E;
        color: white;
        padding: 0 20px;
        border-radius: 25px;
        font-weight: 700;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .track-button {
        cursor: pointer;
        display: inline-block;
        position: relative;
        transition: 0.5s;
    }

    .track-button:after {
        content: '\f0d1';
        font-family: 'Font Awesome 5 Free';
        position: absolute;
        opacity: 0;
        top: 17px;
        right: -20px;
        transition: 0.5s;
    }

    .track-button:hover {
        padding-right: 30px;
        padding-left: 8px;
    }

    .track-button:hover:after {
        opacity: 1;
        right: 3px;
    }

    .tracking-input {
        border: none;
        height: 70px;
        outline: none;
        border-radius: 50px;
        padding: 0 10px;
        flex-grow: 1;
        margin-right: 10px;
    }

    .mascot {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        left: 30px;
    }

    @media (max-width: 768px) {
        .tracking-input-group {
            flex-direction: column;
            align-items: center;
            padding: 20px;
            height: auto;
        }

        .tracking-input,
        .track-button {
            width: 100%;
            margin: 10px 0;
        }

        .mascot {
            width: 70px;
            height: 70px;
            margin-bottom: -30px;
        }
    }
</style>
</head>

<body>
    <?php require_once('config/connect.php'); ?>

    <div class="container text-center">
        <div class="tracking-input-group">
            <img src="../view/assets/img/logo/mascot.png" alt="Mascot" class="mascot">
            <p>&nbsp; &nbsp;</p>
            <input type="text" class="form-control tracking-input" id="trackingIdInput" name="trackingId" placeholder="<?php echo htmlspecialchars($lang_track); ?> (Ex. ED#############)" required>
            <button class="track-button">Track</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const trackingInputGroup = document.querySelector('.tracking-input-group');
            setTimeout(() => {
                trackingInputGroup.classList.add('show');
            }, 500); // Delay in milliseconds

            document.querySelector('.track-button').addEventListener('click', function() {
                var trackingId = document.getElementById('trackingIdInput').value;

                // Using AJAX to send trackingId to search_tracking.php
                var xhr = new XMLHttpRequest();
                xhr.open('POST', 'function/search_tracking.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        var response = JSON.parse(xhr.responseText.trim());
                        if (response.status === 'match') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Tracking ID:',
                                text: trackingId,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.href = `../view/tracking_mainpage.php?trackingId=${encodeURIComponent(trackingId)}`; // Redirect with trackingId as parameter
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ไม่พบเลขพัสดุ',
                                text: 'กรุณาตรวจสอบเลข Tracking Number อีกครั้ง'
                            });
                        }
                    }
                };
                xhr.send('trackingId=' + encodeURIComponent(trackingId));
            });
        });
    </script>
</body>

</html>