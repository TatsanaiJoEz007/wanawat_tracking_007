<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">

<head>
    <?php require_once('function/head.php'); ?>
</head>

<style>
        ::-webkit-scrollbar {
    width: 9px; /* Adjust width for vertical scrollbar */
}

::-webkit-scrollbar-thumb {
    background-color: #FF5722; /* Color for scrollbar thumb */
    border-radius: 10px; /* Rounded corners for scrollbar thumb */
}

    </style>
<body>
    <?php require_once('function/navindex.php'); ?>
    
    <br><br>

    <?php require_once('function/contactcard.php'); ?>
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
        <?php require_once('function/footer.php'); ?>
    </footer>

    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>