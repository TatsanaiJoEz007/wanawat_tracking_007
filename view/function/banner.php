<?php
require_once('../view/config/connect.php');

function imageToBase64($image_data)
{
    // Get image mime type
    $finfo = finfo_open();
    $mime_type = finfo_buffer($finfo, $image_data, FILEINFO_MIME_TYPE);
    finfo_close($finfo);

    // Determine file extension based on mime type
    switch ($mime_type) {
        case 'image/jpeg':
            $extension = 'jpg';
            break;
        case 'image/png':
            $extension = 'png';
            break;
        case 'image/gif':
            $extension = 'gif';
            break;
        default:
            $extension = 'jpg'; // Default to jpg if mime type is unknown
            break;
    }

    // Encode image data to base64
    $base64_image = 'data:image/' . $extension . ';base64,' . base64_encode($image_data);
    return $base64_image;
}
?>

<link href="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../view/assets/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300&display=swap" rel="stylesheet">
<style>
   .carousel-item {
        transition: opacity 8s ease-in-out; /* Adjust the transition duration */
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        opacity: 0;
    }

   .carousel-item.active {
        position: relative;
        opacity: 1;
    }

   .carousel-inner {
        position: relative;
        width: 100%;
        overflow: hidden;
        height: 600px; /* Adjust height as necessary */
    }

   .carousel-inner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @media (max-width: 768px) {
       .carousel-inner {
            height: 250px;
        }

       .carousel-inner img {
            height: 100%;
        }
    }

    /* Add these styles to make the transition smoother */
   .carousel-inner.carousel-item {
        transition-property: opacity;
    }

   .carousel-inner.carousel-item.active,
   .carousel-inner.carousel-item-next,
   .carousel-inner.carousel-item-prev {
        transition: opacity 8s ease-in-out;
    }

   .carousel-inner.active,
   .carousel-inner.carousel-item-next.left,
   .carousel-inner.carousel-item-prev.right {
        transition: opacity 8s ease-in-out;
    }

   .carousel-inner.carousel-item-next,
   .carousel-inner.carousel-item-prev {
        position: relative;
        transform: translate3d(0, 0, 0);
    }

   .carousel-inner.active.left,
   .carousel-inner.active.right {
        position: relative;
        transform: translate3d(0, 0, 0);
    }

   .carousel-inner.carousel-item-next.left,
   .carousel-inner.carousel-item-prev.right {
        transform: translate3d(-100%, 0, 0);
    }

   .carousel-inner.carousel-item-next.right,
   .carousel-inner.carousel-item-prev.left {
        transform: translate3d(100%, 0, 0);
    }
</style>


<div id="carouselExampleControls" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="8000">
    <div class="carousel-inner">
        <?php
        $sql = "SELECT * FROM tb_banner";
        $result = $conn->query($sql);
        $active = 'active'; // Set the first item as active
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="carousel-item '. $active. '">';
                $base64_image = imageToBase64($row['banner_img']);
                echo '<img src="'. $base64_image. '" class="d-block w-100" alt="Banner">';
                echo '</div>';
                $active = ''; // Clear the active class after the first item
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

<script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
