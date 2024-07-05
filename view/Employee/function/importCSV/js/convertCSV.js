let convertedCSVData; // Store converted CSV data globally

function convertCSV() {
  const fileInput = document.getElementById("csvFileInput1");
  const file = fileInput.files[0];

  if (file) {
    const reader = new FileReader();
    reader.onload = function (event) {
      const csvData = event.target.result;
      Papa.parse(csvData, {
        complete: function (results) {
          // Filter out blank rows
          const filteredData = results.data.filter((row) =>
            row.some((cell) => cell.trim() !== "")
          );
          convertedCSVData = Papa.unparse(filteredData);
          document.getElementById("output1").innerText = convertedCSVData;
        },
      });
    };
    reader.readAsText(file, "windows-874");
  } else {
    Swal.fire({
      icon: "error",
      title: "Oops...",
      text: "Please select a CSV file.",
    });
  }
}
