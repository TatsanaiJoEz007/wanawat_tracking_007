<?php
require_once('../config/connect.php');
?>

<?php
    // Query to get the count of questions with status 1
    $query = "SELECT COUNT(*) AS total_delivery_preparing FROM tb_delivery WHERE delivery_status = 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $total_delivery_preparing = $row['total_delivery_preparing'];

    // Close the result set
    mysqli_free_result($result);

    // Assign the count to the box
    $total_delivery_preparing_box = $total_delivery_preparing;
?>

<?php
    // Query to get the count of questions with status 1
    $query = "SELECT COUNT(*) AS total_sending2 FROM tb_delivery WHERE delivery_status = 2";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $total_sending2 = $row['total_sending2'];

    // Close the result set
    mysqli_free_result($result);

    // Assign the count to the box
    $total_sending2_box = $total_sending2;
?>

<?php
    // Query to get the count of questions with status 1
    $query = "SELECT COUNT(*) AS total_sending3 FROM tb_delivery WHERE delivery_status = 3";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $total_sending3 = $row['total_sending3'];

    // Close the result set
    mysqli_free_result($result);

    // Assign the count to the box
    $total_sending3_box = $total_sending3;
?>

<?php
    // Query to get the count of questions with status 1
    $query = "SELECT COUNT(*) AS total_sending4 FROM tb_delivery WHERE delivery_status = 4";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $total_sending4 = $row['total_sending4'];

    // Close the result set
    mysqli_free_result($result);

    // Assign the count to the box
    $total_sending4_box = $total_sending4;
?>

<?php
    $query = "SELECT COUNT(*) AS total_bill FROM tb_header WHERE bill_status = 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $total_bill = $row['total_bill'];

    mysqli_free_result($result);

    $total_bill_box = $total_bill;
?>

<?php
    $query = "SELECT COUNT(*) AS total_problem FROM tb_delivery WHERE delivery_status = 99";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $total_problem = $row['total_problem'];

    mysqli_free_result($result);

    $total_problem_box = $total_problem;
?>

<?php
    // Query to get the count of questions with status 1
    $query = "SELECT COUNT(*) AS total_delivery FROM tb_delivery ";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $total_delivery = $row['total_delivery'];

    // Close the result set
    mysqli_free_result($result);

    // Assign the count to the box
    $total_delivery_box = $total_delivery;
?>

<?php
    // Query to get the count of questions with status 1
    $query = "SELECT COUNT(*) AS total_history FROM tb_delivery WHERE delivery_status = 5";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $total_history = $row['total_history'];

    // Close the result set
    mysqli_free_result($result);

    // Assign the count to the box
    $total_history_box = $total_history;
?>

<?php
    $query = "SELECT COUNT(*) AS total_bill FROM tb_header WHERE bill_status = 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $total_bill = $row['total_bill'];

    mysqli_free_result($result);

    $total_bill_box = $total_bill;

    $query = "SELECT COUNT(*) AS total_line FROM tb_line WHERE line_status = 1";
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    $total_line = $row['total_line'];

    mysqli_free_result($result);

    $total_line_box = $total_line;
?>

<script src="https://cdn.lordicon.com/lordicon.js"></script>

<head>
    <link rel="stylesheet" href="../../view/Employee/function/dashboard/css/style.css">
</head>



<div class="container">

    <div class="row">


        <div class="row">
            <div class="col-lg-3 col-6">
                <div class="small-box" style="background-color: #CBD6CB;">
                    <div class="inner">
                        <h3><?php echo $total_bill_box ?></h3>
                        <p>จำนวนบิลทั้งหมด</p>
                        <br>
                    </div>
                    <div class="icon">
                        <lord-icon src="https://cdn.lordicon.com/ujxzdfjx.json" trigger="hover" state="hover-unfold" style="width:50px;height:50px"></lord-icon></lord-icon>
                    </div>
                    <a href="../../view/Employee/delivery_bill" class="small-box-footer">เพิ่มบิลใหม่ <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3><?php echo $total_delivery ?></h3>
                        <p>จำนวนบิลขนส่งทั้งหมด</p>
                        <br>
                    </div>
                    <div class="icon">
                        <lord-icon src="https://cdn.lordicon.com/okdadkfx.json" trigger="hover" state="hover-rotate-up-to-down" style="width:50px;height:50px">
                        </lord-icon>
                    </div>
                    <a href="../../view/Employee/statusbill" class="small-box-footer">ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box status-blue">
                    <div class="inner">
                        <h3><?php echo $total_delivery_preparing_box ?></h3>
                        <p>คำสั่งซื้อที่กำลังจัดเตรียม</p>
                        <br>
                    </div>
                    <div class="icon" style="background-color: #cce5ff;">
                        <lord-icon class="iconmini" src="https://cdn.lordicon.com/qnstsxhd.json" trigger="hover" colors="primary:#121331,secondary:#f0592e,tertiary:#ebe6ef,quaternary:#ffc738" style="width:50px;height:50px"></lord-icon>
                    </div>
                    <a href="../../view/Employee/preparing" class="small-box-footer">ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box status-yellow">
                    <div class="inner">
                        <h3><?php echo $total_sending2_box ?></h3>
                        <p>สถานะสินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า</p>
                    </div>
                    <div class="icon" style="background-color: #ffffcc;">
                        <lord-icon src="https://cdn.lordicon.com/amfpjnmb.json" trigger="hover" state="loop-cycle" colors="primary:#121331,secondary:#ebe6ef,tertiary:#3a3347,quaternary:#f0592e,quinary:#646e78" style="width:50px;height:50px"></lord-icon>
                    </div>
                    <a href="../../view/Employee/sending" class="small-box-footer">ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box status-grey">
                    <div class="inner">
                        <h3><?php echo $total_sending3_box ?></h3>
                        <p>สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลายทาง</p>
                    </div>
                    <div class="icon" style="background-color: #f0f2f5;">
                        <lord-icon src="https://cdn.lordicon.com/tdtlrbly.json" trigger="hover" stroke="bold" colors="primary:#121331,secondary:#e86830" style="width:50px;height:50px"></lord-icon>
                    </div>
                    <a href="../../view/Employee/sending" class="small-box-footer">ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box status-purple">
                    <div class="inner">
                        <h3><?php echo $total_sending4_box ?></h3>
                        <p>สถานะสินค้าที่กำลังนำส่งให้ลูกค้า</p>
                        <br>
                    </div>
                    <div class="icon" style="background-color: #dfe2fb;">
                        <lord-icon src="https://cdn.lordicon.com/eiekfffz.json" trigger="hover" stroke="bold" colors="primary:#f0592e,secondary:#ebe6ef,tertiary:#f0592e" style="width:50px;height:50px"></lord-icon>
                    </div>
                    <a href="../../view/Employee/sending" class="small-box-footer">ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box status-green">
                    <div class="inner">
                        <h3><?php echo $total_history_box ?></h3>
                        <p>คำสั่งซื้อที่จัดส่งสำเร็จแล้ว</p>
                        <br>

                    </div>
                    <div class="icon" style="background-color: #ccffcc;">
                        <lord-icon src="https://cdn.lordicon.com/qxqvtswi.json" trigger="hover" state="bold" colors="primary:#f0592e,secondary:#f0592e,tertiary:#000000" style="width:50px;height:50px"></lord-icon>
                    </div>
                    <a href="../../view/Employee/history" class="small-box-footer">ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>

            <div class="col-lg-3 col-6">
                <div class="small-box status-red">
                    <div class="inner">
                        <h3><?php echo $total_problem_box ?></h3>
                        <p>จำนวนบิลที่มีปัญหา</p>
                        <br>
                    </div>
                    <div class="icon" style="background-color: #ffcccc;">
                        <lord-icon src="https://cdn.lordicon.com/jxzkkoed.json" trigger="hover" state="hover-enlarge" style="width:60px;height:50px"></lord-icon>
                    </div>
                    <a href="../../view/Employee/problem" class="small-box-footer">ดูเพิ่มเติม <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </div>