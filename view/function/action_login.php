
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<?php
header('Content-Type: application/json');
require_once('../config/connect.php');

if (!isset($_SESSION)) {
    session_start(); //เช็ค Event Session_start จาก config/connect.php
}


if (isset($_POST['login']) ) {
    $user_email = ($_POST['user_email']);
    $user_pass = md5($_POST['user_pass']);
    $check = "SELECT * FROM tb_user WHERE user_email = '$user_email'";
    $check_user = $conn->query($check); 

    if ($check_user->num_rows >= 1) { 
        $check_mailpass = "SELECT * FROM tb_user WHERE user_email = '$user_email' AND user_pass = '$user_pass'";
        $query_pass = $conn->query($check_mailpass);

        if($query_pass->num_rows >=1) {  
            $user = $query_pass->fetch_array(); 

            if($user['user_status']  != 0){  //เช็คสถานะ tb_user
                if($user['user_type'] == 999 ){ //เช็คสถานะ admin
                   
                    echo 'admin' ; 
                    $_SESSION['login'] = true;
                    $_SESSION['user_type'] = 'admin';
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_firstname'] = $user['user_firstname'];
                    $_SESSION['user_lastname'] = $user['user_lastname'];
                    $_SESSION['user_email'] = $user['user_email'];
                    $_SESSION['user_pass'] = $user['user_pass'];
                    $_SESSION['user_img'] = $user['user_img'];
                    $_SESSION['user_address'] = $user['user_address'];
                    $_SESSION['user_address'] = $user['user_address'];
                    $_SESSION['user_tel'] = $user['user_tel'];
                    $_SESSION['user_create_at'] = $user['user_create_at'];

                    
                }
                if($user['user_type'] == 0 ){ //เช็คสถานะ user
                   
                    echo 'user' ;  
                    $_SESSION['login'] = true;
                    $_SESSION['user_type'] = 'user';
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_firstname'] = $user['user_firstname'];
                    $_SESSION['user_lastname'] = $user['user_lastname'];
                    $_SESSION['user_email'] = $user['user_email'];
                    $_SESSION['user_pass'] = $user['user_pass'];
                    $_SESSION['user_img'] = $user['user_img'];
                    $_SESSION['user_address'] = $user['user_address'];
                    $_SESSION['user_address'] = $user['user_address'];
                    $_SESSION['user_tel'] = $user['user_tel'];
                    $_SESSION['user_create_at'] = $user['user_create_at'];
                }
                if($user['user_type'] == 1 ){ //เช็คสถานะ employee
                   
                    echo 'employee' ;  
                    $_SESSION['login'] = true;
                    $_SESSION['user_type'] = 'employee';
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_firstname'] = $user['user_firstname'];
                    $_SESSION['user_lastname'] = $user['user_lastname'];
                    $_SESSION['user_email'] = $user['user_email'];
                    $_SESSION['user_pass'] = $user['user_pass'];
                    $_SESSION['user_img'] = $user['user_img'];
                    $_SESSION['user_address'] = $user['user_address'];
                    $_SESSION['user_address'] = $user['user_address'];
                    $_SESSION['user_tel'] = $user['user_tel'];
                    $_SESSION['user_create_at'] = $user['user_create_at'];
                }
                if($user['user_type'] == 2 ){
                    
                    echo 'clerk' ;  
                    $_SESSION['login'] = true;
                    $_SESSION['user_type'] = 'clerk';
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_firstname'] = $user['user_firstname'];
                    $_SESSION['user_lastname'] = $user['user_lastname'];
                    $_SESSION['user_email'] = $user['user_email'];
                    $_SESSION['user_pass'] = $user['user_pass'];
                    $_SESSION['user_img'] = $user['user_img'];
                    $_SESSION['user_address'] = $user['user_address'];
                    $_SESSION['user_address'] = $user['user_address'];
                    $_SESSION['user_tel'] = $user['user_tel'];
                    $_SESSION['user_create_at'] = $user['user_create_at'];
                }
            } else {
                echo 'close' ;
            }
        } else {
            echo 'failpass' ;
        }
    } else {
        echo 'failuser' ;
    }
}