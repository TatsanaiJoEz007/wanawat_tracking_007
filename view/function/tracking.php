<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        .tracking-input-group {
            position: relative;
            margin-top: 50px;
            background: #fff;
            border-radius: 50px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            height: 60px;
        }
        .track-button {
            position: absolute;
            right: -1px;
            top: -1px;
            bottom: -1px;
            border: none;
            background-color: #ff5722;
            color: white;
            padding: 0 25px;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
            font-weight: 700;
            transition: background-color 0.3s ease;
        }
        .track-button:hover {
            background-color: #e64a19;
            cursor: pointer;
        }
        .tracking-input {
            border: none;
            box-shadow: none;
            border-radius: 80px;
            padding-left: 140px;
            padding-right: 90px;
            height: 60px;
            width: 100%;
        }
        .mascot {
            width: 100px;
            position: absolute;
            left: -100px;
            top: 50%;
            transform: translateY(-50%);
            border-radius: 50%;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }
        @media (max-width: 768px) {
            .tracking-input-group {
                margin-top: 20px;
                position: relative; /* Set relative positioning for parent */
            }
            .tracking-input {
                padding-left: 100px;
                padding-right: 70px;
            }
            .mascot {
                width: 70px; /* Adjust the width as desired */
                right: 10px; /* Position from the right */
            }
        }
        .form-control {
            width: 100%;       /* Makes the input width responsive to the container size */
            padding: 8px 12px; /* Adds some padding inside the input */
            box-sizing: border-box; /* Includes padding and border in the element's total width and height */
            border: 1px solid #ccc; /* Adds a light grey border */
            border-radius: 4px; /* Rounds the corners of the input field */
        }
        /* Optional: Media query for smaller screens */
        @media (max-width: 600px) {
            .form-control {
                padding: 4px 7px; /* Smaller padding on smaller screens */
            }
        }
    </style>
</head>
<body>
<div class="container text-center" >
    <div class="tracking-input-group">
        <img src="../view/assets/img/logo/mascot.png" alt="Mascot" class="mascot">
        <input type="text" class="form-control tracking-input" placeholder=" Enter your tracking number here!">
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