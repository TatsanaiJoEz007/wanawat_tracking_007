
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
    integrity="sha512-nicXN0XQ8FqHgDYdPZj+3mMp3CJtV3rbfn/ukn8fnCRyCJlDoVJy4bhvDRqVLyuD8h1n1HtYjv3kzxng+jc0zg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    .container {
        max-width: auto !important;
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
        font-size: 22px;
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
        font-size: 18px;
    }
</style>

<div class="container">
    <br>
    <hr>
    <h1>คำถามที่พบบ่อย</h1>
    
    <?php require_once 'function/func_freq.php'; ?>

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
    </div>