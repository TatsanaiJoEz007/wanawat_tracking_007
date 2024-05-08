<script>
    $.ajax({
    url: 'addfreq.php',
    type: 'post',
    dataType: 'json',
    data: { 
        freq_header: freq_header, 
        freq_content: freq_content,
    },
    success: function(response) {
        if (response.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: response.message
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.message
            });
        }
    },
    error: function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'An error occurred. Please try again later.'
        });
    }
});

</script>