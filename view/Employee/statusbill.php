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

<?php
// Your database connection code goes here

// Query to fetch delivery details along with items
$query = "SELECT d.delivery_number, COUNT(di.item_code) AS item_count
                FROM tb_delivery d
                INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id
                WHERE d.delivery_status = 1
                GROUP BY d.delivery_number";

$result = mysqli_query($conn, $query);

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
            max-width: 800px;
            padding: 0 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
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
            background-color: red;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <?php require_once('function/sidebar_employee.php'); ?>

    <div class="container">
        <h2>Delivery Bills</h2>
        <div class="card-container">
            <?php
            // Your database connection code goes here

            // Query to fetch delivery details along with items
            $query = "SELECT d.delivery_number, COUNT(di.item_code) AS item_count
                FROM tb_delivery d
                INNER JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id
                WHERE d.delivery_status = 1
                GROUP BY d.delivery_number";

            $result = mysqli_query($conn, $query);

            // Check if there are any results
            if (mysqli_num_rows($result) > 0) {
                // Loop through each row of results
                while ($row = mysqli_fetch_assoc($result)) {
            ?>
                    <div class="card">
                        <div class="card-body">
                            <h1 class="card-text">เลขที่ขนส่ง : <?php echo $row['delivery_number']; ?></h1>
                            <p class="card-text">จำนวนสินค้าในบิล : <?php echo $row['item_count']; ?></p>
                            <a href="#" class="btn">View Details</a> <!-- Add a link to view details of items -->
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
