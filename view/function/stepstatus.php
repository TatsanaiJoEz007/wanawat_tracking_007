<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Tracking</title>

    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=6675a897f75dab0019adeb8f&product=inline-share-buttons&source=platform" async="async"></script>

    <style>
        body {
            color: #000;
            overflow-x: hidden;
            height: 100%;
            background-color: #FFF;
            background-repeat: no-repeat;
        }

        .card {
            background-color: #FFE3BF;
            padding-bottom: 20px;
            margin-top: 90px;
            margin-bottom: 90px;
            border-radius: 10px;
        }

        .top {
            padding-top: 40px;
            padding-left: 13%;
            padding-right: 13%;
        }

        .order {
            color: #FF7043;
        }

        #progressbar {
            margin-bottom: 30px;
            overflow: hidden;
            color: #FF7043;
            padding-left: 0px;
            margin-top: 30px;
            border-radius: 10px;
            position: relative;
        }

        #progressbar li {
            list-style-type: none;
            font-size: 13px;
            width: 20%;
            float: left;
            position: relative;
            font-weight: 400;
        }

        #progressbar li:before {
            width: 40px;
            height: 40px;
            line-height: 45px;
            display: block;
            font-size: 20px;
            background: #FFAB91;
            border-radius: 50%;
            margin: auto;
            padding: 0px;
            content: "\f10c";
            color: #fff;
            font-family: FontAwesome;
        }

        #progressbar li:after {
            content: '';
            width: 100%;
            height: 12px;
            background: #FFAB91;
            position: absolute;
            left: 50%;
            top: 16px;
            z-index: -1;
        }

        #progressbar li.active:before,
        #progressbar li.active:after {
            background: #FF7043;
        }

        #progressbar li.active:before {
            content: "\f00c";
        }

        /* Remove the line for the last step */
        #progressbar li.active.step5:after {
            display: none;
        }

        .icon {
            width: 40px;
            height: 40px;
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
        }

        .icon-content {
            text-align: center;
            margin-top: 10px;
        }

        ::-webkit-scrollbar {
            width: 12px;
            /* Adjust width for vertical scrollbar */
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            /* Color for scrollbar thumb */
            border-radius: 10px;
            /* Rounded corners for scrollbar thumb */
        }

        /* Container Styling */
        .home-section {
            max-height: 100vh;
            /* Adjust height as needed */
            overflow-y: auto;
            /* Allow vertical scroll */
            overflow-x: hidden;
            /* Prevent horizontal scroll */
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        @media screen and (max-width: 1024px) {
            .top {
                padding-left: 5%;
                padding-right: 5%;
            }

            #progressbar li {
                font-size: 11px;
                width: 20%;
            }

            #progressbar li:before {
                width: 30px;
                height: 30px;
                line-height: 35px;
                font-size: 16px;
            }

            .icon {
                width: 30px;
                height: 30px;
                top: -45px;
            }

            .icon-content {
                margin-top: 45px;
            }
        }

        @media screen and (max-width: 768px) {
            .top {
                padding-left: 3%;
                padding-right: 3%;
            }

            #progressbar li {
                font-size: 10px;
                width: 20%;
            }

            #progressbar li:before {
                width: 25px;
                height: 25px;
                line-height: 30px;
                font-size: 14px;
            }

            .icon {
                width: 25px;
                height: 25px;
                top: -40px;
            }

            .icon-content {
                margin-top: 40px;
            }
        }

        @media screen and (max-width: 320px) {
            .top {
                padding-left: 1%;
                padding-right: 1%;
            }

            #progressbar li {
                font-size: 9px;
                width: 20%;
            }

            #progressbar li:before {
                width: 20px;
                height: 20px;
                line-height: 25px;
                font-size: 12px;
            }

            .icon {
                width: 20px;
                height: 20px;
                top: -35px;
            }

            .icon-content {
                margin-top: 35px;
            }
        }
    </style>
</head>

<body>
    <div class="container px-1 px-md-4 py-5 mx-auto ">
        <div class="card">
            <div class="row d-flex justify-content-between px-3 top">
                <div class="d-flex">
                <?php
                    // Establish database connection
                    require_once('config/connect.php');

                    // Check connection
                    if ($conn->connect_error) {
                        die("Connection failed: " . $conn->connect_error);
                    }

                    // Fetch delivery number or bill number from URL parameter
                    $trackingId = htmlspecialchars($_GET['trackingId']);

                    // Initialize variables
                    $delivery_number = null;
                    $delivery_date = null;
                    $delivery_status = null;
                    $searchByBillNumber = false;

                    // Query to fetch details from tb_delivery using delivery_number
                    $sql = "SELECT d.delivery_number, d.delivery_date, d.delivery_status 
                            FROM tb_delivery AS d
                            WHERE d.delivery_number = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $trackingId);
                    $stmt->execute();
                    $stmt->bind_result($delivery_number, $delivery_date, $delivery_status);
                    $stmt->fetch();
                    $stmt->close();

                    // If no result found, try searching by bill_number
                    if (!$delivery_number) {
                        $sql = "SELECT di.delivery_id, di.bill_number 
                                FROM tb_delivery_items AS di
                                WHERE di.bill_number = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("s", $trackingId);
                        $stmt->execute();
                        $stmt->bind_result($delivery_id, $bill_number);
                        $stmt->fetch();
                        $stmt->close();

                        if ($bill_number) {
                            $searchByBillNumber = true;
                            // Fetch delivery_number, delivery_date, and delivery_status from tb_delivery using delivery_id
                            $sql = "SELECT d.delivery_number, d.delivery_date, d.delivery_status 
                                    FROM tb_delivery AS d
                                    INNER JOIN tb_delivery_items AS di ON d.delivery_id = di.delivery_id
                                    WHERE di.bill_number = ?";
                    
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $delivery_id);
                            $stmt->execute();
                            $stmt->bind_result($delivery_number, $delivery_date, $delivery_status);
                            $stmt->fetch();
                            $stmt->close();
                        }
                    }

                    // Determine active steps based on delivery status
                    $active_steps = [];
                    if ($delivery_number && $delivery_status !== null) {
                        $active_steps = range(1, $delivery_status);
                    } else {
                        echo "<b>ไม่พบวันที่คำสั่งซื้อเข้าระบบ</b>"; // If neither delivery number nor bill number is found
                    }

                    $show_error = false;
                    if ($delivery_status == 99) {
                        $show_error = true;
                    }

                    // Close connection
                    $conn->close();
                    ?>
                </div>
            </div>
            <!-- Add class 'active' to progress -->
            <div class="row d-flex justify-content-center">
                <div class="col-10">
                    <h5>หมายเลขบิล <span class='order font-weight-bold'><?php echo "$trackingId" ?></span></h5>
                    <?php if ($delivery_date) : ?>
                        <b> &nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; วันที่คำสั่งซื้อเข้าระบบ: <?php echo date('Y-m-d H:i:s', strtotime($delivery_date)) ?> </b>
                    <?php endif; ?>
                    <ul id="progressbar" class="text-center">
                        <li class="step1 <?php if (in_array(1, $active_steps)) echo 'active'; ?>">
                            <lord-icon class="iconmini" src="https://cdn.lordicon.com/qnstsxhd.json" trigger="hover" colors="primary:#121331,secondary:#f0592e,tertiary:#ebe6ef,quaternary:#ffc738" style="width:50px;height:50px"></lord-icon>
                            <div class="icon-content"> คำสั่งซื้อเข้าระบบ </div>
                        </li>
                        <li class="step2 <?php if (in_array(2, $active_steps)) echo 'active'; ?>">
                            <lord-icon src="https://cdn.lordicon.com/amfpjnmb.json" trigger="hover" state="loop-cycle" colors="primary:#121331,secondary:#ebe6ef,tertiary:#3a3347,quaternary:#f0592e,quinary:#646e78" style="width:50px;height:50px">
                            </lord-icon>
                            <div class="icon-content"> กำลังจัดส่งไปยังปลายทาง </div>
                        </li>
                        <li class="step3 <?php if (in_array(3, $active_steps)) echo 'active'; ?>">
                            <lord-icon src="https://cdn.lordicon.com/tdtlrbly.json" trigger="hover" stroke="bold" colors="primary:#121331,secondary:#e86830" style="width:50px;height:50px">
                            </lord-icon>
                            <div class="icon-content"> ถึงศูนย์กระจายสินค้าปลายทาง </div>
                        </li>
                        <li class="step4 <?php if (in_array(4, $active_steps)) echo 'active'; ?>">
                            <lord-icon src="https://cdn.lordicon.com/eiekfffz.json" trigger="hover" stroke="bold" colors="primary:#f0592e,secondary:#ebe6ef,tertiary:#f0592e" style="width:50px;height:50px">
                            </lord-icon>
                            <div class="icon-content"> กำลังนำส่งให้ลูกค้า </div>
                        </li>
                        <li class="step5 <?php if (in_array(5, $active_steps)) echo 'active'; ?>">
                            <lord-icon src="https://cdn.lordicon.com/qxqvtswi.json" trigger="hover" state="bold" colors="primary:#f0592e,secondary:#f0592e,tertiary:#000000" style="width:50px;height:50px">
                            </lord-icon>
                            <div class="icon-content"> จัดส่งสำเร็จ </div>
                        </li>
                    </ul>
                    <?php if ($show_error) : ?>
                        <center>
                            <lord-icon src="https://cdn.lordicon.com/jxzkkoed.json" trigger="hover" state="hover-enlarge" colors="primary:#121331,secondary:#ffc738,tertiary:#f0952e" style="width:80px;height:80px">
                        </center>
                        </lord-icon>
                        <div class="alert alert-danger mt-3" role="alert">
                            <p>
                                <center>There is a problem with this delivery. Please contact customer support for assistance. </center>
                            </p>
                            <p>
                                <center>การจัดส่งนี้มีปัญหา กรุณาติดต่อเจ้าหน้าที่ดูแลลูกค้าเพื่อขอความช่วยเหลือ </center>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="sharethis-inline-share-buttons"></div>

</body>
<script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        // Assuming delivery_status is sent from PHP
        var deliveryStatus = parseInt('<?php echo $delivery_status ?? 0; ?>'); // Use nullish coalescing operator
        var active_steps = <?php echo json_encode($active_steps); ?>;

        // Remove all active classes first
        $('#progressbar li').removeClass('active');

        // Add active class to the appropriate steps based on delivery status
        active_steps.forEach(function(step) {
            $('#progressbar li.step' + step).addClass('active');
        });
    });
</script>


</html>