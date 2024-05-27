<?php
require_once('../../config/connect.php');
if(isset($_POST['amphure_id'])){
    $amphure_id = $_POST['amphure_id'];
    $sql = "SELECT * FROM districts WHERE amphure_id = '$amphure_id'";
    $query = mysqli_query($conn, $sql);
    
    echo '<option value="" disabled selected>เลือกตำบล</option>';
    while($result = mysqli_fetch_assoc($query)) {
        echo '<option value="'.$result['id'].'">'.$result['name_th'].'</option>';
    }
}
?>
