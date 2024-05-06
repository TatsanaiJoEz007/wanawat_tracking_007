<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
        height: 70px;
        background-color: #ff5722;
        color: white;
        padding: 0 20px;
        border-radius: 25px;
        font-weight: 700;
        transition: background-color 0.3s ease;
        cursor: pointer;
    }
    .track-button:hover {
        background-color: #e64a19;
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
        width: 70px;
        height: 70px;
        border-radius: 50%;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        left: 30px;
    }
    @media (max-width: 768px) {
        .tracking-input-group {
            flex-direction: column;
            align-items: center;
            padding: 20px;
            height: auto; /* Adjust height to fit content */
        }
        .tracking-input, .track-button {
            width: 100%;
            margin: 10px 0; /* Add margin between items */
        }
        .mascot {
            width: 78px;
            margin-bottom: 10px; /* Add margin to push mascot down */
        }
    }
</style>
</head>
<body>

<div class="container text-center" >
    <div class="tracking-input-group">
        <img src="../view/assets/img/logo/mascot.png" alt="Mascot" class="mascot">
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