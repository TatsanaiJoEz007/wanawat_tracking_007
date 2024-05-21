<!-- NODE JS -->
<script>
const fs = require('fs');
const iconv = require('iconv-lite');
const { parse } = require('csv-parse');
const mysql = require('mysql');

function readAndProcessCSV(inputFile, outputFile, callback) {
    // Read and process the CSV file
    fs.readFile(inputFile, (err, data) => {
        if (err) throw err;

        // Convert encoding from Windows-874 to UTF-8
        const utf8Data = iconv.decode(data, 'windows-874');
        const utf8Buffer = iconv.encode(utf8Data, 'utf-8');

        fs.writeFile(outputFile, utf8Buffer, (err) => {
            if (err) throw err;

            // Parse the CSV data
            const records = [];
            fs.createReadStream(outputFile)
                .pipe(parse({ delimiter: ',' }))
                .on('data', (row) => {
                    records.push(row);
                })
                .on('end', () => {
                    callback(records);
                });
        });
    });
}

function importToDatabase(records, dbConfig, tableName) {
    // Connect to the database
    const connection = mysql.createConnection(dbConfig);

    connection.connect((err) => {
        if (err) throw err;
        console.log('Connected to the database');

        // Create table if it doesn't exist
        const createTableQuery = `
            CREATE TABLE IF NOT EXISTS ${tableName} (
                column1 VARCHAR(255),
                column2 VARCHAR(255),
                column3 VARCHAR(255),
                ...
            )
        `;
        connection.query(createTableQuery, (err) => {
            if (err) throw err;

            // Insert records into the table
            const insertQuery = `INSERT INTO ${tableName} VALUES ?`;
            connection.query(insertQuery, [records], (err) => {
                if (err) throw err;
                console.log('Data imported successfully');
                connection.end();
            });
        });
    });
}

function main() {
    const inputFile = 'path/to/input.csv';  // Path to the input CSV file
    const outputFile = 'path/to/output.csv';  // Path to the processed CSV file
    const dbConfig = {
        host: 'localhost',
        user: 'root',
        password: 'password',
        database: 'your_database'
    };
    const tableName = 'your_table';  // Name of the table in your database

    readAndProcessCSV(inputFile, outputFile, (records) => {
        importToDatabase(records, dbConfig, tableName);
    });
}

main();

</script>

<!-- PURE JS -->
<script>
    function processCSV() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    const formData = new FormData();
    formData.append('file', file);

    $.ajax({
        url: 'http://localhost:3000/process',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            console.log(response);
            alert('CSV processed successfully!');
        },
        error: function(xhr, status, error) {
            console.error(error);
            alert('Error processing CSV.');
        }
    });
}

</script>


<!-- FRONT END -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Import</title>
</head>
<body>
    <input type="file" id="fileInput">
    <button onclick="processCSV()">Process CSV</button>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="frontend.js"></script>
</body>
</html>
