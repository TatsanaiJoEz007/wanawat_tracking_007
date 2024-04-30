<?php

include_once("../config/database.php");

// Instantiate Database class
$database = new Database();
$db = $database->getConnection();

define('tableName', 'user');
$userData = $_POST;

loginUser($db, $userData);

function loginUser($db, $userData) {

    $email   = $userData['email'];
    $password = $userData['password'];
   
    if(!empty($email) && !empty($password)){

        // Use prepared statements to prevent SQL injection
        $query = "SELECT email, password, userlevel FROM ".tableName." WHERE email = :email AND password = :password";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            session_start();
            $_SESSION['userid'] = $email;
            if ($user['userlevel'] == 'admin') {
                // Redirect to indexadmin.php if user is admin
                header("Location: http://localhost/wanawat/Wanawat/public/indexadmin.php");
            } else {
                // Redirect to index.php for regular users
                header("Location: http://localhost/wanawat/Wanawat/public/index.php");
            }
            exit(); // Ensure script stops execution after redirection
        } else {
            echo "Wrong email and password";
        }
     
   } else {
      echo "All Fields are required";
   }
}
?>