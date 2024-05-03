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
            position: relative; /* Needed for positioning button */
        }

        .send-email-btn {
            position: absolute;
            top: 10px;
            right: 10px;
        }
    </style>
</head>
<body>
    <?php require_once('function/sidebar.php'); ?>
    
    <div class="container">
        <br>
        <h2>คำถาม และปัญหาในการใช้งาน</h2>
        
        <!-- Display bug reports -->
        <div>
            <?php
            // Example bug reports, replace with dynamic data from database
            $bugReports = [
                ["name" => "John Doe", "email" => "john@example.com", "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam at turpis a nunc pulvinar molestie."],
                ["name" => "Jane Smith", "email" => "jane@example.com", "description" => "Phasellus at risus augue. Pellentesque euismod odio et ligula egestas, eu fringilla nisi dignissim."],
            ];

            foreach ($bugReports as $report) {
                echo '<div class="bug-report">';
                echo '<h3>' . $report["name"] . '</h3>';
                echo '<form method="post" action="send_email.php">'; // Form for sending email
                echo '<input type="hidden" name="email" value="' . $report["email"] . '">'; // Hidden input to pass email address
                echo '<button type="submit" class="btn btn-primary send-email-btn"><a href="">Send Email</a></button>'; // Submit button
                echo '</form>';
                echo '<p><strong>Description:</strong> ' . $report["description"] . '</p>';
                echo '</div>';
            }
            ?>
        </div>
    </div>
</body>
</html>
