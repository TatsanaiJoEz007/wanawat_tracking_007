document.getElementById("updateStatusBtn").onclick = function() {
    let selectedDeliveryIds = [];
    document.querySelectorAll('input[type="checkbox"]:checked').forEach(function(checkbox) {
        selectedDeliveryIds.push(parseInt(checkbox.value)); // Ensure values are integers
    });

    console.log("Selected delivery IDs:", selectedDeliveryIds); // Log delivery IDs

    if (selectedDeliveryIds.length === 0) {
        alert('Please select at least one delivery.');
        return;
    }

    // Ask for confirmation using SweetAlert
    Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to update the status of these deliveries?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, update',
        cancelButtonText: 'No, cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            // User confirmed, proceed with updating status
            fetch('function/update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        deliveryIds: selectedDeliveryIds
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
                            text: 'Delivery status updated successfully.',
                        }).then(() => {
                            location.reload(); // Reload the page after successful update
                        });
                    } else if (data.status === 'error' && data.code === 'status_limit') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Status cannot be more than 5.',
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message || 'Failed to update delivery status.',
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Failed to update delivery status.',
                    });
                });
        }
    });
}
