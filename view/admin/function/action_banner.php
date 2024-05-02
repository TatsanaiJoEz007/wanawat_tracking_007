<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once('../../config/connect.php');




if (isset($_POST['addBanner'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $sub_title = $_POST['sub_title'];
    $link = $_POST['link'];
    $filename = isset($_FILES['file']['name']) ? $_FILES['file']['name'] : '';
    $old_img = isset($_POST['old_img']) ? $_POST['old_img'] : '';

    /* Location */
    $location = "../../assets/upload/" . $filename;
    $imageFileType = pathinfo($location, PATHINFO_EXTENSION);
    $imageFileType = strtolower($imageFileType);

    /* Valid extensions */
    $valid_extensions = array("jpg", "jpeg", "png", "webp");

    if (!empty($_POST['id'])) {
        if (!empty($filename)) {
            $response = 1;
            $file = rand(1000, 100000) . "-" . $filename;
            $new_file_name = strtolower($file);
            $fainal = str_replace(' ', '-', $new_file_name);
            $newname = 'upload/' . $fainal;
            /* Location */
            $location = "../assets/upload/" . $fainal;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
                $sql = "UPDATE tb_banner SET title = '$title', sub_title = '$sub_title', link = '$link', img = '$newname' WHERE id = '$id'";
                $query = $conn->query($sql);
            }
        } else {
            $response = 1;
            $sql = "UPDATE tb_banner SET title = '$title', sub_title = '$sub_title', img = '$old_img',link = '$link' WHERE id = '$id'";
            $query = $conn->query($sql);
        }
    } else {

        /* Check file extension */
        if (in_array(strtolower($imageFileType), $valid_extensions)) {
            /* Upload file */
            $file = rand(1000, 100000) . "-" . $filename;
            $new_file_name = strtolower($file);
            $fainal = str_replace(' ', '-', $new_file_name);
            $newname = 'upload/' . $fainal;
            /* Location */
            $location = "../assets/upload/" . $fainal;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
                $sql = "INSERT INTO tb_banner(title,sub_title,link,img) 
                        VALUES('$title','$sub_title','$link','$newname')";
                $query = $conn->query($sql);
                $response = 0;
            }
        } else {
            $response = 3;
        }
    }
    echo $response;
}


if (isset($_POST['editBanner'])) {
    $id = $_POST['id'];
    $sql = "SELECT * FROM tb_banner WHERE id = '$id'";
    $query = $conn->query($sql);
    $row = $query->fetch_array();
    echo json_encode($row);
}

if (isset($_POST['delBanner'])) {
    $conn->query("DELETE FROM tb_banner WHERE id = '$_POST[id]'");
}


?>








