
<?php
// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูล CSV จาก HTTP request
$data = file_get_contents('php://input');

// แปลงข้อมูล CSV เป็น Array
$rows = explode("\n", $data);
foreach ($rows as $row) {
    $fields = str_getcsv($row);
    // ประมวลผลข้อมูลตามต้องการ เช่น เพิ่มลงในฐานข้อมูล
    // ตัวอย่างการเพิ่มลงในฐานข้อมูล
    $sql = "INSERT INTO table_name (column1, column2, column3)
            VALUES ('" . $fields[0] . "', '" . $fields[1] . "', '" . $fields[2] . "')";
    if ($conn->query($sql) === TRUE) {
        echo "Record inserted successfully";
    } else {
        echo "Error inserting record: " . $conn->error;
    }
}

// ปิดการเชื่อมต่อกับฐานข้อมูล
$conn->close();

?>