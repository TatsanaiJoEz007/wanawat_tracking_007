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
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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
    </style>
</head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>
    <div class="container">
        <h2>สถานะบิล</h2>
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
                    <b style="color: blue; ">สีน้ำเงิน</b>
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

        <div class="search-bar">
            <form method="GET" action="">
                <input class="insearch" type="text" name="search" placeholder="Search by delivery number" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                <button type="submit" class="search">Search</button>
            </form>
        </div>
        <div class="card-container">
            <?php
            $search_term = isset($_GET['search']) ? $_GET['search'] : '';
            $query = "SELECT d.delivery_number, COUNT(di.item_code) AS item_count, d.delivery_status FROM tb_delivery d INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id";
            if ($search_term) $query .= " WHERE d.delivery_number LIKE '%" . mysqli_real_escape_string($conn, $search_term) . "%'";
            $query .= " GROUP BY d.delivery_number";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $status_text = '';
                    $card_class = '';
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
            ?>
                    <div class="card <?php echo $card_class; ?>">
                        <div class="card-body">
                            <h1 class="card-text">เลขที่ขนส่ง : <?php echo $row['delivery_number']; ?></h1>
                            <p class="card-text">จำนวนสินค้าในบิล : <?php echo $row['item_count']; ?></p>
                            <h3 class="card-text">สถานะ: <?php echo $status_text; ?></h3>
                            <a href="#" class="btn btn-primary view-details-btn" data-status="<?php echo $row['current_status']; ?>" data-delivery-id="<?php echo $row['delivery_id']; ?>" data-toggle="modal" data-target="#statusUpdateModal">View Details</a>


                            <!-- Button to report problem -->
                            <a href="#" class="btn btn-danger report-problem-btn" data-delivery-id="<?php echo $row['delivery_id']; ?>">แจ้งพัสดุเกิดปัญหา</a>
                        </div>
                    </div>

                    <!-- Include SweetAlert CSS and JS -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.min.css">
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>

                    <script>
                        $(document).ready(function() {
                            // Function to handle "Report Problem" button click
                            $('.report-problem-btn').on('click', function() {
                                var deliveryId = $(this).data('delivery-id'); // Assuming you have delivery_id stored somewhere

                                // Show SweetAlert confirmation dialog
                                Swal.fire({
                                    title: 'สินค้านี้มีปัญหาใช่หรือไม่?',
                                    text: "หากสินค้ามีปัญหา จะมีการส่งแจ้งเตือนไปที่ลูกค้า หากเป็นความผิดพลาดจะทำให้มีปัญหาได้?",
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonColor: '#d33',
                                    cancelButtonColor: '#3085d6',
                                    confirmButtonText: 'ใช่สินค้านี้มีปัญหาเกี่ยวกับการขนส่ง'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Perform AJAX request to report problem
                                        $.ajax({
                                            url: 'function/report_problem.php',
                                            method: 'POST',
                                            data: {
                                                deliveryId: deliveryId
                                            },
                                            success: function(response) {
                                                // Handle success
                                                console.log(response); // Log response for debugging
                                                Swal.fire('Success!', 'Parcel problem reported successfully.', 'success');
                                                // Optionally update UI here
                                            },
                                            error: function(xhr, status, error) {
                                                // Handle error
                                                console.error(xhr.responseText);
                                                Swal.fire('Error!', 'Failed to report parcel problem.', 'error');
                                            }
                                        });
                                    }
                                });
                            });
                        });
                    </script>

            <?php
                }
            } else {
                echo "<p>No delivery bills found.</p>";
            }
            ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="statusUpdateModal" tabindex="-1" role="dialog" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="statusUpdateModalLabel">Update Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Current Status: <span id="currentStatusText"></span></p>
                        <textarea id="statusUpdateTextarea" class="form-control" placeholder="Enter new status"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="updateStatusBtn">Update Status</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            $(document).ready(function() {
                $('.view-details-btn').on('click', function() {
                    var currentStatus = $(this).data('status');
                    var deliveryId = $(this).data('delivery-id'); // Assuming you have delivery_id stored somewhere
                    $('#currentStatusText').text(currentStatus);
                    $('#statusUpdateTextarea').val(''); // Populate this with current data if needed
                });

                $('#updateStatusBtn').on('click', function() {
                    var newStatus = $('#statusUpdateTextarea').val();
                    var deliveryId = $('.view-details-btn').data('delivery-id'); // Get delivery_id from the button

                    // AJAX request to update status
                    $.ajax({
                        url: 'function/update_status.php',
                        method: 'POST',
                        data: {
                            newStatus: newStatus,
                            deliveryId: deliveryId
                        },
                        success: function(response) {
                            console.log(response); // Log response for debugging
                            $('#statusUpdateModal').modal('hide');
                            // Optionally update UI here (remove this line in production)
                            // Reload the page or update specific elements to reflect status change
                            location.reload(); // Reload page to reflect changes
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr.responseText);
                            // Handle errors here
                        }
                    });
                });
            });
        </script>

    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</html>