        // Update status button click handler
        document.getElementById("updateStatusBtn2").onclick = function() {
            var deliveryIds = modal.dataset.deliveryIds;

            // Ask for confirmation using SweetAlert
            Swal.fire({
                title: 'คุณแน่ใจไหม?',
                text: 'คุณแน่ใจหรือไม่ที่จะอัพเดทสถานะการขนส่งครั้งนี้ คุณจะไม่สามารถแก้ไขได้หากคุณได้ทำการอัพเดทไปแล้ว?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, อัพเดท',
                cancelButtonText: 'ไม่, ยกเลิก',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with updating status

                    // Example AJAX request for updating status
                    fetch('function/update_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                deliveryIds: deliveryIds
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
                                });
                                location.reload(); // Reload the page after successful update
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message || 'Failed to update delivery status.',
                                });
                            }
                            modal.style.display = "none"; // Close modal after action
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to update delivery status.',
                            });
                            modal.style.display = "none"; // Close modal on error
                        });
                }
            });
        }