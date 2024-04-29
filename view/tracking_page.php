
<?php
require_once('function/navbar.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Timeline Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<style>
:root{
    --body-bg: #f4f4f4;
    --cont: #fff;
    --line: #333;
    --txt: #333;
    --light: #666;
}

*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;

}
body{
    width: 100%;
    min-height: 100vh;
    background: var(--body-bg);
    display: flex;
    justify-content: center;
    align-items: center;
    line-height: 1.4;
}
container{
    width: 1100px;
    max-width: 100%;
    margin: 0 30px;
    padding: 40px;
    background: var(--cont);
    position: relative;
}

.details{
    display: flex;
    gap: 1em;
    flex-wrap: wrap;
    justify-content: space-between;
}

.order h1{
    text-transform: uppercase;
}
.order span{
    color: var(--txt);
}
.date p{
    font-size: 1.1rem;
}
.track{
    margin-top: 4em 0 8em;
}
#progress{
    display: flex;
    justify-content: space-between;
    align-items: center;
    list-style: none;
    flex-wrap: wrap;
    gap: 1em;
    position: relative;
    text-align: center;
}
#progress li{
    width: 20%;
    position: relative;
}

#progress li:before{
    content: '\2713';
    position: absolute;
    display: flex;
    justify-content: center;
    width: 50px;
    align-items: center;
    font-size: 2rem;
    height: 50px;
    background: var(--line);
    color:#fff;
    border-radius: 50%;
    z-index: 11111;
}
#progress li:last-child::before{
    content: '\2713';
    font-weight: bold;
    background: var(--line);
}
#progress::before{
    content: '';
    position: absolute;
    top: 20px;
    width: 55%;
    margin-left: 35px;
    height: 10px;
    background: var(--line);
    z-index: 111;
}
#progress::before{
    content: '';
    position: absolute;
    top: 20px;
    width: 80%;
    margin-left: 35px;
    height: 10px;
    background: #c5cae9;
}
.lists{
    display: flex;
    gap: 2em;
    flex-wrap: wrap;
    align-items: center;
}
.list{
    display: flex;
    gap: 1em;
    flex: 1 1 50px;
    align-items: center;
}
.list p{
    font-size: 1.1rem;
}
.list img{
    width: 50px;
}
</style>

<body>
    <div class ="container">
        <div class = "details">
            <div class="order">
                <h1>order <span>qwpoiqw34 </span></h1>
            </div>
            <div class ="date">
                <p>Expected Delivery Date: <span> 2022-01-01</span></p>
                <p>USPS <b>1234567890</b></p>
            </div>

    <div class="track">
        <ul id="progress" class = "text-center">
            <li class = "active"></li>
            <li class = "active"></li>
            <li class = "active"></li>
            <li class = ""></li>
        </ul>
    </div>

    <div class ="lists">
        <div class ="list">
            <img src="" alt="">
            <p>Order Placed</p>
         </div>
         <div class ="list">
            <img src="" alt="">
            <p>Order Shippped</p>
         </div>
         <div class ="list">
            <img src="" alt="">
            <p>Order Enrooute</p>
         </div>
         <div class ="list">
            <img src="" alt="">
            <p>Order Arrived</p>
         </div>

    </div>
</body>
</html>

