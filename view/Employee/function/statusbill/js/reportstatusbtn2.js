document.getElementById("reportProblemBtn2").onclick = function() {
    var deliveryId = modal.dataset.deliveryId;

    // Ask for confirmation using SweetAlert
    Swal.fire({
        title: 'คุณแน่ใจไหม?',
        text: 'คุณแน่ใจหรือไม่ที่จะแจ้งว่าการจัดส่งครั้งนี้มีปัญหา คุณจะไม่สามารถแก้ไขได้หากคุณได้ทำการแจ้งว่าการจัดส่งครั้งนี้มีปัญหา?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ใช่, แจ้งปัญหา',
        cancelButtonText: 'ไม่, ยกเลิก',
    }).then((result) => {
        if (result.isConfirmed) {
            // User confirmed, proceed with reporting problem

            // Example AJAX request for updating status
            fetch('function/problem_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        deliveryId: deliveryId,
                        problem: 'Specify the problem here if needed'
                    }),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("Response from server:", data);

                    // Handle response
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Parcel problem reported successfully.',
                        });
                        location.reload();
                        // Optionally, you can reload the page or update UI here
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message || 'Failed to report parcel problem.',
                        });
                    }
                    modal.style.display = "none"; // Close modal after action
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to update status.',
                    });
                    modal.style.display = "none"; // Close modal on error
                });
        }
    });
}