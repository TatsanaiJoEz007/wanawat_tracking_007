<?php
// Set pagination variables - เปลี่ยนจาก 10 เป็น 20
$records_per_page = 20;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$current_page = max(1, $current_page);
$offset = ($current_page - 1) * $records_per_page;

// Process search term
$search_term = '';
$search_condition = '';
$params = [];
$types = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = mysqli_real_escape_string($conn, $_GET['search']);
    // เพิ่มการค้นหาใน delivery_id ด้วย
    $search_condition = " AND (d.delivery_number LIKE ? OR d.delivery_id LIKE ?)";
    $params[] = "%$search_term%";
    $params[] = "%$search_term%";
    $types .= 'ss';
}

// Get total count for pagination
$count_sql = "SELECT COUNT(DISTINCT d.delivery_id) as total 
              FROM tb_delivery d 
              LEFT JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
              WHERE 1=1 $search_condition";

if (!empty($params)) {
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param($types, ...$params);
    $count_stmt->execute();
    $count_result = $count_stmt->get_result();
    $total_records = $count_result->fetch_assoc()['total'];
    $count_stmt->close();
} else {
    $count_result = $conn->query($count_sql);
    $total_records = $count_result->fetch_assoc()['total'];
}

$total_pages = ceil($total_records / $records_per_page);

// Main query with improved ordering and transfer_type handling
$sql = "SELECT 
            d.delivery_id,
            d.delivery_number,
            d.delivery_status,
            d.delivery_date,
            d.delivery_step1_received,
            d.delivery_step2_transit,
            d.delivery_step3_warehouse,
            d.delivery_step4_last_mile,
            d.delivery_step5_completed,
            COUNT(di.delivery_item_id) as item_count,
            COALESCE(GROUP_CONCAT(DISTINCT di.transfer_type SEPARATOR ', '), 'ทั่วไป') as transfer_type
        FROM tb_delivery d
        LEFT JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id
        WHERE 1=1 $search_condition
        GROUP BY d.delivery_id, d.delivery_number, d.delivery_status, d.delivery_date, 
                 d.delivery_step1_received, d.delivery_step2_transit, d.delivery_step3_warehouse,
                 d.delivery_step4_last_mile, d.delivery_step5_completed
        ORDER BY 
            CASE 
                WHEN d.delivery_status = 5 THEN 1  -- ส่งเสร็จแล้วจะอยู่ล่างสุด
                ELSE 0                             -- สถานะอื่นๆ จะอยู่บนสุด
            END ASC,
            d.delivery_date DESC,                  -- เรียงจากใหม่สุดไปเก่าสุด
            d.delivery_id DESC                     -- เรียงจาก ID ใหม่สุด (สำหรับกรณีวันเดียวกัน)
        LIMIT ? OFFSET ?";

// Add pagination parameters
$params[] = $records_per_page;
$params[] = $offset;
$types .= 'ii';

// Execute the query
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// If no results and we're not on page 1, redirect to page 1
if ($result->num_rows === 0 && $current_page > 1) {
    $redirect_url = '?';
    if (!empty($search_term)) {
        $redirect_url .= 'search=' . urlencode($search_term) . '&';
    }
    $redirect_url .= 'page=1';
    
    echo '<script>window.location.href = "' . $redirect_url . '";</script>';
    exit;
}

// Calculate pagination info
$start_record = $offset + 1;
$end_record = min($offset + $records_per_page, $total_records);

$stmt->close();
?>