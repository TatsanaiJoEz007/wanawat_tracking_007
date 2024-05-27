<?php
// connect to database
require_once('../../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = $_POST['email']; // Ensure the name matches the input in the form
    // Check if email exists in the database
    $query = $conn->prepare('SELECT * FROM tb_user WHERE user_email = ?');
    $query->bind_param('s', $user_email);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        echo 'exists';
    } else {
        echo 'notexists';
    }

    $query->close();
}
?>
