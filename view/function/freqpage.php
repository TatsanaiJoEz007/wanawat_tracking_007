<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-nicXN0XQ8FqHgDYdPZj+3mMp3CJtV3rbfn/ukn8fnCRyCJlDoVJy4bhvDRqVLyuD8h1n1HtYjv3kzxng+jc0zg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
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
            transition: 0.4s;
            font-size: 18px;
            font-weight: bold;
            background-color: transparent;
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
</style>

<div class="container">
    <br>
    <h1>คำถามที่พบบ่อย</h1>
    <div class="accordion-container">
        <button class="accordion">หากสินค้าขึ้นสถานะว่ากำลังนำส่งหมายถึงอะไร?<i class="fas fa-caret-down"></i></button>
        <div class="panel">
            <div class="panel-content">
                <div class="box-widget">
                    <p>&nbsp;&nbsp;&nbsp; สถานะ "กำลังนำส่งนั้น" หมายถึง ขณะนี้สินค้ากำลังไปส่งให้ถึงมือคุณลูกค้าตามที่อยู่ที่ลูกค้าระบุไว้ และจะถึงภายในวันที่ขึ้นสถานะ</p>
                </div>
            </div>
        </div>

        <button class="accordion">หากสินค้าไม่ขึ้นสถานะหลังจากกดสั่งซื้อ หมายถึงอะไร?<i class="fas fa-caret-down"></i></button>
        <div class="panel">
            <div class="panel-content">
                <div class="box-widget">
                    <p>บลาๆๆๆๆ</p>
                </div>
            </div>
        </div>

        <button class="accordion">เลข Tracking นำมาจากไหน?<i class="fas fa-caret-down"></i></button>
        <div class="panel">
            <div class="panel-content">
                <div class="box-widget">
                    <p>บลาๆๆๆ</p>
                </div>
            </div>
        </div>
        <!-- Add more questions here -->
    </div>
</div>


    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
