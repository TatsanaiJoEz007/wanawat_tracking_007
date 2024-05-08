<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
    $('#register').submit(function(e) {
        e.preventDefault();

        console.log('Register form submitted');

        const user_pass = $('#register-password').val();
        const user_email = $('#register-email').val();
        const c_pass = $('#register-c-password').val();
        const user_firstname = $('#register-firstname').val();
        const user_lastname = $('#register-lastname').val();
        const user_address = $('#register-address').val();
        const province_id = $('#province').val();
        const amphure_id = $('#amphure').val();
        const district_id = $('#district').val();
        const user_tel = $('#register-tel').val();

        // Check if passwords match
        if (user_pass !== c_pass) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'รหัสผ่านไม่ตรงกัน!!',
                showConfirmButton: false,
                timer: 1500
            });
            $('#register-password').val('');
            $('#register-c-password').val('');
            return;
        }

        // AJAX request
        $.ajax({
            url: 'function/action_register2.php',
            type: 'POST',
            data: $(this).serialize(),
            // {
            //     register: 1,
            //     user_email: user_email,
            //     user_pass: user_pass,
            //     user_firstname: user_firstname,
            //     user_lastname: user_lastname,
            //     user_address: user_address,
            //     province_id: province_id,
            //     amphure_id: amphure_id,
            //     district_id: district_id,
            //     user_tel: user_tel,
            // },
            success: function(response) {
                console.log('Ajax request to action_register2.php successful');
                console.log('response:', response);
                if (response === 'success') {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'สมัครสมาชิกสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                    setTimeout(function() {
                        location.href = "index.php"; // Redirect to index.php after successful registration
                    }, 600);
                } else if (response === 'fail') {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'มีสมาชิกนี้แล้วในระบบ!!',
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'มีข้อผิดพลาดเกิดขึ้น โปรดลองอีกครั้ง',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error occurred during AJAX request:', error);
                console.error('Server response:', xhr.responseText);
                Swal.fire({
                    position: 'center',
                    icon: 'error',
                    title: 'มีข้อผิดพลาดเกิดขึ้น โปรดลองอีกครั้ง',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });
});
</script>
