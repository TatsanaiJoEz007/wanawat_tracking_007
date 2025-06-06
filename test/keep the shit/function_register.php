<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://fastly.jsdelivr.net/npm/sweetalert2@11"></script>

<script> 

   $('#register').submit((e) => {
        e.preventDefault()
        let pass = $('#register-password').val()
        let email = $('#register-email').val()
        let c_pass = $('#register-c-password').val()
        let firstname = $('#register-firstname').val()
        let lastname = $('#register-lastname').val()
        let address = $('#register-address').val()
        let provinceId = $('#province').val()
        let amphureId = $('#amphure').val()
        let districtId = $('#district').val()
        let tel = $('#register-tel').val()
        
        let option = {
            url: '../view/function/action_register.php',
            type: 'post',
            data: { 
                email: email,
                firstname: firstname,
                lastname: lastname,
                address: address,
                provinceId: provinceId,
                amphureId: amphureId,
                districtId: districtId,
                tel: tel,
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
                        location.href="../view/mainpage.php"
                    }, 600)
                    $('#register-password').val('')
                    $('#register-email').val('')
                    $('#register-c-password').val('')
                    $('#register-firstname').val('')
                    $('#register-lastname').val('')
                    $('#register-address').val('')
                    $('#province').val('')
                    $('#amphure').val('')
                    $('#district').val('')
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
        if (pass != c_pass) {
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
