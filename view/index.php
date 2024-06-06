

<!DOCTYPE html>
    <html lang="<?php echo $lang; ?>">

    <head>
        <?php
            require_once('function/head.php');
        ?>
    </head>

    <style>

/* Hide scrollbar for Chrome, Safari and Opera */
::-webkit-scrollbar {
    width: 9px; /* Adjust width for vertical scrollbar */
}

::-webkit-scrollbar-thumb {
    background-color: #FF5722; /* Color for scrollbar thumb */
    border-radius: 10px; /* Rounded corners for scrollbar thumb */
}

/* Hide scrollbar for IE, Edge and Firefox */
/* Note: Firefox currently does not support hiding the scrollbar */



    </style>
    <body>
        <div>
        <?php
            require_once('function/navbar.php');
        ?>
        <?php
            require_once('function/banner.php');
        ?>
        <br>
        <br>
        <br>
        <?php
            require_once('function/tracking.php');
        ?>  
            <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
<br>
    <footer>
    <?php
    require_once('function/footer.php');
    ?>
    </footer>
</html>
