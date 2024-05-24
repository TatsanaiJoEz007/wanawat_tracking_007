<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $('#profileForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this); // Use FormData to handle file uploads

            $.ajax({
                url: '../view/function/action_updateprofile.php',
                type: 'POST',
                data: formData, // Use FormData object directly
                contentType: false, // Set contentType to false when using FormData
                processData: false, // Set processData to false when using FormData
                dataType: 'json',
                beforeSend: function() {
                    console.log('Sending AJAX request...');
                },
                success: function(response) {
                    console.log('Response received:', response);
                    if (response.success) {
                        console.log('Profile updated successfully:', response.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: response.message,
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                closeModal('editProfileModal'); // Close the modal
                            }
                        });
                    } else {
                        console.log('Profile update failed:', response.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message,
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error:', status, error);
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

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = "none";
    }
</script>
