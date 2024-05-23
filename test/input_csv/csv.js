document.getElementById('csvForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const fileInput = document.getElementById('fileInput');
    const feedback = document.getElementById('feedback');

    if (fileInput.files.length === 0) {
        feedback.textContent = 'Please select a file.';
        return;
    }

    const formData = new FormData();
    formData.append('csvFile', fileInput.files[0]);

    fetch('processCSV.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())  // Use text() instead of json() for debugging
    .then(data => {
        console.log('Server response:', data);  // Log the full response
        try {
            const jsonData = JSON.parse(data);
            feedback.textContent = jsonData.message;
            if (jsonData.errors) {
                console.error('Errors:', jsonData.errors);
            }
        } catch (error) {
            feedback.textContent = 'Invalid JSON response';
            console.error('Invalid JSON response:', data);
        }
    })
    .catch(error => {
        feedback.textContent = 'An error occurred during processing.';
        console.error('Error:', error);
    });
});
