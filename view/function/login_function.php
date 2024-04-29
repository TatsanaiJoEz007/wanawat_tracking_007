<?php 
require_once('/config/connect.php');

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = md5($_POST['password']);
    $check = "SELECT * FROM tb_user WHERE user_username = '$email'";
    $check_user = $conn->query($check);
    if ($check_user->num_rows >= 1) {
        $check_pass = "SELECT * FROM tb_user WHERE user_username = '$email' AND user_pass = '$pass'";
        $query_pass = $conn->query($check_pass);
        if ($query_pass->num_rows >= 1) {
            $user = $query_pass->fetch_array();
            if ($user['status'] != 0) {
                if ($user['user_type'] == 999) {
                    echo 'admin';
                    $_SESSION['login'] = true;
                    $_SESSION['user_type'] = 'admin';
                    $_SESSION['user_id'] = $user['User_ID'];
                    $_SESSION['user_img'] = $user['user_img'];
                    $_SESSION['user_username'] = $user['user_username'];
                    $_SESSION['user_password'] = $user['user_pass'];
                    $_SESSION['user_create'] = $user['created_at'];
                } else {
                    $_SESSION['login'] = true;
                    $_SESSION['user_type'] = 'user';
                    $_SESSION['user_img'] = $user['user_img'];
                    $_SESSION['user_id'] = $user['User_ID'];
                    $_SESSION['user_username'] = $user['user_username'];
                    $_SESSION['user_password'] = $user['user_pass'];
                    $_SESSION['user_create'] = $user['created_at'];
                }
            } else {
                echo 'close';
            }
        } else {
            echo 'failpass';
        }
    } else {
        echo 'failuser';
    }
}
?>

<script>
    $('#login').submit((e) => {
        e.preventDefault()
        let email = $('#singin-email').val()
        let password = $('#singin-password').val()
        let option = {
            type: 'post',
            data: {
                email: email,
                password: password,
                login: 1
            },
            success: function(res) {
                if (res == 'failuser') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'ไม่มีบัญชีนี้ในระบบ!!',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    $('#singin-password').val('')
                } else if (res == 'failpass') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'รหัสผ่านไม่ถูกต้อง!!',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    $('#singin-password').val('')
                } else if (res == 'admin') {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เข้าสู่ระบบสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    setTimeout(() => {
                        location.href = "admin/index"
                    }, 900)
                } else if (res == 'close') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'บัญชีนี้ถูกระงับการใช้งาน',
                        showConfirmButton: false,
                        timer: 1500
                    })
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เข้าสู่ระบบสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    setTimeout(() => {
                        location.href = "index"
                    }, 900)
                }
            }
        }
        $.ajax(option)
    })
</script>