<?php
require_once ('../view/config/connect.php');
// Fetch questions from the database
$query = "SELECT freq_header , freq_content FROM tb_freq";
$result = mysqli_query($conn, $query);
?>

<?php
// Check if there are any questions retrieved
if (mysqli_num_rows($result) > 0) {
    // Output questions and answers dynamically
    while ($row = mysqli_fetch_assoc($result)) {
        echo '
        <button class="accordion">' . $row['freq_header'] . '<i class="fas fa-caret-down"></i></button>
        <div class="panel">
            <div class="panel-content">
                <div class="box-widget">
                    <p>' . $row['freq_content'] . '</p>
                </div>
            </div>
        </div>';
    }
} else {
    // Output a message if no questions are found
    echo "No questions found.";
}
?>