<?php
// Include database connection file
require_once('../../config/connect.php');

// Check if the form is submitted
if (isset($_POST['send_email'])) {
    // Retrieve question ID from the form
    $question_id = $_POST['question_id'];

    // Update question_status to 0 for the corresponding question ID
    $update_sql = "UPDATE tb_question SET question_status = 0 WHERE question_id = $question_id";
    $conn->query($update_sql);

    // Fetch question details from the database
    $question_sql = "SELECT * FROM tb_question WHERE question_id = $question_id";
    $question_result = $conn->query($question_sql);
    $question_row = $question_result->fetch_assoc();

    // Extract question details
    $sender_name = $question_row['question_sender_name'];
    $sender_email = $question_row['question_sender_email'];
    $question_content = $question_row['question_content'];

    // Email content
    $to = $sender_email;
    $subject = "Your Question has been Answered";
    $message = "Dear $sender_name,\n\n";
    $message .= "Thank you for your question. Here is the response:\n\n";
    $message .= "$question_content\n\n";
    $message .= "Best regards,\nYour Admin";

    // Additional headers
    $headers = "From: Your Admin <admin@example.com>\r\n";
    $headers .= "Reply-To: admin@example.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Send email
    if (mail($to, $subject, $message, $headers)) {
        echo "Email sent successfully!";
    } else {
        echo "Failed to send email!";
    }

    // Redirect back to the dashboard or any other page
    header("Location: ../contact.php");
    exit();
}
?>
