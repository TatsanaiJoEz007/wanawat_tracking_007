<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://fastly.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function () {
    $('#login').on('submit', function (e) {
        e.preventDefault();
        let email = $('#signin-email').val();
        let password = $('#signin-password').val();
        let option = {
            url: '../view/function/action_login.php',
            type: 'post',
            data: {
                email: email,
                password: password,
                login: 1
            },
            success: function(res) {
                console.log('Response:', res);
                if (res.trim() == 'failuser') {
                    // Handle invalid user case
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'ไม่มีบัญชีนี้ในระบบ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#signin-password').val('');
                } else if (res.trim() == 'failpass') {
                    // Handle invalid password case
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'รหัสผ่านไม่ถูกต้อง!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    $('#signin-password').val('');
                } else if (res.trim() == '999') {
                    // Redirect admin users
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เข้าสู่ระบบสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(() => {
                        location.href = "../view/admin/index.php";
                    }, 900);
                } else if (res == 'close') {
                    // Handle closed account case
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'บัญชีนี้ถูกระงับการใช้งาน',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    // Redirect non-admin users
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'เข้าสู่ระบบสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(() => {
                        location.href = "../view/index.php";
                    }, 900);
                }
            }
        };
        $.ajax(option).fail(function(jqXHR, textStatus, errorThrown) {
            console.log("AJAX call failed: " + textStatus + ", " + errorThrown);
        });
    });
});

</script>
