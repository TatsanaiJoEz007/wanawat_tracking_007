<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Delivery Tracking</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/clipboard@2.0.8/dist/clipboard.min.js"></script>
    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=6675a897f75dab0019adeb8f&product=inline-share-buttons&source=platform" async="async"></script>
    <link href="../view/function/css/step.css" rel="stylesheet">
</head>

<body>
    <div class="container px-1 px-md-4 py-5 mx-auto">
        <div class="card">
            <div class="row d-flex justify-content-between px-3 top">
                <div class="d-flex">
                    <?php require_once "../view/function/stepstatus/search_delivery.php" ?>
                </div>
            </div>
            <div class="row d-flex justify-content-center">
                <div class="col-10">
                    <h5>หมายเลขบิล <span class='order font-weight-bold'><?php echo htmlspecialchars($trackingId); ?></span></h5>
                    <?php if ($delivery_date) : ?>
                        <b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;วันที่คำสั่งซื้อเข้าระบบ: <?php echo date('Y-m-d H:i:s', strtotime($delivery_date)); ?></b>
                    <?php endif; ?>
                    <ul id="progressbar" class="text-center">
                        <li class="step1 <?php if (in_array(1, $active_steps)) echo 'active'; ?>">
                            <lord-icon class="iconmini" src="https://cdn.lordicon.com/qnstsxhd.json" trigger="hover" colors="primary:#121331,secondary:#f0592e,tertiary:#ebe6ef,quaternary:#ffc738" style="width:50px;height:50px"></lord-icon>
                            <div class="icon-content">คำสั่งซื้อเข้าระบบ</div>
                        </li>
                        <li class="step2 <?php if (in_array(2, $active_steps)) echo 'active'; ?>">
                            <lord-icon src="https://cdn.lordicon.com/amfpjnmb.json" trigger="hover" state="loop-cycle" colors="primary:#121331,secondary:#ebe6ef,tertiary:#3a3347,quaternary:#f0592e,quinary:#646e78" style="width:50px;height:50px"></lord-icon>
                            <div class="icon-content">กำลังจัดส่งไปยังปลายทาง</div>
                        </li>
                        <li class="step3 <?php if (in_array(3, $active_steps)) echo 'active'; ?>">
                            <lord-icon src="https://cdn.lordicon.com/tdtlrbly.json" trigger="hover" stroke="bold" colors="primary:#121331,secondary:#e86830" style="width:50px;height:50px"></lord-icon>
                            <div class="icon-content">ถึงศูนย์กระจายสินค้าปลายทาง</div>
                        </li>
                        <li class="step4 <?php if (in_array(4, $active_steps)) echo 'active'; ?>">
                            <lord-icon src="https://cdn.lordicon.com/eiekfffz.json" trigger="hover" stroke="bold" colors="primary:#f0592e,secondary:#ebe6ef,tertiary:#f0592e" style="width:50px;height:50px"></lord-icon>
                            <div class="icon-content">กำลังนำส่งให้ลูกค้า</div>
                        </li>
                        <li class="step5 <?php if (in_array(5, $active_steps)) echo 'active'; ?>">
                            <lord-icon src="https://cdn.lordicon.com/qxqvtswi.json" trigger="hover" state="bold" colors="primary:#f0592e,secondary:#f0592e,tertiary:#000000" style="width:50px;height:50px"></lord-icon>
                            <div class="icon-content">จัดส่งสำเร็จ</div>
                        </li>
                    </ul>
                    <?php if ($show_error) : ?>
                        <center>
                            <lord-icon src="https://cdn.lordicon.com/jxzkkoed.json" trigger="hover" state="hover-enlarge" colors="primary:#121331,secondary:#ffc738,tertiary:#f0952e" style="width:80px;height:80px"></lord-icon>
                        </center>
                        <div class="alert alert-danger mt-3" role="alert">
                            <p>
                                <center>There is a problem with this delivery. Please contact customer support for assistance.</center>
                            </p>
                            <p>
                                <center>การจัดส่งนี้มีปัญหา กรุณาติดต่อเจ้าหน้าที่ดูแลลูกค้าเพื่อขอความช่วยเหลือ</center>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="sharethis-inline-share-buttons"></div>
        </div>
    </div>
</body>
<script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var active_steps = <?php echo json_encode($active_steps); ?>;
        active_steps.forEach(function(step) {
            document.querySelector("#progressbar li.step" + step).classList.add("active");
        });
    });
</script>

</html>