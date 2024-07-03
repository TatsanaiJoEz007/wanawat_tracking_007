function importToDatabase2() {
    if (convertedCSVData2) {
        const formData = new FormData();
        formData.append('csvData2', convertedCSVData2); // Pass converted CSV data

        fetch('', { // Use current file path
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(message => {
                const output = document.getElementById('output2');
                output.innerText = '';
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: "Importing data successfully!"
                });
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