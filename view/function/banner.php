<?php
require_once('../view/config/connect.php');
?>

<link href="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../view/assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300&display=swap" rel="stylesheet">
<style>
.carousel-inner img {
    width: 100%;
    height: 500px;
    object-fit: cover;
}



@media (max-width: 768px) {
    .carousel-inner img {
        width: 1000px;
        height: 250px;
    }
}
</style>

<div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <?php
        $sql = "SELECT * FROM tb_banner";
        $result = $conn->query($sql);
        $active = 'active'; // ตัวแปรสำหรับกำหนดไอเทมแรกเป็น active
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="carousel-item ' . $active . '">';
                echo '<img src="admin' . $row['banner_img'] . '" class="d-block w-100" alt="Banner" style="object-fit: cover;" width="1000" height="700">';
                echo '</div>';
                $active = ''; // ล้างค่าตัวแปร active หลังจากไอเทมแรก
            }
        } else {
            echo '<p>No banners found</p>';
        }
        ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
