<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://fastly.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $('#register').submit((e) => {
        e.preventDefault()
        let user_pass = $('#register-password').val()
        let user_email = $('#register-email').val()
        let c_pass = $('#register-c-password').val()
        let user_firstname = $('#register-firstname').val()
        let user_lastname = $('#register-lastname').val()
        let user_address = $('#register-address').val()
        let province_id = $('#register-province').val()
        let amphure_id = $('#register-amphure').val()
        let district_id = $('#register-district').val()
        let user_tel = $('#register-tel').val()



        let option = {
            url: 'function/action_register2.php',
            type: 'post',
            data: {
                user_pass: user_pass,
                user_email: user_email,
                user_firstname: user_firstname,
                user_lastname: user_lastname,
                user_address: user_address,
                province_id: province_id,
                amphure_id: amphure_id,
                district_id: district_id,
                user_tel: user_tel,
                register: 1
            },
            success: function(res) {
                if (res != 'fail') {
                    Swal.fire({
                        position: 'center',
                        icon: 'success',
                        title: 'สมัครสมาชิกสำเร็จ!!',
                        showConfirmButton: false,
                        timer: 1500
                    })
                    setTimeout(() => {
                        location.href="index"
                    }, 600)
                    $('#register-password').val('')
                    $('#register-email').val('')
                    $('#register-c-password').val('')
                    $('#register-firstname').val('')
                    $('#register-lastname').val('')
                    $('#register-address').val('')
                    $('#register-province').val('')
                    $('#register-amphure').val('')
                    $('#register-district').val('')
                    $('#register-tel').val('')
                } else {
                    Swal.fire({
                        position: 'center',
                        icon: 'error',
                        title: 'มีสมาชิกนี้แล้วในระบบ!!',
                        showConfirmButton: false,
                        timer: 1500
                    })
                }
            }
        }
        if (user_pass != c_pass) {
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: 'รหัสผ่านไม่ตรงกัน!!',
                showConfirmButton: false,
                timer: 1500
            })
            $('#register-password').val('')
            $('#register-c-password').val('')
        } else {
            $.ajax(option)
        }
    })
</script>