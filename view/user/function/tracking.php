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
        /* Unicode for truck icon in Font Awesome */
        font-family: 'Font Awesome\ 5 Free';
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
            /* Adjust height to fit content */
        }

        .tracking-input,
        .track-button {
            width: 100%;
            margin: 10px 0;
            /* Add margin between items */
        }

        .mascot {
            width: 78px;
            margin-bottom: 10px;
            /* Add margin to push mascot down */
        }
    }
</style>
</head>

<body>

    <div class="container text-center">
        <div class="tracking-input-group">
            <img src="../view/assets/img/logo/mascot.png" alt="Mascot" class="mascot">
            <p>&nbsp; &nbsp;</p>
            <input type="text" class="form-control tracking-input" placeholder="<?php echo $lang_track ?>  (Ex. ED#############)" required>
            <button class="track-button">Track</button>
        </div>
    </div>
    <script>
        document.querySelector('.track-button').addEventListener('click', function() {
            var trackingId = document.querySelector('.tracking-input').value;
            // Replace alert with SweetAlert
            Swal.fire({
                icon: 'success',
                title: 'Tracking ID:',
                text: trackingId,
            });
        });
    </script>
</body>