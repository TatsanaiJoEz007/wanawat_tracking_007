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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parcel Sending System</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
        }

        .container {
            margin: 20px auto;
            max-width: 1200px;
            padding: 0 20px;
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

        .search{
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
            background-color: #ffcccc; /* Light red */
        }

        .card-green {
            background-color: #ccffcc; /* Light green */
        }

        .card-yellow {
            background-color: #ffffcc; /* Light yellow */
        }

        .card-blue {
            background-color: #cce5ff; /* Light blue */
        }

    </style>
</head>

<body>
    <?php require_once('function/sidebar_employee.php'); ?>
    <div class="container">
        <h2>สถานะบิล</h2>
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
                        case 1: $status_text = 'กำลังจัดเตรียม'; $card_class = 'card-blue'; break;
                        case 2: $status_text = 'กำลังจัดส่ง'; $card_class = 'card-yellow'; break;
                        case 3: $status_text = 'จัดส่งถึงปลายทาง'; $card_class = 'card-green'; break;
                        case 99: $status_text = 'เกิดปัญหา'; $card_class = 'card-red'; break;
                        default: $status_text = 'Unknown'; break;
                    }
            ?>
                    <div class="card <?php echo $card_class; ?>">
                        <div class="card-body">
                            <h1 class="card-text">เลขที่ขนส่ง : <?php echo $row['delivery_number']; ?></h1>
                            <p class="card-text">จำนวนสินค้าในบิล : <?php echo $row['item_count']; ?></p>
                            <p class="card-text">สถานะ: <?php echo $status_text; ?></p>
                            <a href="#" class="btn">View Details</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo "<p>No delivery bills found.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>