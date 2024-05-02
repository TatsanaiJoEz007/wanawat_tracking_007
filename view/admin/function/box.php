<style>
        /* Add your custom styles here */
        body {
            padding-top: 20px;
            background-color: #f4f6f9;
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
        .small-box h3, .small-box p {
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
                    <h3>2,000,000</h3>
                    <p>จำนวนผู้ใช้งาน</p>
                </div>
                <div class="icon" style="background-color: #F2C93F;">
                    <i class="fas fa-users" style="color: #FFFFFF;"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>53<sup style="font-size: 20px">%</sup></h3>
                    <p>Bounce Rate</p>
                </div>
                <div class="icon" style="background-color: #E18B77;">
                    <i class="fas fa-chart-line" style="color: #FFFFFF;"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>44</h3>
                    <p>User Registrations</p>
                </div>
                <div class="icon" style="background-color: #05433E;">
                    <i class="fas fa-user-plus" style="color: #FFFFFF;"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>65</h3>
                    <p>Unique Visitors</p>
                </div>
                <div class="icon" style="background-color: #22D4BE;">
                    <i class="fas fa-chart-pie" style="color: #FFFFFF;"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
</div>