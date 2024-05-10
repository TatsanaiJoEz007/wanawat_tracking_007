<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://fastly.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function logout() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'Do you want to logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, logout',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // User confirmed, proceed with logout
                let option = {
                    url: '../../view/function/action_logout.php',
                    type: 'post',
                    data: {
                        logout: 1
                    },
                    success: function (res) {
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: 'Logged out successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        });
                        setTimeout(() => {
                            location.href = '../../view/index.php';
                        }, 900);
                    }
                };
                $.ajax(option);
            }
        });
    }
</script>