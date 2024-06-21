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
            background-color: #f4f4f4;
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
        .modal {
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

        .modal-content {
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
            <script>
                function toggleInstructions() {
                    var instructions = document.querySelector('.instruction-list');
                    instructions.style.display = instructions.style.display === 'none' ? 'block' : 'none';
                    var expandIcon = document.querySelector('.expand-icon');
                    expandIcon.textContent = expandIcon.textContent === '+' ? '-' : '+';
                }
            </script>
            <form method="GET" action="">
                <input class="insearch" type="text" name="search" placeholder="Search by delivery number" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn-custom">Search</button>
            </form>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Delivery ID & Number</th>
                        <th>Item Count</th>
                        <th>Status</th>
                        <th>วันที่สร้างบิล</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $search_term = isset($_GET['search']) ? $_GET['search'] : '';
                    $query = "SELECT d.delivery_id, d.delivery_number, d.delivery_date, COUNT(di.item_code) AS item_count, d.delivery_status FROM tb_delivery d INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id";
                    if ($search_term) $query .= " WHERE d.delivery_number LIKE '%" . mysqli_real_escape_string($conn, $search_term) . "%'";
                    $query .= " GROUP BY d.delivery_id";
                    $result = mysqli_query($conn, $query);
                    if (mysqli_num_rows($result) > 0) {
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

                            // Output the row in the table
                            echo '<tr class="' . $status_class . '">';
                            echo '<td>' . $row['delivery_id'] . ' - ' . $row['delivery_number'] . '</td>';
                            echo '<td>' . $row['item_count'] . '</td>';
                            echo '<td>' . $status_text . '</td>';
                            echo '<td>' . $row['delivery_date'] . '</td>';
                            echo '<td><button class="btn-custom" onclick="openModal(\'' . $status_text . '\', \'' . $row['delivery_id'] . '\', \'' . $row['delivery_number'] . '\')">Manage</button></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo "<tr><td colspan='4'>No delivery bills found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal section -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Status</h2>
            <h1 id="deliveryNumber" class="card-text"><b>Delivery Number : </b><span></span></h1> <br>
            <p><b>Current Status: </b><span id="currentStatus"></span></p>
            <hr> <br>
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
            modal.style.display = "block";
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
    </script>
</body>

</html>