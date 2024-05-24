<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#registerForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serialize();
            console.log('Serialized Form Data:', formData); // This will log all data sent in the request

            $.ajax({
                url: '../view/function/action_register.php', // Update with the correct path
                type: 'POST',
                data: formData,
                dataType: 'json',
                beforeSend: function() {
                    console.log('Sending AJAX request...');
                },
                success: function(response) {
                    console.log('Response received:', response); // Log response from server
                    if (response.success) {
                        console.log('Registration successful:', response.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'login.php';
                        });
                    } else {
                        console.log('Registration failed:', response.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error); // Log AJAX errors
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while processing your request.',
                        confirmButtonText: 'OK'
                    });
                },
                complete: function() {
                    console.log('AJAX request completed.');
                }
            });
        });
    });
</script>