<?php
// Assuming you have a database connection established
require_once('../config/connect.php');
// Query to get the count of questions with status 1
$query = "SELECT COUNT(*) AS total_questions FROM tb_question WHERE question_status = 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_questions = $row['total_questions'];

// Close the result set
mysqli_free_result($result);

// Assign the count to the box
$total_questions_box = $total_questions;
?>

<?php
// Assuming you have a database connection established
require_once('../config/connect.php');
// Query to get the count of questions with status 1
$query = "SELECT COUNT(*) AS total_user FROM tb_user WHERE user_status = 1";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$total_user = $row['total_user'];

// Close the result set
mysqli_free_result($result);

// Assign the count to the box
$total_user_box = $total_user;
?>

<?php
require_once('../config/connect.php');
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

<style>
    /* Add your custom styles here */
    body {
        padding-top: 20px;

    }

    .small-box {
        margin-bottom: 20px;
        border: 1px solid #dcdcdc;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
        transition: transform 0.3s;
    }

    .small-box:hover {
        transform: translateY(-5px);
    }

    .small-box .inner {
        padding: 20px;
    }

    .small-box h3,
    .small-box p {
        color: #333333;
    }

    .small-box .icon {
        padding: 15px;
        border-radius: 0 10px 10px 0;
    }

    .small-box a.small-box-footer {
        color: #333333;
        display: block;
        padding: 10px;
        text-align: right;
        text-decoration: none;
    }

    .bg-green {
        background-color: #4CC6AF;
    }

    .bg-blue {
        background-color: #1AB8EF;
    }

    .bg-yellow {
        background-color: #E97E63;
    }

    .bg-red {
        background-color: #F73F62;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-blue">
                <div class="inner">
                    <h3><?php echo $total_questions_box ?></h3>
                    <p>คำถามที่ยังไม่ได้ตอบ</p>
                </div>
                <div class="icon" style="background-color: #F2C93F;">
                    <i class="fas fa-users" style="color: #FFFFFF;"></i>
                </div>
                <a href="../admin/contact.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?php echo $total_user_box ?></h3>
                    <p>จำนวนผู้ใช้งานทั้งหมด</p>
                </div>
                <div class="icon" style="background-color: #E18B77;">
                    <i class="fas fa-chart-line" style="color: #FFFFFF;"></i>
                </div>
                <a href="#" class="small-box-footer" style="color: #4CC6AF;"> <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>Header : <?php echo $total_bill_box ?></h3>
                    <h3>Line : <?php echo $total_line_box ?></h3>
                    <p>จำนวน Header และ Line</p>
                </div>
                <div class="icon" style="background-color: #05433E;">
                    <i class="fas fa-user-plus" style="color: #FFFFFF;"></i>
                </div>
                <a href="../admin/table_header.php" class="small-box-footer">ดูบิลทั้งหมด <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>65</h3>
                    <p>จำนวนบิลที่ส่ง</p>
                </div>
                <div class="icon" style="background-color: #22D4BE;">
                    <i class="fas fa-chart-pie" style="color: #FFFFFF;"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
</div>