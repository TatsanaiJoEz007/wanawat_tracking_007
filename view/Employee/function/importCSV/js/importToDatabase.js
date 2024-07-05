function importToDatabase() {
    if (convertedCSVData) {
        const formData = new FormData();
        formData.append('csvData', convertedCSVData); // Pass converted CSV data

        fetch('../../view/Employee/function/importCSV/importHeader.php', { // ชื่อไฟล์ PHP ที่จะรับข้อมูล
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const { duplicateRows, importedRows } = data;

                let message = `Header Imported Rows:\n${importedRows.join('\n')}\n\nHeader Duplicate Rows:\n${duplicateRows.join('\n')}`;

                const output = document.getElementById('outputHeader');
                output.innerText = message;

                Swal.fire({
                    icon: 'success',
                    title: 'Import Result',
                    text: message
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