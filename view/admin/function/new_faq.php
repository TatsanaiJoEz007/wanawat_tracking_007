<?php
// Include database connection
include '../../config/connect.php';

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input fields
    $freq_header = mysqli_real_escape_string($conn, $_POST['freq_header']);
    $freq_content = mysqli_real_escape_string($conn, $_POST['freq_content']);

    // Insert new FAQ into the database
    $query = "INSERT INTO tb_freq (freq_header, freq_content, freq_create_at) VALUES ('$freq_header', '$freq_content', NOW())";
    if (mysqli_query($conn, $query)) {
        // Redirect to the FAQ list page
        header("Location: ../admin/faq_list.php");
        echo "New record created successfully";
        exit();
    } else {
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
    }
}
// Close database connection
mysqli_close($conn);
?>
