async function processCSV() {
    const fileInput = document.getElementById('fileInput');
    const feedback = document.getElementById('feedback');
    feedback.textContent = '';

    if (fileInput.files.length === 0) {
        feedback.textContent = 'Please select a file!';
        return;
    }

    const file = fileInput.files[0];

    try {
        const data = await readFileAsText(file);
        feedback.textContent = 'File read successfully. Parsing CSV...';

        Papa.parse(data, {
            header: false, // Since the CSV has no header, set to false
            complete: async function(results) {
                const records = results.data;
                feedback.textContent = 'CSV parsed successfully. Importing to database...';
                await importToDatabase(records, feedback);
            },
            error: function(error) {
                feedback.textContent = 'Error parsing CSV: ' + error.message;
            }
        });
    } catch (error) {
        feedback.textContent = 'Error reading file: ' + error.message;
    }
}

function readFileAsText(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = () => reject(reader.error);
        reader.readAsText(file);
    });
}

async function importToDatabase(records, feedback) {
    try {
        const response = await fetch('processCSV.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ records })
        });

        const result = await response.json();

        if (response.ok) {
            feedback.textContent = result.message;
            if (result.errors) {
                console.error('Errors:', result.errors);
            }
        } else {
            feedback.textContent = 'Failed to import data: ' + result.message;
            console.error('Errors:', result.errors);
        }
    } catch (error) {
        feedback.textContent = 'Error: ' + error.message;
    }
}
