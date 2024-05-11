
<!DOCTYPE html>
<html lang="th">

<head>
    <title>Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <!-- Bootstrap CSS from CDN -->
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Custom CSS for styling */
        /* You can add your own styles here */
        .container {
            margin-top: 50px;
        }

        .bug-report {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            position: relative;
            /* Needed for positioning button */
        }

        .send-email-btn {
            background-color: orangered;
            position: absolute;
            top: 10px;
            right: 10px;
            color: white;
        }
    </style>
</head>
<body>
    <?php require_once ('function/sidebar3.php'); ?>
  
    <div class="container">
        <br>
        <h2>คำถาม และปัญหาในการใช้งาน</h2>

        <!-- Display bug reports -->
        <div>
            <?php
            // Include database connection file
            require_once ('../config/connect.php');

            // Fetch bug reports from database
            $sql = "SELECT * FROM tb_question WHERE question_status = 1"; // Only select unanswered questions
            $result = $conn->query($sql);

            // Check if there are any unanswered bug reports
            if ($result->num_rows > 0) {
                // Output each unanswered bug report
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="bug-report">';
                    echo '<h3>' . $row["question_sender_name"] . '</h3>';
                    echo '<form method="post" action="function/send_mail.php">'; // Form for sending email, updated action
                    echo '<input type="hidden" name="question_id" value="' . $row["question_id"] . '">'; // Hidden input field to hold the question ID
                    echo '<button type="submit" class="btn btn-primary send-email-btn" name="send_email">Send Email</button>'; // Submit button
                    echo '</form>';
                    echo '<p><strong>รายละเอียด:</strong> ' . $row["question_content"] . '</p>';
                    echo '</div>';
                }
            } else {
                echo "<p>No unanswered bug reports found.</p>";
            }

            // Close database connection
            $conn->close();
            ?>
        </div>

    </div>
</body>

</html>