<?php 
if (!isset($_SESSION)) {
    session_start();
}
if(!isset($_SESSION['user_type']) == 'admin'){
    header('Location: ../index');
}
?>