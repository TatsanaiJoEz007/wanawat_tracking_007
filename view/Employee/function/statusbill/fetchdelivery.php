<?php
if (mysqli_num_rows($result) > 0) {
    $i = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        switch ($row['delivery_status']) {
            case 1:
                $status_text = 'สถานะสินค้าที่คำสั่งซื้อเข้าสู่ระบบ';
                $status_class = 'status-blue';
                break;
            case 2:
                $status_text = 'สถานะสินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า';
                $status_class = 'status-yellow';
                break;
            case 3:
                $status_text = 'สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลายทาง';
                $status_class = 'status-grey';
                break;
            case 4:
                $status_text = 'สถานะสินค้าที่กำลังนำส่งให้ลูกค้า';
                $status_class = 'status-purple';
                break;
            case 5:
                $status_text = 'สถานะสินค้าที่ถึงนำส่งให้ลูกค้าสำเร็จ';
                $status_class = 'status-green';
                break;
            case 99:
                $status_text = 'สถานะสินค้าที่เกิดปัญหา';
                $status_class = 'status-red';
                break;
            default:
                $status_text = 'Unknown';
                break;
        }

        echo '<tr class="' . $status_class . '">';
        echo '<td><center><input type="checkbox" name="select" value="' . $row['delivery_id'] . '" data-status-text="' . $status_text . '" data-delivery-number="' . $row['delivery_number'] . '"></center></td>';
        echo '<td>' . $i . '</td>';
        echo '<td>' . $row['delivery_number'] . '</td>';
        echo '<td><center>' . $row['item_count'] . '</center></td>';
        echo '<td>' . $status_text . '</td>';
        echo '<td>' . $row['delivery_date'] . '</td>';
        echo '<td>' . $row['transfer_type'] . '</td>';
        // echo '<td><button class="btn-custom" onclick="openModal(\'' . $status_text . '\', \'' . $row['delivery_id'] . '\', \'' . $row['delivery_number'] . '\')">Manage</button></td>';
        echo '</tr>';

        $i++;
    }
} else {
    echo "<tr><td colspan='6'>No delivery bills found.</td></tr>";
}
