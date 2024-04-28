<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ระบบติดตามสถานะการส่ง</title>
<!-- ใส่ Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<style>
  /* เพิ่มสไตล์เพื่อแสดงเส้น timeline และสถานะ */
  .timeline {
    display: flex;
    list-style: none;
    padding: 0;
    text-align: center;
    margin: 20px 0;
  }
  .timeline li {
    flex: 1;
    position: relative;
  }
  .timeline li:not(:last-child):after {
    content: '';
    position: absolute;
    width: 100%;
    height: 3px;
    background-color: #d9534f;
    top: 15px; right: -50%;
    z-index: -1;
  }
  .timeline li.active {
    color: #d9534f;
  }
  .timeline li.active:after {
    background-color: #5cb85c;
  }
  /* เพิ่มสไตล์อื่นๆตามความจำเป็น */
</style>
</head>
<body>

<div class="container mt-3">
  <a href="#" class="btn btn-secondary mb-3">กลับ</a>
  <div class="d-flex justify-content-between">
    <h2>ผู้ส่ง</h2>
    <h2>ผู้รับสำเร็จ</h2>
  </div>

  <!-- Timeline -->
  <ul class="timeline">
    <li class="active">START</li>
    <li>2</li>
    <li>3</li>
    <li>4</li>
    <li>5</li>
  </ul>

  <!-- สถานะและข้อมูล -->
  <div class="row">
    <div class="col-md-6">
      <h4>สถานะ</h4>
      <!-- รายการสถานะ -->
      <div class="status-item">
        <p><strong>เริ่มต้น</strong></p>
        <p>เวลาที่เริ่มต้น</p>
      </div>
      <!-- สามารถเพิ่มรายการสถานะเพิ่มเติมได้ตามนี้ -->
    </div>
    <div class="col-md-6">
      <h4>ข้อมูล</h4>
      <!-- รายการข้อมูล -->
      <div class="info-item">
        <p><strong>ข้อมูลชั่วคราว</strong></p>
        <p>ที่อยู่ชั่วคราว</p>
      </div>
      <!-- สามารถเพิ่มรายการข้อมูลเพิ่มเติมได้ตามนี้ -->
    </div>
  </div>
</div>

<!-- ใส่ Bootstrap JS และ Popper.js -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
