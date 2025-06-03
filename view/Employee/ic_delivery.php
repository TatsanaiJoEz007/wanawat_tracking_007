<?php
require_once('../../view/config/connect.php');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    echo '<script>location.href="../../view/login"</script>';
    exit;
}

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการใบส่งสินค้า IC - Wanawat Tracking System</title>
    
    <!-- CSS Dependencies -->
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* Google Fonts Import Link */
        @import url('https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Kanit', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #F0592E 0%, #FF8A65 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Main Content */
        .home-section {
            position: relative;
            background: transparent;
            min-height: 100vh;
            left: 300px;
            width: calc(100% - 300px);
            transition: all 0.5s ease;
            padding: 12px;
            overflow-y: auto;
        }

        .sidebar.close ~ .home-section {
            left: 78px;
            width: calc(100% - 78px);
        }

        /* Header Content */
        .home-content {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .home-section .home-content .bx-menu,
        .home-section .home-content .text {
            color: #fff;
            font-size: 35px;
        }

        .home-section .home-content .bx-menu {
            cursor: pointer;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .home-section .home-content .bx-menu:hover {
            color: rgba(255, 255, 255, 0.8);
        }

        .home-section .home-content .text {
            font-size: 26px;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        /* Container */
        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        /* Content Container */
        .content-container {
            flex: 2;
            min-width: 300px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* Cart Container */
        .cart-container {
            flex: 1;
            min-width: 300px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            height: fit-content;
            position: sticky;
            top: 20px;
        }

        /* Back Button */
        .back-button {
            display: inline-flex;
            align-items: center;
            padding: 10px 18px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(240, 89, 46, 0.3);
            border-radius: 10px;
            color: #F0592E;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.2);
            font-size: 0.95rem;
        }

        .back-button:hover {
            background: rgba(240, 89, 46, 0.1);
            color: #D84315;
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.3);
        }

        .back-button i {
            margin-right: 8px;
            font-size: 1rem;
        }

        /* Page Title */
        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: #F0592E;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .page-title i {
            margin-right: 15px;
            color: #F0592E;
            font-size: 1.8rem;
        }

        /* Instruction Box */
        .instruction-box {
            background: rgba(240, 89, 46, 0.05);
            border: 1px solid rgba(240, 89, 46, 0.2);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.1);
        }

        .instruction-box h2 {
            font-size: 1.3rem;
            font-weight: 600;
            color: #F0592E;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .instruction-box ol {
            padding-left: 25px;
            margin: 0;
        }

        .instruction-box li {
            margin-bottom: 12px;
            font-size: 1rem;
            color: #2d3748;
            line-height: 1.5;
        }

        .instruction-box li b {
            color: #F0592E;
            font-weight: 600;
        }

        .instruction-box li i {
            font-style: normal;
            color: #dc3545;
            font-weight: 500;
        }

        /* Section Title */
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .section-title i {
            color: #F0592E;
        }

        /* Table Container */
        .product-table-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 20px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }

        table thead th {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            padding: 15px 10px;
            font-weight: 600;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            font-size: 0.9rem;
            position: relative;
        }

        table thead th:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background: rgba(255, 255, 255, 0.3);
        }

        table tbody td {
            padding: 12px 8px;
            vertical-align: middle;
            text-align: center;
            border-bottom: 1px solid rgba(240, 89, 46, 0.1);
            color: #2d3748;
            font-weight: 500;
            font-size: 0.85rem;
        }

        table tbody tr:nth-child(even) {
            background-color: rgba(240, 89, 46, 0.03);
        }

        table tbody tr:hover {
            background-color: rgba(240, 89, 46, 0.08);
            transition: background-color 0.3s ease;
        }

        /* Form Elements */
        .quantity-dropdown {
            padding: 8px 12px;
            border: 2px solid rgba(240, 89, 46, 0.3);
            border-radius: 8px;
            background: white;
            color: #2d3748;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 80px;
        }

        .quantity-dropdown:focus {
            outline: none;
            border-color: #F0592E;
            box-shadow: 0 0 0 3px rgba(240, 89, 46, 0.2);
        }

        /* Checkbox Styling */
        .product-checkbox {
            width: 18px;
            height: 18px;
            accent-color: #F0592E;
            cursor: pointer;
        }

        /* Cart Styling */
        .cart-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #F0592E;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .cart-items {
            list-style: none;
            padding: 0;
            margin-bottom: 20px;
            max-height: 400px;
            overflow-y: auto;
            min-height: 50px;
        }

        .cart-item {
            background: rgba(240, 89, 46, 0.05);
            border: 1px solid rgba(240, 89, 46, 0.2);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }

        .cart-item:hover {
            background: rgba(240, 89, 46, 0.1);
            transform: translateY(-1px);
        }

        .cart-item-name {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .cart-item-details {
            font-size: 0.9rem;
            color: #718096;
            line-height: 1.4;
        }

        .cart-total {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2d3748;
            margin: 20px 0;
            padding: 15px;
            background: rgba(240, 89, 46, 0.1);
            border-radius: 10px;
            text-align: center;
        }

        #total-price {
            color: #F0592E;
            font-size: 1.4rem;
        }

        /* Radio Buttons */
        .transfer-type-section {
            margin: 20px 0;
            padding: 15px;
            background: rgba(240, 89, 46, 0.05);
            border-radius: 10px;
            border: 1px solid rgba(240, 89, 46, 0.2);
        }

        .transfer-type-title {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .radio-group {
            display: flex;
            gap: 20px;
        }

        .radio-option {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .radio-option input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #F0592E;
        }

        .radio-option label {
            font-weight: 500;
            color: #2d3748;
            cursor: pointer;
        }

        /* Create Bill Button */
        .create-bill-btn {
            width: 100%;
            padding: 15px 20px;
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(240, 89, 46, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .create-bill-btn:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(240, 89, 46, 0.4);
        }

        .create-bill-btn:active {
            transform: translateY(0);
        }

        .create-bill-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        /* Empty State */
        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            color: #718096;
        }

        .empty-cart i {
            font-size: 3rem;
            color: #adb5bd;
            margin-bottom: 15px;
            display: block;
        }

        .empty-cart h3 {
            color: #2d3748;
            margin-bottom: 8px;
            font-size: 1.2rem;
        }

        .empty-cart p {
            font-size: 0.9rem;
            line-height: 1.4;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #F0592E, #FF8A65);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #D84315, #F0592E);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .home-section {
                left: 0;
                width: 100%;
                padding: 12px 8px;
            }

            .home-content .text {
                font-size: 20px;
            }

            .home-content .bx-menu {
                font-size: 28px;
            }

            .container {
                flex-direction: column;
                gap: 15px;
            }

            .content-container,
            .cart-container {
                padding: 20px;
                min-width: auto;
            }

            .cart-container {
                position: static;
                order: -1;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 1.3rem;
            }

            .product-table-container {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }

            table thead th,
            table tbody td {
                padding: 8px 6px;
                font-size: 0.8rem;
            }

            .instruction-box {
                padding: 20px;
            }

            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
        }

        @media (max-width: 480px) {
            .home-section {
                padding: 8px;
            }

            .home-content .text {
                font-size: 18px;
            }

            .home-content .bx-menu {
                font-size: 24px;
            }

            .content-container,
            .cart-container {
                padding: 15px;
            }

            .page-title {
                font-size: 1.3rem;
                flex-direction: column;
                text-align: center;
                gap: 8px;
            }

            .instruction-box {
                padding: 15px;
            }

            .instruction-box h2 {
                font-size: 1.1rem;
            }

            .instruction-box li {
                font-size: 0.9rem;
            }

            table {
                min-width: 900px;
            }
        }

        /* Animations */
        .animate__fadeInUp {
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate__fadeIn {
            animation: fadeIn 0.6s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Loading States */
        .loading {
            opacity: 0.6;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            margin: -10px 0 0 -10px;
            border: 2px solid transparent;
            border-top: 2px solid #F0592E;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>

    <section class="home-section">
        <!-- Header with menu button -->
        <div class="home-content">
            <i class='bx bx-menu'></i>
            <span class="text">จัดการใบส่งสินค้า IC</span>
        </div>

        <div class="container">
            <!-- Main Content -->
            <div class="content-container animate__fadeInUp">
                <a href="dashboard" class="back-button">
                    <i class="bi bi-arrow-left"></i> กลับไปหน้า Dashboard
                </a>
                
                <div class="page-title">
                    <i class="bi bi-receipt-cutoff"></i>
                    จัดการใบส่งสินค้า IC
                </div>

                <div class="instruction-box">
                    <h2>
                        <i class="bi bi-info-circle"></i>
                        วิธีการใช้งานระบบการเลือกบิล
                    </h2>
                    <ol>
                        <li>กด <b>เลือกจำนวนสินค้าที่ต้องการส่ง</b> ที่ Dropdown <i>(ต้องกดเลือกก่อน ถึงแม้จะเอาทั้งหมด)</i></li>
                        <li>กด <b>เลือกสินค้าที่ต้องการส่ง</b> ที่ Checkbox <i>(เลือกได้สูงสุด 15 รายการ)</i></li>
                        <li>เช็คความถูกต้องจาก <b>ตะกร้าสินค้า</b></li>
                        <li>กด <b>สร้างบิล</b> เพื่ออัปโหลดบิลการจัดส่งสินค้า</li>
                    </ol>
                </div>

                <div class="section-title">
                    <i class="bi bi-box-seam"></i>
                    สินค้ารอเลือกบิล
                </div>

                <div class="product-table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>เลขบิล</th>
                                <th>ชื่อลูกค้า</th>
                                <th>น้ำหนักบิล</th>
                                <th>รหัสสินค้า</th>
                                <th>รายละเอียด</th>
                                <th>จำนวน</th>
                                <th>หน่วย</th>
                                <th>ราคา</th>
                                <th>ราคารวม</th>
                                <th>เลือก</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Your SQL Query
                            $sql = "SELECT DISTINCT tb_header.bill_number, tb_header.bill_customer_name, tb_header.bill_customer_id, tb_header.bill_weight,
                                    tb_line.item_code, tb_line.item_desc, tb_line.item_quantity, 
                                    tb_line.item_unit, tb_line.item_price, tb_line.line_total, tb_line.item_sequence
                                    FROM tb_header
                                    INNER JOIN tb_line ON TRIM(tb_header.bill_number) = TRIM(tb_line.line_bill_number)
                                    WHERE tb_header.bill_status = 1 AND tb_line.line_status = 1 AND tb_line.line_bill_number LIKE '%ic%'";

                            $result = $conn->query($sql);

                            $merged_rows = [];

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $bill_number = $row["bill_number"];
                                    if (!isset($merged_rows[$bill_number])) {
                                        $merged_rows[$bill_number] = [
                                            "bill_number" => $bill_number,
                                            "bill_customer_name" => $row["bill_customer_name"],
                                            "bill_customer_id" => $row["bill_customer_id"],
                                            "bill_weight" => number_format((float)$row["bill_weight"] ?? 0, 2),
                                            "item_details" => []
                                        ];
                                    }

                                    $merged_rows[$bill_number]["item_details"][] = [
                                        "item_code" => $row["item_code"],
                                        "item_desc" => $row["item_desc"],
                                        "item_quantity" => (float)$row["item_quantity"],
                                        "item_unit" => $row["item_unit"],
                                        "item_price" => number_format((float)$row["item_price"], 2),
                                        "line_total" => number_format((float)$row["line_total"], 2),
                                        "item_sequence" => $row["item_sequence"]
                                    ];
                                }
                            }

                            // Output the data
                            if (empty($merged_rows)) {
                                echo "<tr>";
                                echo "<td colspan='10' style='text-align: center; padding: 40px; color: #718096;'>";
                                echo "<i class='bi bi-inbox' style='font-size: 2rem; display: block; margin-bottom: 10px; color: #adb5bd;'></i>";
                                echo "<strong>ไม่มีสินค้ารอเลือกบิล</strong><br>";
                                echo "<small>ยังไม่มีข้อมูลสินค้าที่พร้อมสำหรับการสร้างบิล IC</small>";
                                echo "</td>";
                                echo "</tr>";
                            } else {
                                foreach ($merged_rows as $row) {
                                    echo "<tr>";
                                    echo "<td rowspan='" . count($row["item_details"]) . "'><strong>" . $row["bill_number"] . "</strong></td>";
                                    echo "<td rowspan='" . count($row["item_details"]) . "'>" . $row["bill_customer_name"] . "</td>";
                                    echo "<td rowspan='" . count($row["item_details"]) . "'><span style='font-weight: 600; color: #F0592E;'>" . $row["bill_weight"] . "</span></td>";

                                    foreach ($row["item_details"] as $index => $item) {
                                        if ($index > 0) {
                                            echo "<tr>";
                                        }
                                        echo "<td><code>" . $item["item_code"] . "</code></td>";
                                        echo "<td style='text-align: left; padding-left: 12px;'>" . $item["item_desc"] . "</td>";

                                        echo "<td>";
                                        echo "<select class='quantity-dropdown' data-item-code='" . $item["item_code"] . "'>";
                                        $maxQuantity = (float)$item["item_quantity"];
                                        $step = 1;
                                        
                                        for ($i = $step; $i <= ceil($maxQuantity); $i++) {
                                            $displayValue = (fmod($i, 1) == 0) ? (int)$i : number_format($i, 2);
                                            $selected = (abs($i - $maxQuantity) < 0.01) ? "selected" : "";
                                            echo "<option value='" . number_format($i, 2) . "' $selected>" . $displayValue . "</option>";
                                        }
                                        echo "</select>";
                                        echo "</td>";

                                        echo "<td>" . $item["item_unit"] . "</td>";
                                        echo "<td><span style='color: #28a745; font-weight: 500;'>฿" . $item["item_price"] . "</span></td>";
                                        echo "<td><span style='color: #F0592E; font-weight: 600;'>฿" . $item["line_total"] . "</span></td>";
                                        
                                        echo "<td>";
                                        echo "<input type='checkbox' class='product-checkbox' 
                                            data-bill-number='" . $row["bill_number"] . "' 
                                            data-bill-weight='" . $row["bill_weight"] . "' 
                                            data-bill-customer='" . $row["bill_customer_name"] . "' 
                                            data-bill-customer-id='" . $row["bill_customer_id"] . "' 
                                            data-item-code='" . $item["item_code"] . "' 
                                            data-name='" . $item["item_desc"] . "' 
                                            data-quantity='" . $item["item_quantity"] . "' 
                                            data-unit='" . $item["item_unit"] . "' 
                                            data-price='" . $item["item_price"] . "' 
                                            data-total='" . $item["line_total"] . "' 
                                            data-item-sequence='" . $item["item_sequence"] . "'>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                }
                            }

                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Cart -->
            <div class="cart-container animate__fadeInUp">
                <h2 class="cart-title">
                    <i class="bi bi-cart3"></i>
                    ตะกร้าสินค้า
                </h2>
                
                <ul class="cart-items" id="cart-items">
                    <!-- Items will be added dynamically -->
                </ul>

                <div class="cart-total">
                    <i class="bi bi-calculator"></i>
                    ราคารวม: <span id="total-price">฿0.00</span>
                </div>

                <div class="transfer-type-section">
                    <div class="transfer-type-title">
                        <i class="bi bi-truck"></i>
                        ประเภทการขนส่ง
                    </div>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" name="transfer_type" value="Human" id="human" checked>
                            <label for="human">Human</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" name="transfer_type" value="Forklift" id="forklift">
                            <label for="forklift">Forklift</label>
                        </div>
                    </div>
                </div>

                <button class="create-bill-btn" id="create-bill-btn">
                    <i class="bi bi-receipt"></i>
                    สร้างบิล
                </button>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="function/delivery_bill/js/selectbill.js"></script>
</body>
</html>