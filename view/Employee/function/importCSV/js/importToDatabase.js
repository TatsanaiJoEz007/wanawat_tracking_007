let = [];


function importToDatabase() {
    if (convertedCSVData2) {
        const formData = new FormData();
        formData.append('csvData', convertedCSVData); // Ensure this is the correct data variable

        console.log('Sending CSV Data:', convertedCSVData);

        fetch('../importHeader.php', { // Ensure the correct server-side script is specified
                method: 'POST',
                body: formData
            })
            .then(response => response.text()) // Get response as text for debugging
            .then(text => {
                console.log('Raw response from server:', text); // Log raw response
                try {
                    const message = JSON.parse(text); // Attempt to parse JSON
                    console.log('Parsed response from server:', message);
                    const output = document.getElementById('output1');
                    output.innerText = message.message;
                    if (message.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: message.message
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: message.message
                        });
                    }
                } catch (e) {
                    console.error('Failed to parse JSON:', e);
                    console.error('Response text:', text);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Failed to parse server response. Check console for details.'
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    } else {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please convert a CSV file first.'
        });
    }
}