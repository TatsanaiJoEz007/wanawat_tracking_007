<?php
session_start();

// db.php
$servername = "localhost";  // Usually 'localhost' if running on the same server
$username = "root";  // Replace with your database username
$password = "";  // Replace with your database password
$dbname = "wanawat_tracking";  // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parcel Sending System</title>
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <script src="https://cdn.lordicon.com/lordicon.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .container {
            margin: 20px auto;
            max-width: 1200px;
            padding: 0 20px;
            background-color: #ffff;
        }

        h2 {
            font-size: 2.5em;
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }

        .search-bar input {
            width: 80%;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .table-container {
            width: 100%;
            margin: 0 auto;

            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table,
        th,
        td {
            border: 1px solid #ccc;
        }

        th,
        td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #ff9967;
            color: white;
        }

        .search {
            background-color: #f0592e;
            color: white;
            margin-top: 20px;
            margin-left: 20px;
            margin-right: 20px;
            padding: 10px;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search:hover {
            background-color: #F1693E;
            cursor: pointer;
            transition: 0.3s ease-in-out;
        }

        .status-red {
            background-color: #ffcccc;
        }

        .status-green {
            background-color: #ccffcc;
        }

        .status-yellow {
            background-color: #ffffcc;
        }

        .status-blue {
            background-color: #cce5ff;
        }

        .status-purple {
            background-color: #dfe2fb;
        }

        .status-grey {
            background-color: #f0f2f5;
        }

        .btn-custom {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: background-color 0.3s;
            text-decoration: none;
        }

        .btn-custom:hover {
            background-color: #0056b3;
        }

        .btn-red {
            background-color: #dc3545;
        }

        .btn-red:hover {
            background-color: #c82333;
        }

        /* Modal Styles */
        .modal,
        .modal2 {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content,
        .modal-content2 {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 10px;
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        ::-webkit-scrollbar {
            width: 9px;
            /* Adjust width for vertical scrollbar */
        }

        ::-webkit-scrollbar-thumb {
            background-color: #FF5722;
            /* Color for scrollbar thumb */
            border-radius: 10px;
            /* Rounded corners for scrollbar thumb */
        }

        .home-section {
            max-height: 100vh;
            /* Adjust height as needed */
            overflow-y: auto;
            /* Allow vertical scroll */
            overflow-x: hidden;
            /* Prevent horizontal scroll */
            padding: 20px;
            background-color: #f9f9f9;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .instruction-box {
            background-color: #FFA84C;
            /* Light grey background */
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .instruction-box h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: #333;
        }

        .instruction-list {
            list-style-type: none;
            padding-left: 20px;
            margin: 0;
            color: #555;
            /* Text color */
            display: none;
        }

        .instruction-box.active .instruction-list {
            display: block;

        }

        .expand-icon {
            font-size: 24px;
            color: white;
            float: right;
            transition: transform 0.3s ease;
        }

        .active .expand-icon {
            transform: rotate(45deg);
        }

        .button-cute {
            background-color: #f0592e;
            border: 2px solid #f0600e;
            border-radius: 12px;
            padding: 10px 20px;
            cursor: pointer;
            transition: transform 0.3s ease-in-out;
        }

        .button-cute:hover {
            background-color: #f0500e;
            transform: translateY(-3px);
        }

        .button-cute a {
            text-decoration: none;
            color: #fff;
            font-size: 18px;
            transition: color 0.3s ease-in-out;
        }

        .button-cute:hover a {
            color: #ffdeeb;
        }

        .button-cute a::after {
            opacity: 0;
            transition: opacity 0.3s ease-in-out, transform 0.3s ease-in-out;
            transform: translateX(-5px);
        }

        .button-cute:hover a::after {
            opacity: 1;
            transform: translateX(0);
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            text-decoration: none;
            color: #333;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .collapsible {
            background-color: #f1f1f1;
            color: #444;
            cursor: pointer;
            padding: 18px;
            width: 100%;
            border: none;
            text-align: left;
            outline: none;
            font-size: 15px;
        }

        .active,
        .collapsible:hover {
            background-color: #ddd;
        }

        .content {
            padding: 0 18px;
            display: none;
            overflow: hidden;
            background-color: #f1f1f1;
            max-height: 0;
            transition: max-height 0.2s ease-out;
        }
    </style>

</head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>
    <div class="container">
        <button id="updateSelectedBtn" class="btn-custom" style="display:none;">อัพเดทสถานะที่เลือก</button>
        <!-- Search bar -->
        <div class="search-bar">
            <h2>สถานะการขนส่ง</h2>
            <div class="instruction-box" onclick="toggleInstructions()">
                <h2 style="color:black;">ความหมายของสีสถานะสินค้า <span class="expand-icon" style="color:black;">+</span></h2>
                <ol class="instruction-list" style="display:none;">
                    <li>
                        <b style="color: red;">สีแดง</b>
                        <i style="color:black;">: สถานะสินค้าที่เกิดปัญหา</i>
                    </li>
                    <li>
                        <b style="color: green;">สีเขียว</b>
                        <i style="color:black;">: สถานะสินค้าที่ถึงนำส่งให้ลูกค้าสำเร็จ</i>
                    </li>
                    <li>
                        <b style="color: blue;">สีน้ำเงิน</b>
                        <i style="color:black;">: สถานะสินค้าที่คำสั่งซื้อเข้าสู่ระบบ</i>
                    </li>
                    <li>
                        <b style="color: yellow;">สีเหลือง</b>
                        <i style="color:black;">: สถานะสินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า</i>
                    </li>
                    <li>
                        <b style="color: grey;">สีเทา</b>
                        <i style="color:black;">: สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลาย</i>
                    </li>
                    <li>
                        <b style="color: purple;">สีม่วง</b>
                        <i style="color:black;">: สถานะสินค้าที่กำลังนำส่งให้ลูกค้า</i>
                </ol>
            </div>
            <script>
                function toggleInstructions() {
                    var instructions = document.querySelector('.instruction-list');
                    instructions.style.display = instructions.style.display === 'none' ? 'block' : 'none';
                    var expandIcon = document.querySelector('.expand-icon');
                    expandIcon.textContent = expandIcon.textContent === '+' ? '-' : '+';
                }

                window.onscroll = function() {
                    myFunction();
                };

                var instructionsbox = document.querySelector('.instruction-box');
                var sticky = instructionsbox.offsetTop;

                function myFunction() {
                    if (window.pageYOffset >= sticky) {
                        instructionsbox.classList.add("sticky");
                    } else {
                        instructionsbox.classList.remove("sticky");
                    }
                }
            </script>

            <form method="GET" action="">
                <input class="insearch" type="text" name="search" placeholder="Search by delivery number" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="search">Search</button>
            </form>
        </div>

        <?php
        $search_term = isset($_GET['search']) ? $_GET['search'] : '';

        // Query to get total number of items
        $total_items_query = "SELECT COUNT(DISTINCT d.delivery_id) as total 
                                FROM tb_delivery d 
                                INNER JOIN tb_delivery_items di ON d.delivery_id  = di.delivery_id 
                                WHERE d.created_by = $user_id";

        // Append search term filter if provided
        if ($search_term) {
            $search_term_escaped = mysqli_real_escape_string($conn, $search_term);
            $total_items_query .= " AND d.delivery_number LIKE '%$search_term_escaped%'";
        }

        // Execute query to get total count
        $total_items_result = mysqli_query($conn, $total_items_query);

        if (!$total_items_result) {
            echo "Error fetching total items: " . mysqli_error($conn);
            exit;
        }

        $total_items = mysqli_fetch_assoc($total_items_result)['total'];

        $items_per_page = 20;
        $total_pages = ceil($total_items / $items_per_page);
        $current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

        $offset = ($current_page - 1) * $items_per_page;

        $query = "SELECT d.delivery_id, d.delivery_number, d.delivery_date, COUNT(di.item_code) AS item_count, d.delivery_status, di.transfer_type 
            FROM tb_delivery d 
            INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
            WHERE d.created_by = $user_id";

        // Append search term filter if provided
        if ($search_term) {
            $search_term_escaped = mysqli_real_escape_string($conn, $search_term);
            $query .= " AND d.delivery_number LIKE '%$search_term_escaped%'";
        }

        $query .= " GROUP BY d.delivery_id, di.transfer_type LIMIT $items_per_page OFFSET $offset";


        // Execute query to fetch data
        $result = mysqli_query($conn, $query);

        if (!$result) {
            echo "Error fetching data: " . mysqli_error($conn);
            exit;
        }
        ?>
        <div id="action-buttons" style="display: none;">
            <button class="btn-custom" onclick="handleSelectedItems(); openModal2('<?php echo $status_text ?>', '<?php echo $row['delivery_id'] ?>', '<?php echo $row['delivery_number'] ?>')"> Manage All </button>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', (event) => {
                    const checkboxes = document.querySelectorAll('input[name="select"]');
                    const actionButtons = document.getElementById('action-buttons');

                    checkboxes.forEach((checkbox) => {
                        checkbox.addEventListener('change', () => {
                            const anyChecked = Array.from(checkboxes).some(chk => chk.checked);
                            actionButtons.style.display = anyChecked ? 'block' : 'none';
                        });
                    });
                });

            function handleSelectedItems() {
                const selectedItems = [];
                const checkboxes = document.querySelectorAll('input[name="select"]:checked');

                checkboxes.forEach((checkbox) => {
                    selectedItems.push(checkbox.value);
                });

                // Perform your action with the selected items
                console.log(selectedItems);
                // Here you can perform AJAX requests or other actions with selectedItems
            }
        </script>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>เลือก</th>
                        <th>#</th>
                        <th>เลขบิล</th>
                        <th>จำนวน</th>
                        <th>สถานะ</th>
                        <th>วันที่สร้างบิล</th>
                        <th>ประเภทการขนย้าย</th>
                        <th>จัดการสถานะ</th>
                </thead>
                <tbody>
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
                                    $status_text = 'สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลาย';
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
                            echo '<td><center><input type="checkbox" name="select" value="' . $row['delivery_id'] . '"></center></td>';
                            echo '<td>' . $i . '</td>';
                            echo '<td>' . $row['delivery_number'] . '</td>';
                            echo '<td>' . $row['item_count'] . '</td>';
                            echo '<td>' . $status_text . '</td>';
                            echo '<td>' . $row['delivery_date'] . '</td>';
                            echo '<td>' . $row['transfer_type'] . '</td>';
                            echo '<td><button class="btn-custom" onclick="openModal(\'' . $status_text . '\', \'' . $row['delivery_id'] . '\', \'' . $row['delivery_number'] . '\')">Manage</button></td>';
                            echo '</tr>';
                
                            $i++;
                        }
                    } else {
                        echo "<tr><td colspan='6'>No delivery bills found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <script>
            function toggleNewButton(checkboxId, buttonId) {
                var checkbox = document.getElementById(checkboxId);
                var button = document.getElementById(buttonId);
                if (checkbox.checked) {
                    button.style.display = 'inline-block';
                } else {
                    button.style.display = 'none';
                }
            }

            function newButtonAction(deliveryId) {
                // Define the action for the new button here
                alert('New button clicked for delivery ID: ' + deliveryId);
            }
        </script>


        <div class="pagination">
            <?php if ($current_page > 1) : ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" class="btn-custom">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" class="btn-custom <?php echo ($i == $current_page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($current_page < $total_pages) : ?>
                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" class="btn-custom">Next &raquo;</a>
            <?php endif; ?>
        </div>

    </div>

    <!-- Modal section -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <h2>Update Status</h2>
            <h1 id="deliveryNumber" class="card-text"><b>Delivery Number : </b><span id="deliveryNumberText"></span></h1> <br>
            <p><b>Current Status: </b><span id="currentStatus"></span></p>
            <h3>รายละเอียดสินค้า</h3>
            <hr><br>
            <div id="itemDetails">
                <!-- Add container to show item details here -->
            </div>
            <button id="updateStatusBtn" class="btn-custom">อัพเดทสถานะการจัดส่งสินค้า</button>
            <button id="reportProblemBtn" class="btn-custom btn-red">แจ้งว่าสินค้ามีปัญหา</button>
        </div>
    </div>

    <!-- Include SweetAlert for modal notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

    <!-- JavaScript section for modal interaction -->
    <script>
        var modal = document.getElementById("myModal");
        var span = document.getElementsByClassName("close")[0];

        // Open modal and set current status text
        function openModal(statusText, deliveryId, deliveryNumber) {
            var currentStatus = document.getElementById("currentStatus");
            currentStatus.textContent = statusText;
            modal.dataset.deliveryId = deliveryId;
            document.getElementById("deliveryNumber").getElementsByTagName("span")[0].textContent = deliveryNumber;

            // Fetch data for the modal
            fetchModalData(deliveryId);
        }

        // Fetch data from the server
        function fetchModalData(deliveryId) {
            fetch('function/fetch_modal_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'deliveryId': deliveryId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }
                    // Assuming the data structure is data.items array
                    // Assuming the data structure is data.items array
                    if (data.items && data.items.length > 0) {
                        var itemDetailsContainer = document.getElementById('itemDetails');
                        itemDetailsContainer.innerHTML = ''; // Clear previous content

                        // Initialize a counter for bill numbers
                        var billNumber = 1;

                        data.items.forEach(item => {
                            var itemHTML = `
                                <div class="item-detail">
                                    <p><b># ${billNumber}</b></p>
                                    <p><b>เลขบิล:</b> ${item['TRIM(di.bill_number)']}</p>
                                    <p><b>ชื่อลูกค้า:</b> ${item['TRIM(di.bill_customer_name)']}</p>
                                    <p><b>รายละเอียดสินค้า:</b> ${item['TRIM(di.item_desc)']}</p>
                                    <p><b>ราคา:</b> ${item['TRIM(di.item_price)']}</p>
                                    <p><b>ราคารวม:</b> ${item['TRIM(di.line_total)']}</p>
                                    <br> <hr> <br>
                                </div>
                            `;
                            itemDetailsContainer.insertAdjacentHTML('beforeend', itemHTML);

                            // Increment the bill number for the next item
                            billNumber++;
                        });
                    }

                    modal.style.display = "block";
                })
                .catch(error => console.error('Error:', error));
        }

        // Close modal when clicking on <span> (x)
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Close modal when clicking outside modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Update status button click handler
        document.getElementById("updateStatusBtn").onclick = function() {
            var deliveryId = modal.dataset.deliveryId;

            // Ask for confirmation using SweetAlert
            Swal.fire({
                title: 'คุณแน่ใจไหม?',
                text: 'คุณแน่ใจหรือไม่ที่จะอัพเดทสถานะการขนส่งครั้งนี้ คุณจะไม่สามารถแก้ไขได้หากคุณได้ทำการอัพเดทไปแล้ว?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, อัพเดท',
                cancelButtonText: 'ไม่, ยกเลิก',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with updating status

                    // Example AJAX request for updating status
                    fetch('function/update_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                deliveryId: deliveryId
                            }),
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log("Response from server:", data);

                            // Handle response
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Delivery status updated successfully.',
                                });
                                location.reload(); // Reload the page after successful update
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message || 'Failed to update delivery status.',
                                });
                            }
                            modal.style.display = "none"; // Close modal after action
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to update delivery status.',
                            });
                            modal.style.display = "none"; // Close modal on error
                        });
                }
            });
        }


        document.getElementById("reportProblemBtn").onclick = function() {
            var deliveryId = modal.dataset.deliveryId;

            // Ask for confirmation using SweetAlert
            Swal.fire({
                title: 'คุณแน่ใจไหม?',
                text: 'คุณแน่ใจหรือไม่ที่จะแจ้งว่าการจัดส่งครั้งนี้มีปัญหา คุณจะไม่สามารถแก้ไขได้หากคุณได้ทำการแจ้งว่าการจัดส่งครั้งนี้มีปัญหา?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, แจ้งปัญหา',
                cancelButtonText: 'ไม่, ยกเลิก',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with reporting problem

                    // Example AJAX request for updating status
                    fetch('function/problem_status.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                deliveryId: deliveryId,
                                problem: 'Specify the problem here if needed'
                            }),
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log("Response from server:", data);

                            // Handle response
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Parcel problem reported successfully.',
                                });
                                location.reload();
                                // Optionally, you can reload the page or update UI here
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message || 'Failed to report parcel problem.',
                                });
                            }
                            modal.style.display = "none"; // Close modal after action
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to update status.',
                            });
                            modal.style.display = "none"; // Close modal on error
                        });
                }
            });
        }

        $(document).ready(function() {
            $("#myTable").DataTable();
        });
    </script>

    <!-- Modal section -->
    <div id="myModal2" class="modal2">
        <div class="modal-content2">
            <span class="close">&times;</span>
            <h2>Update Status</h2>
            <h1 id="deliveryNumber" class="card-text"><b>Delivery Number : </b><span id="deliveryNumberText"></span></h1>
            <p><b>Current Status: </b><span id="currentStatus"></span></p>
            <h3>รายละเอียดสินค้า</h3>
            <hr>
            <div id="itemDetails">
                <!-- Item details will be dynamically inserted here -->
            </div>
            <button id="updateStatusBtn" class="btn-custom">อัพเดทสถานะการจัดส่งสินค้า</button>
            <button id="reportProblemBtn" class="btn-custom btn-red">แจ้งว่าสินค้ามีปัญหา</button>
        </div>
    </div>


    <script>
        var modal2 = document.getElementById("myModal2");

        // Open modal and set current status text
        // Open modal and set current status text
        function openModal2(statusText, deliveryId, deliveryNumber) {
            var currentStatus = document.getElementById("currentStatus");
            currentStatus.textContent = statusText;
            modal2.dataset.deliveryId = deliveryId;
            document.getElementById("deliveryNumberText").textContent = deliveryNumber;

            // Fetch data for the modal
            fetchModalData2(deliveryId);
        }

        // Fetch data from the server
        function fetchModalData2(deliveryId) {
            fetch('function/fetch_modal_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        'deliveryId': deliveryId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.error(data.error);
                        return;
                    }
                    // Clear previous content
                    var itemDetailsContainer = document.getElementById('itemDetails');
                    itemDetailsContainer.innerHTML = '';

                    // Assuming the data structure is data.items array
                    if (data.items && data.items.length > 0) {
                        data.items.forEach((item, index) => {
                            var itemHTML = `
                    <div class="item-detail">
                        <button class="collapsible">Item ${index + 1}</button>
                        <div class="content">
                            <p><b>เลขบิล:</b> ${item['TRIM(di.bill_number)']}</p>
                            <p><b>ชื่อลูกค้า:</b> ${item['TRIM(di.bill_customer_name)']}</p>
                            <p><b>รายละเอียดสินค้า:</b> ${item['TRIM(di.item_desc)']}</p>
                            <p><b>ราคา:</b> ${item['TRIM(di.item_price)']}</p>
                            <p><b>ราคารวม:</b> ${item['TRIM(di.line_total)']}</p>
                        </div>
                    </div>
                `;
                            itemDetailsContainer.insertAdjacentHTML('beforeend', itemHTML);
                        });

                        // Add click event listeners to collapsible buttons
                        var coll = document.getElementsByClassName("collapsible");
                        for (var i = 0; i < coll.length; i++) {
                            coll[i].addEventListener("click", function() {
                                this.classList.toggle("active");
                                var content = this.nextElementSibling;
                                if (content.style.maxHeight) {
                                    content.style.maxHeight = null;
                                } else {
                                    content.style.maxHeight = content.scrollHeight + "px";
                                }
                            });
                        }
                    }

                    modal2.style.display = "block"; // Corrected modal display
                })
                .catch(error => console.error('Error:', error));
        }

        // Close modal when clicking on <span> (x)
        var span = document.getElementsByClassName("close")[0];
        span.onclick = function() {
            modal2.style.display = "none";
        }

        // Close modal when clicking outside modal
        window.onclick = function(event) {
            if (event.target == modal2) {
                modal2.style.display = "none";
            }
        }
    </script>

</body>

</html>