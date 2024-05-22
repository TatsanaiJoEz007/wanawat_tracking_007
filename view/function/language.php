<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    $_SESSION['lang'] = $lang;
} elseif (isset($_SESSION['lang'])) {
    $lang = $_SESSION['lang'];
} else {
    // Default language is Thai
    $lang = 'th';
    $_SESSION['lang'] = $lang;
}

// Now, include the language file
require_once('th_eng.php');
?>
