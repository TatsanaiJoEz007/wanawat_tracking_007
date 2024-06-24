<?php
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

session_start();
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
            font-family: Arial, sans-serif;
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

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .card {
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 250px;
            background-color: #fff;
            overflow: hidden;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-body {
            padding: 15px;
        }

        .card-body .card-text {
            margin-bottom: 0.5rem;
            color: #555;
        }

        .card-body .btn {
            display: inline-block;
            padding: 10px 15px;
            font-size: 1rem;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .card-body .btn:hover {
            background-color: #005fad;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
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

        .card-red {
            background-color: #ffcccc;
            /* Light red */
        }

        .card-green {
            background-color: #ccffcc;
            /* Light green */
        }

        .card-yellow {
            background-color: #ffffcc;
            /* Light yellow */
        }

        .card-blue {
            background-color: #cce5ff;
            /* Light blue */
        }

        .card-purple {
            background-color: #dfe2fb;
            /* Light purple */
        }

        .card-grey {
            background-color: #f0f2f5;
            /* Light grey */
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

        .btn-custom {
            background-color: #007bff;
            /* Blue color from Bootstrap */
            color: #fff;
            /* White text */
            border: none;
            /* No border */
            padding: 0.5rem 1rem;
            /* Padding for better spacing */
            border-radius: 0.25rem;
            /* Rounded corners */
            cursor: pointer;
            /* Cursor style */
            transition: background-color 0.3s;
            /* Smooth transition */
        }

        .btn-custom:hover {
            background-color: #0056b3;
            /* Darker shade on hover */
        }

        .btn-red {
            background-color: #dc3545;
            /* Red color from Bootstrap */
        }

        .btn-red:hover {
            background-color: #c82333;
            /* Red color from Bootstrap */
        }

        /* Modal Styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1000;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            /* Could be more or less, depending on screen size */
            max-width: 600px;
            /* Limit maximum width */
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

        .content {
            padding: 16px;
        }

        .sticky {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 9999;
            /* Ensure it's above other elements */
        }

        .sticky+.content {
            padding-top: 60px;
            /* Adjust according to the height of your sticky element */
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
    </style>
</head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>
    <div class="container">
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
            <button class="button-cute search"><a href="statusbill_table.php">Table Style</a></button>
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
                <input class="search-bar" type="text" name="search" placeholder="Search by delivery number" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="search">Search</button>
            </form>
        </div>
        <div class="card-container">

            <?php
            $search_term = isset($_GET['search']) ? $_GET['search'] : '';

            // Query to get total number of items
            $total_items_query = "SELECT COUNT(DISTINCT d.delivery_id) as total 
                             FROM tb_delivery d 
                             INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
                             WHERE d.created_by = $user_id";

            // Append search term filter if provided
            if ($search_term) {
                $search_term_escaped = mysqli_real_escape_string($conn, $search_term);
                $total_items_query .= " AND d.delivery_number LIKE '%$search_term_escaped%'";
            }


            // Query to fetch actual data with pagination
            $query = "SELECT d.delivery_id, d.delivery_number, d.delivery_date, COUNT(di.item_code) AS item_count, d.delivery_status 
                  FROM tb_delivery d 
                  INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
                  WHERE d.created_by = $user_id";

            // Append search term filter if provided
            if ($search_term) {
                $search_term_escaped = mysqli_real_escape_string($conn, $search_term);
                $query .= " AND d.delivery_number LIKE '%$search_term_escaped%'";
            }


            // Execute query to fetch data
            $result = mysqli_query($conn, $query);

            if (!$result) {
                echo "Error fetching data: " . mysqli_error($conn);
                exit;
            }
            ?>
            <?php
            $search_term = isset($_GET['search']) ? $_GET['search'] : '';

            // Query to get total number of items
            $total_items_query = "SELECT COUNT(DISTINCT d.delivery_id) as total 
                             FROM tb_delivery d 
                             INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
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

            // Query to fetch actual data with pagination
            $query = "SELECT d.delivery_id, d.delivery_number, d.delivery_date, COUNT(di.item_code) AS item_count, d.delivery_status 
                  FROM tb_delivery d 
                  INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
                  WHERE d.created_by = $user_id";

            // Append search term filter if provided
            if ($search_term) {
                $search_term_escaped = mysqli_real_escape_string($conn, $search_term);
                $query .= " AND d.delivery_number LIKE '%$search_term_escaped%'";
            }

            $query .= " GROUP BY d.delivery_id LIMIT $items_per_page OFFSET $offset";

            // Execute query to fetch data
            $result = mysqli_query($conn, $query);

            if (!$result) {
                echo "Error fetching data: " . mysqli_error($conn);
                exit;
            }
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    switch ($row['delivery_status']) {
                        case 1:
                            $status_text = 'สถานะสินค้าที่คำสั่งซื้อเข้าสู่ระบบ';
                            $card_class = 'card-blue';
                            break;
                        case 2:
                            $status_text = 'สถานะสินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า';
                            $card_class = 'card-yellow';
                            break;
                        case 3:
                            $status_text = 'สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลาย';
                            $card_class = 'card-grey';
                            break;
                        case 4:
                            $status_text = 'สถานะสินค้าที่กำลังนำส่งให้ลูกค้า';
                            $card_class = 'card-purple';
                            break;
                        case 5:
                            $status_text = 'สถานะสินค้าที่ถึงนำส่งให้ลูกค้าสำเร็จ';
                            $card_class = 'card-green';
                            break;
                        case 99:
                            $status_text = 'สถานะสินค้าที่เกิดปัญหา';
                            $card_class = 'card-red';
                            break;
                        default:
                            $status_text = 'Unknown';
                            break;
                    }

                    // Output the card with data-delivery-id attribute
                    echo '<div class="card ' . $card_class . '" data-delivery-id="' . $row['delivery_id'] . '">';
                    echo '<div class="card-body">';
                    echo '<h1 class="card-text">เลขที่ขนส่ง : ' . $row['delivery_number'] . '</h1>';
                    echo '<p class="card-text">จำนวนสินค้าในบิล : ' . $row['item_count'] . '</p>';
                    echo '<h3 class="card-text">สถานะ: ' . $status_text . '</h3>';
                    // Conditionally render the button based on the class
                    echo '<button class="btn-custom" onclick="openModal(\'' . htmlspecialchars($status_text, ENT_QUOTES) . '\', \'' . htmlspecialchars($row['delivery_id'], ENT_QUOTES) . '\', \'' . htmlspecialchars($row['delivery_number'], ENT_QUOTES) . '\')">Manage</button>';

                    echo '</div></div>';
                }
            } else {
                echo "<p>No delivery bills found.</p>";
            }
            ?>
        </div>
    </div>


    <!-- Modal section -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <h2>Update Status</h2>
            <h1 id="deliveryNumber" class="card-text"><b>Delivery Number : </b><span></span></h1>
            <p><b>Current Status: </b><span id="currentStatus"></span></p>
            <hr>
            <div id="modalContent"></div>
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

            fetch('function/fetch_modal_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        deliveryId: deliveryId
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data); // Log the received data to inspect it
                    // Populate the modal with the fetched data
                    populateModal(data);
                })
                .catch(error => console.error('Error:', error));

            modal.style.display = "block";
        }

        // Function to populate the modal with the fetched data
        function populateModal(data) {
            // Get the modal content element
            var modalContent = document.getElementById("modalContent");

            // Clear existing content
            modalContent.innerHTML = '';

            // Check if data is valid and has items array
            if (!data || !Array.isArray(data.items) || data.items.length === 0) {
                var errorRow = document.createElement("p");
                errorRow.textContent = "No data available.";
                modalContent.appendChild(errorRow);
                return;
            }

            // Create a table to display the data
            var table = document.createElement("table");
            table.className = "modal-table";

            // Create table headers
            var headers = ["Item Code", "Item Description", "Quantity", "Unit", "Price", "Line Total"];
            var headerRow = document.createElement("tr");
            headers.forEach(function(header) {
                var th = document.createElement("th");
                th.textContent = header;
                headerRow.appendChild(th);
            });
            table.appendChild(headerRow);

            // Create table rows for each item
            data.items.forEach(function(item) {
                var row = document.createElement("tr");
                row.innerHTML = `
            <td>${item.item_code}</td>
            <td>${item.item_desc}</td>
            <td>${item.item_quantity}</td>
            <td>${item.item_unit}</td>
            <td>${item.item_price}</td>
            <td>${item.line_total}</td>
        `;
                table.appendChild(row);
            });

            // Add the table to the modal content
            modalContent.appendChild(table);
        }




        // Close modal when clicking outside modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        document.getElementById("updateStatusBtn").onclick = function() {
            var deliveryId = modal.dataset.deliveryId;

            // Show confirmation dialog
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: 'คุณแน่ใจไหมที่จะอัพเดทสถานะการจัดส่งสินค้า คุณจะไม่สามารถแก้ไขได้อีกหากกดอัพเดทไปแล้ว?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.isConfirmed) {
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
                                if (data.maxReached) {
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'คุณไม่สามารถเลือกสินค้าได้มากกว่า 15 รายการ',
                                        text: 'คุณสามารถเลือกสินค้าได้มากที่สุด 15 รายการต่อการขนส่ง 1 ครั้ง',
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'สำเร็จ!',
                                        text: 'ทำการแก้ไขสถานะเสร็จสิ้น!',
                                    });
                                    location.reload(); // Reload page after successful update
                                }
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'มีข้อผิดพลาด!',
                                    text: data.message || 'มีข้อผิดพลาดในการแก้ไขสถานะ!',
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
                } else {
                    // User clicked "No" or closed the dialog
                    Swal.fire({
                        icon: 'info',
                        title: 'ยกเลิก',
                        text: 'การอัพเดทสถานะถูกยกเลิก.',
                    });
                    modal.style.display = "none"; // Close modal without action
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
    </script>

</body>

</html>