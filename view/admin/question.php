<!DOCTYPE html>
<html lang="th">

<head>
    <title>Manage - Question</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <!-- เรียกใช้ Bootstrap CSS จาก CDN -->
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .accordion-container {
            margin-top: 20px;
        }

        .accordion {
            cursor: pointer;
            border: none;
            outline: none;
            font-size: 18px;
            font-weight: bold;
            transition: 0.3s;
            width: 100%;
            text-align: left;
            color: #F0592E;
        }

        .accordion:hover {
            color: #FF7E47;
        }

        .panel {
            padding: 0 18px;
            background-color: white;
            display: none;
            overflow: hidden;
        }

        .panel.show {
            display: block;
        }

        .accordion:after {
            content: '\002B';
            color: #777;
            font-weight: bold;
            float: right;
            margin-left: 5px;
        }

        .accordion.active:after {
            content: "\2212";
        }

        .panel-content {
            border-top: 2px solid #F0592E;
            padding-top: 10px;
        }

        .edit-icon {
            float: right;
            margin-top: -5px;
            color: #888;
            cursor: pointer;
        }

        .edit-icon:hover {
            color: #333;
        }

        .editable {
            border: 1px solid #ccc;
            padding: 5px;
            width: 100%;
            min-height: 100px;
            resize: vertical; /* Allow vertical resizing */
            border-radius: 10px;
            transition: none; /* Remove transition */
        }

        .submit-btn {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php require_once('function/sidebar.php'); ?>
    <div class="container">
        <br>
        <h1>คำถามที่พบบ่อย</h1>
        <div class="accordion-container">
            <div class="accordion">
                หากสินค้าขึ้นสถานะว่ากำลังนำส่งหมายถึงอะไร?
                <i class="bi bi-pencil edit-icon"></i>
                <i class="bi bi-caret-down"></i>
            </div>
            <div class="panel">
                <div class="panel-content">
                    <div class="box-widget">
                        <textarea class="editable">&nbsp;&nbsp;&nbsp; สถานะ "กำลังนำส่งนั้น" หมายถึง ขณะนี้สินค้ากำลังไปส่งให้ถึงมือคุณลูกค้าตามที่อยู่ที่ลูกค้าระบุไว้ และจะถึงภายในวันที่ขึ้นสถานะ</textarea>
                        <button class="btn btn-primary submit-btn">Submit Edit</button>
                    </div>
                </div>
            </div>

            <div class="accordion">
                หากสินค้าไม่ขึ้นสถานะหลังจากกดสั่งซื้อ หมายถึงอะไร?
                <i class="bi bi-pencil edit-icon"></i>
                <i class="bi bi-caret-down"></i>
            </div>
            <div class="panel">
                <div class="panel-content">
                    <div class="box-widget">
                        <textarea class="editable">บลาๆๆๆๆ</textarea>
                        <button class="btn btn-primary submit-btn">Submit Edit</button>
                    </div>
                </div>
            </div>

            <div class="accordion">
                เลข Tracking นำมาจากไหน?
                <i class="bi bi-pencil edit-icon"></i>
                <i class="bi bi-caret-down"></i>
            </div>
            <div class="panel">
                <div class="panel-content">
                    <div class="box-widget">
                        <textarea class="editable">บลาๆๆๆ</textarea>
                        <button class="btn btn-primary submit-btn">Submit Edit</button>
                    </div>
                </div>
            </div>
            <!-- Add more questions here -->
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/bootstrap-icons.min.js"></script>
    <script>
        var acc = document.getElementsByClassName("accordion");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function () {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } else {
                    panel.style.display = "block";
                }
            });
        }
    </script>
</body>

</html>
