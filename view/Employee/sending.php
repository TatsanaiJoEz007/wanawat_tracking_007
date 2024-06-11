<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee-Sending</title>
    <link rel="icon" type="image/x-icon"
        href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300&display=swap" rel="stylesheet">
</head>
<style>
    h1 {
        font-size: 36px;
        color: #333;
        text-align: center;
        margin-top: 50px;
    }

    .container {
        max-width: 1500px;
        margin: 30px auto;
    }

    h1 {
        color: #343a40;
        text-align: center;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background-color: #fff;
    }

    table th,
    table td {
        padding: 12px 15px;
        border-bottom: 1px solid #dee2e6;
        color: #343a40;
    }

    table th {
        background-color: #F0592E;
        color: #fff;
        text-align: left;
        text-transform: uppercase;
    }

    table tbody tr:hover {
        background-color: #f2f4f6;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
        padding: 8px 16px;
        font-size: 14px;
        border-radius: 4px;
        color: #fff;
        cursor: pointer;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
        border-radius: 10%;
        transition: 0.3s;

    }

    @media only screen and (max-width: 600px) {
        .container {
            margin: 15px auto;
        }

        table {
            font-size: 12px;
        }
    }

    ::-webkit-scrollbar {
    width: 9px; /* Adjust width for vertical scrollbar */
}

::-webkit-scrollbar-thumb {
    background-color: #FF5722; /* Color for scrollbar thumb */
    border-radius: 10px; /* Rounded corners for scrollbar thumb */
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
</style>
</head>

<body>
    <?php require_once ('function/sidebar_employee.php'); ?>
    <div class="container">
        <h1>Sending</h1>
        <br>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Order Date</th>
                    <th>Delivery Date</th>
                    <th>Delivery Time</th>
                    <th>Delivery Address</th>
                    <th>Delivery Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>John Doe</td>
                    <td>Product 1</td>
                    <td>2</td>
                    <td>200</td>
                    <td>2021-12-01</td>
                    <td>2021-12-05</td>
                    <td>10:00-12:00</td>
                    <td>123/4 ถ.สุขุมวิท แขวงคลองตัน เขตคลองเตย กรุงเทพมหานคร 10110</td>
                    <td>Preparing</td>
                    <td>
                        <button class="btn btn-success">Preparing</button>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>John Doe</td>
                    <td>Product 1</td>
                    <td>2</td>
                    <td>200</td>
                    <td>2021-12-01</td>
                    <td>2021-12-05</td>
                    <td>10:00-12:00</td>
                    <td>123/4 ถ.สุขุมวิท แขวงคลองตัน เขตคลองเตย กรุงเทพมหานคร 10110</td>
                    <td>Preparing</td>
                    <td>
                        <button class="btn btn-success">Preparing</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>


    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#sidebarCollapse').on('click', function () {
                $('#sidebar').toggleClass('active');
            });
        });

        function myFunction() {
            alert("Preparing");
        }


    </script>

</body>

</html>