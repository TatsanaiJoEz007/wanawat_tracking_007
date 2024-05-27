<?php
require_once('../../config/connect.php');
if(isset($_POST['province_id'])){
    $province_id = $_POST['province_id'];
    $sql = "SELECT * FROM amphures WHERE province_id = '$province_id'";
    $query = mysqli_query($conn, $sql);
    
    echo '<option value="" disabled selected>เลือกอำเภอ</option>';
    while($result = mysqli_fetch_assoc($query)) {
        echo '<option value="'.$result['id'].'">'.$result['name_th'].'</option>';
    }
}
?>
