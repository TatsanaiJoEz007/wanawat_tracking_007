document.getElementById("reportProblemBtn").onclick = function(event) {
    // Prevent default form submission if inside a form
    event.preventDefault();

    var modal = document.getElementById("manageModal");

    if (!modal) {
        console.error("Modal element not found");
        return;
    }

    // Collect selected delivery IDs from checkboxes
    const checkboxes = document.querySelectorAll('input[name="select"]:checked');
    const deliveryIds = Array.from(checkboxes).map(checkbox => parseInt(checkbox.value));

    if (deliveryIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Selection',
            text: 'Please select at least one delivery to report a problem.',
        });
        return;
    }

    const deliveries = deliveryIds.map(id => ({
        deliveryId: id,
        problem: 'Specify the problem here if needed'
    }));

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
                        deliveries: deliveries
                    }),
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text(); // Get text response to inspect it
                })
                .then(text => {
                    console.log("Raw response from server:", text);

                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        throw new Error('Invalid JSON response');
                    }

                    // Handle response
                    let successCount = 0;
                    let errorCount = 0;

                    data.forEach(result => {
                        if (result.status === 'success') {
                            successCount++;
                        } else {
                            errorCount++;
                        }
                    });

                    if (errorCount === 0) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'All parcel problems reported successfully.',
                        }).then(() => {
                            location.reload(); // Reload the page after user confirms the success message
                        });
                    } else if (successCount > 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Partial Success!',
                            text: `${successCount} problems reported successfully, ${errorCount} failed.`,
                        }).then(() => {
                            location.reload(); // Reload the page after user confirms the warning message
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Failed to report any parcel problems.',
                        }).then(() => {
                            location.reload(); // Reload the page after user confirms the error message
                        });
                    }
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