<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Processor</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@1.14.0/dist/full.css" rel="stylesheet">
    

    <style>
        body {
            @apply bg-gradient-to-r from-pink-200 via-purple-200 to-pink-200 flex items-center justify-center min-h-screen;
        }

        #csvForm {
            @apply bg-white p-8 rounded-lg shadow-lg transform transition-all duration-300 hover:scale-105;
        }
    </style>
</head>

<body>
    <div class="flex flex-col items-center">
        <h1 class="text-4xl font-bold mb-6 text-purple-700">CSV Processor</h1>
        <form id="csvForm" class="w-full max-w-md">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="fileInput">
                    Upload CSV
                </label>
                <input type="file" name="csvFile" id="fileInput" class="w-full py-2 px-3 border border-purple-300 rounded-lg text-white-700 leading-tight focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
            </div>
            <div class="flex items-center justify-between">
                <button type="submit" class="btn btn-primary w-full py-2">
                    Process CSV
                </button>
            </div>
        </form>
        <div id="feedback" class="mt-4 p-4 bg-white rounded-lg shadow-lg max-w-md text-center hidden"></div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>
    <script src="csv.js"></script>
    <script>
        document.getElementById('csvForm').addEventListener('submit', function (event) {
            event.preventDefault();
            const fileInput = document.getElementById('fileInput');
            const feedback = document.getElementById('feedback');

            if (fileInput.files.length === 0) {
                feedback.textContent = 'Please select a file.';
                feedback.classList.remove('hidden');
                feedback.classList.remove('bg-green-100', 'text-green-700');
                feedback.classList.add('bg-red-100', 'text-red-700');
                return;
            }

            const file = fileInput.files[0];

            Papa.parse(file, {
                complete: function (results) {
                    feedback.textContent = 'CSV processed successfully!';
                    feedback.classList.remove('hidden');
                    feedback.classList.remove('bg-red-100', 'text-red-700');
                    feedback.classList.add('bg-green-100', 'text-green-700');
                },
                error: function () {
                    feedback.textContent = 'Error processing CSV.';
                    feedback.classList.remove('hidden');
                    feedback.classList.remove('bg-green-100', 'text-green-700');
                    feedback.classList.add('bg-red-100', 'text-red-700');
                }
            });
        });
    </script>
</body>

</html>
