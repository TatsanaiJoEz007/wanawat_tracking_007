<head>
    <?php require_once('function/head.php'); ?>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
        body {
            color: #000;
            overflow-x: hidden;
            height: 100%;
            background-color: #FFCC80;
            background-repeat: no-repeat;
        }

        .card {
            z-index: 0;
            background-color: #FFF3E0;
            padding-bottom: 20px;
            margin-top: 90px;
            margin-bottom: 90px;
            border-radius: 10px;
        }

        .top {
            padding-top: 40px;
            padding-left: 13% !important;
            padding-right: 13% !important;
        }

        .order{
            color: #FF7043;
        }

        /* Icon progress bar */
        #progressbar {
            margin-bottom: 30px;
            overflow: hidden;
            color: #FF7043;
            padding-left: 0px;
            margin-top: 30px;
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

        /* Progress bar connectors */
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

        #progressbar li:first-child:after {
            left: 50%;
        }

        #progressbar li:last-child:after {
            width: 150%;
        }

        #progressbar li:nth-child(2):after {
            left: -50%;
            width: 200%;
        }

        #progressbar li:nth-child(3):after {
            left: -50%;
            width: 200%;
        }

        #progressbar li:nth-child(4):after {
            left: -50%;
            width: 200%;
        }

        #progressbar li.active:before, #progressbar li.active:after {
            background: #FF7043;
        }

        #progressbar li.active:before {
            content: "\f00c";
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

        @media screen and (max-width: 992px) {
            .top {
                padding-left: 5% !important;
                padding-right: 5% !important;
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
                padding-left: 3% !important;
                padding-right: 3% !important;
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

        @media screen and (max-width: 576px) {
            .top {
                padding-left: 1% !important;
                padding-right: 1% !important;
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
    <?php require_once('function/navindex.php'); ?>

    <div class="container px-1 px-md-4 py-5 mx-auto">
        <div class="card">
            <div class="row d-flex justify-content-between px-3 top">
                <div class="d-flex">
                    <h5>ORDER <span class="order font-weight-bold">#Y34XDHR</span></h5>
                </div>
                <div class="d-flex flex-column text-sm-right">
                    <p class="mb-0">Expected Arrival <span>01/12/19</span></p>

                </div>
            </div>
            <!-- Add class 'active' to progress -->
            <div class="row d-flex justify-content-center">
                <div class="col-10">
                    <ul id="progressbar" class="text-center">
                        <li class="active step0">
                            <img class="icon" src="https://i.imgur.com/9nnc9Et.png">
                            <div class="icon-content">
                                <p class="font-weight-bold">สินค้าเข้าระบบ</p>
                            </div>
                        </li>
                        <li class="active step0">
                            <img class="icon" src="https://i.imgur.com/u1AzR7w.png">
                            <div class="icon-content">
                                <p class="font-weight-bold">ขนส่งจากต้นทาง</p>
                            </div>
                        </li>
                        <li class="active step0">
                            <img class="icon" src="https://i.imgur.com/TkPm63y.png">
                            <div class="icon-content">
                                <p class="font-weight-bold">ถึงปลายทาง</p>
                            </div>
                        </li>
                        <li class="active step0">
                            <img class="icon" src="https://i.imgur.com/HdsziHP.png">
                            <div class="icon-content">
                                <p class="font-weight-bold">นำส่งลูกค้า</p>
                            </div>
                        </li>
                        <li class="step0">
                            <img class="icon" src="https://i.imgur.com/HdsziHP.png">
                            <div class="icon-content">
                                <p class="font-weight-bold">จัดส่งสำเร็จ</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <?php require_once('function/footer.php'); ?>
</body>
