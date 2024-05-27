<?php
require_once('../../../config/connect.php');
session_start(); // Start the session

// Function to get user profile picture
function Profilepic($conn, $userId)
{
    $sql = "SELECT user_img FROM tb_user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
}

// Function to convert image data to base64
function base64img($imageData)
{
    return 'data:image/jpeg;base64,' . base64_encode($imageData);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    // Fetch user data along with province_id, district_id, and amphure_id
    $sql = "SELECT u.*, p.id AS province_id, p.name_th AS province_name, d.id AS district_id, d.name_th AS district_name, a.id AS amphure_id, a.name_th AS amphure_name
    FROM tb_user u
    LEFT JOIN provinces p ON u.province_id = p.id
    LEFT JOIN districts d ON u.district_id = d.id
    LEFT JOIN amphures a ON u.amphure_id = a.id
    WHERE u.user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Get user profile picture
        $myprofile = Profilepic($conn, $row['user_id']);
        // Set default avatar path
        $defaultAvatarPath = '../../view/assets/img/logo/mascot.png';
        // Check if user image is set
        if (!empty($myprofile['user_img'])) {
            $imageBase64 = base64img($myprofile['user_img']);
        } else {
            $imageBase64 = $defaultAvatarPath;
        }

        // Prepare user data array
        $userData = array(
            'user_id' => $row['user_id'],
            'user_firstname' => $row['user_firstname'],
            'user_lastname' => $row['user_lastname'],
            'user_email' => $row['user_email'],
            'user_tel' => $row['user_tel'],
            'user_address' => $row['user_address'],
            'province' => array(
                'id' => $row['province_id'],
                'name_th' => $row['province_name']
            ),
            'district' => array(
                'id' => $row['district_id'],
                'name_th' => $row['district_name']
            ),
            'amphure' => array(
                'id' => $row['amphure_id'],
                'name_th' => $row['amphure_name']
            ),
            

            'user_img' => $imageBase64
        );

        // Output user data as JSON
        echo json_encode($userData);
    } else {
        // No user found with the given ID
        echo json_encode(array('error' => 'User not found'));
    }
} else {
    // Invalid request method or missing user ID
    echo json_encode(array('error' => 'Invalid request'));
}
