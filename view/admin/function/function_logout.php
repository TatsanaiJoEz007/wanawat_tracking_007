<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://fastly.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
        function logout() {
        let option = {
            url: 'function/action_logout.php',
            type: 'post',
            data: {
                logout: 1
            },
            success: function(res) {
                Swal.fire({
                    position: 'center',
                    icon: 'success',
                    title: 'ออกจากระบบสำเร็จ!!',
                    showConfirmButton: false,
                    timer: 1500
                })
                setTimeout(() => {
                    debugger;

                    console.log(res)
                    location.href = '../index'
                }, 900)
            }
        }
        $.ajax(option)
    }
</script>