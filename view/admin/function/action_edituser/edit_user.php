<?php
require_once('../../../config/connect.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $firstname = $_POST['adduser-firstname'];
    $lastname = $_POST['adduser-lastname'];
    $email = $_POST['adduser-email'];
    $tel = $_POST['adduser-tel'];
    $address = $_POST['adduser-address'];
    $province_id = $_POST['province_id'];
    $amphure_id = $_POST['amphure_id'];
    $district_id = $_POST['district_id'];
    $status = $_POST['user_status'];

    // Handle file upload if a new image is uploaded
    if (isset($_FILES['user_img']) && $_FILES['user_img']['error'] === UPLOAD_ERR_OK) {
        $imgData = file_get_contents($_FILES['user_img']['tmp_name']);
        $sql = "UPDATE tb_user SET user_firstname=?, user_lastname=?, user_email=?, user_tel=?, user_address=?, province_id=?, amphure_id=?, district_id=?, user_img=?, user_status=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssiibii", $firstname, $lastname, $email, $tel, $address, $province_id, $amphure_id, $district_id, $imgData, $status, $user_id);
    } else {
        $sql = "UPDATE tb_user SET user_firstname=?, user_lastname=?, user_email=?, user_tel=?, user_address=?, province_id=?, amphure_id=?, district_id=?, user_status=? WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssiibii", $firstname, $lastname, $email, $tel, $address, $province_id, $amphure_id, $district_id, $status, $user_id);
    }
    
    if ($stmt->execute()) {
        // Redirect to the user management page
        header('Location: ../manage_user.php');
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
