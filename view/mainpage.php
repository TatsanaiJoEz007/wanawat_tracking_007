<?php 

session_start(); 

if ($_SESSION['user_type'] != 'user') {
    header('Location: index.php');
}

?> 


<!DOCTYPE html>
    <html lang="en">

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
            
        
        <?php require_once('function/banner.php'); ?>
            
        
        <br>
        <br>
        <br>
        <?php require_once('function/tracking.php'); ?> 
            
    </body>
<br>
<br>
<br>
<br>
<br>
    <footer>
    <?php require_once('function/footer.php'); ?>
    
    
    </footer>
</html>
