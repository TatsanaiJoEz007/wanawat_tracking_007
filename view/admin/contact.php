<!DOCTYPE html>
<html lang="th">
<?php require_once('function/action_activity_log/log_activity.php');  
$adminId = 123;
?>

<head>
    <title>Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <!-- Bootstrap CSS from CDN -->
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        ::-webkit-scrollbar {
            width: 12px;
            /* Adjust width for vertical scrollbar */
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            /* Color for scrollbar thumb */
            border-radius: 10px;
            /* Rounded corners for scrollbar thumb */
        }

        /* Container Styling */
        .home-section {
            max-height: 100vh;
            /* Adjust height as needed */
            overflow-y: auto;
            /* Allow vertical scroll */
            overflow-x: hidden;
            /* Prevent horizontal scroll */
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
    </style>
</head>

<body>

    <script>
        function sendEmail(question_id) {
            // Display SweetAlert confirmation dialog
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "ต้องการส่งอีเมลล์ให้ผู้ใช้งานหรือไม่ ถ้าหากกดส่งแล้ว จะไม่สามารถส่งอีกครั้งได้",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If user confirms, submit the form to send the email
                    $('#sendEmailForm_' + question_id).submit();
                    // Log admin activity
                    logAdminActivity(<?php echo $adminId; ?>, 'Email Sent', 'Question', question_id, 'Email sent to user');
                    // Make AJAX request to update question_status
                    $.ajax({
                        type: "POST",
                        url: "function/update_questionstatus.php", // Update with the actual PHP script path
                        data: {
                            question_id: question_id
                        },
                        success: function(response) {
                            if (response === "success") {
                                // If update successful, remove the bug report from the page
                                $('#sendEmailForm_' + question_id).closest('.bug-report').remove();
                                location.reload();
                            } else {
                                // Handle error
                                alert("Failed to update status.");
                            }
                        },
                        error: function() {
                            // Handle error
                            alert("Error occurred while updating status.");
                        }
                    });
                }
            });
        }
    </script>
    <?php require_once('function/sidebar.php'); ?>

    <div class="container">
        <br>
        <h2>คำถาม และปัญหาในการใช้งาน</h2>

        <!-- Display bug reports -->
        <div>
            <?php
            // Include database connection file
            require_once('../config/connect.php');

            // Fetch bug reports from database
            $sql = "SELECT * FROM tb_question WHERE question_status = 1"; // Only select unanswered questions
            $result = $conn->query($sql);

            // Check if there are any unanswered bug reports
            if ($result->num_rows > 0) {
                // Output each unanswered bug report
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="bug-report">';
                    echo '<h3>' . $row["question_sender_name"] . '</h3>';
                    echo '<p><strong>อีเมลล์:</strong> ' . $row["question_sender_email"] . '</p>';
                    echo '<form id="sendEmailForm_' . $row["question_id"] . '" method="post" action="mailto:' . $row["question_sender_email"] . '">'; // Form for sending email
                    echo '<button type="button" class="btn btn-primary send-email-btn" onclick="sendEmail(' . $row["question_id"] . ')">Send Email</button>'; // Submit button
                    echo '</form>';
                    echo '<p><strong>รายละเอียด:</strong> ' . $row["question_content"] . '</p>';
                    echo '</div>';
                }
            } else {
                echo "<p>No unanswered bug reports found.</p>";
            }

            ?>
        </div>
        <!-- Display History bug reports -->
        <div>
            <hr>
            <br>
            <h2>ประวัติคำถาม</h2>
            <?php
            // Include database connection file
            require_once('../config/connect.php');

            // Fetch bug reports from database
            $sql = "SELECT * FROM tb_question WHERE question_status = 0"; // Only select unanswered questions
            $result = $conn->query($sql);

            // Check if there are any unanswered bug reports
            if ($result->num_rows > 0) {
                // Output each unanswered bug report
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="bug-report">';
                    echo '<h3>' . $row["question_sender_name"] . '</h3>';
                    echo '<p><strong>อีเมลล์:</strong> ' . $row["question_sender_email"] . '</p>';
                    echo '<form method="post" action="mailto:' . $row["question_sender_email"] . '">'; // Form for sending email
                    echo '</form>';
                    echo '<p><strong>รายละเอียด:</strong> ' . $row["question_content"] . '</p>';
                    echo '</div>';
                }
            } else {
                echo "<p>No unanswered bug reports found.</p>";
            }

            ?>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>



</body>

</html>