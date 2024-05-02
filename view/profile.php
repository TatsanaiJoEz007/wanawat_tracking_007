<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Assuming 'function/head.php' includes necessary meta tags, stylesheets, etc. -->
    <?php require_once('function/head.php'); ?>

    <style>
        body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}


.profile-container {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-grow: 1;
    margin-top: 50px;
}

.profile-picture {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    overflow: hidden;
}

.profile-picture img {
    width: 100%;
    height: auto;
    border-radius: 50%;
}

.profile-details {
    margin-left: 20px;
    max-width: 400px; /* Adjust as needed */
}

.profile-details h2 {
    margin-top: 0;
}

.profile-details p {
    margin: 5px 0;
}

    </style>
</head>

<body>
    <nav>
        <?php require_once('function/navindex.php'); ?>
    </nav>

    <div class="profile-container">
        <div class="profile-picture">
            <?php 
                // Assuming $userProfilePicture contains the path to the user's profile picture
                echo '<img src="' . $userProfilePicture . '" alt="User Profile Picture">';
            ?>
        </div>
        <div class="profile-details">
            <h2>User's Name</h2>
            <p>Email: user@example.com</p>
            <p>Location: City, Country</p>
            <p>About: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
        </div>
    </div>

    <footer>
        <?php require_once('function/footer.php'); ?>
    </footer>
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
