<?php
// Start the session
session_start();

// Check if the session variable for the cart is set, if not, initialize it
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Example of adding an item to the cart
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {
    $item_id = $_POST['id'];
    $item_name = $_POST['name'];
    $item_price = $_POST['price'];
    $item_quantity = 1; // Default quantity

    $item = [
        'id' => $item_id,
        'name' => $item_name,
        'price' => $item_price,
        'quantity' => $item_quantity
    ];

    // Check if item already in cart, then update quantity
    $existingItemIndex = array_search($item_id, array_column($_SESSION['cart'], 'id'));
    if ($existingItemIndex !== false) {
        $_SESSION['cart'][$existingItemIndex]['quantity'] += 1;
    } else {
        $_SESSION['cart'][] = $item;
    }
}

// Function to get the total price of the cart
function getTotalPrice()
{
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
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
            background-color: #f0f2f5;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            font-family: Arial, sans-serif;
        }

        .container {
            margin: 20px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .scroll-wrapper {
            overflow-y: auto;
            max-height: 400px;
            /* Set a fixed height */
        }

        .card-container {
            display: flex;
            flex-wrap: nowrap;
            gap: 10px;
            padding: 20px;
        }





        .card {
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex: 0 0 250px;
            max-width: 250px;
            background-color: #fff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card-body {
            padding: 15px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .card-body .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            color: #007bff;
        }

        .card-body .card-text {
            margin-bottom: 0.5rem;
            flex-grow: 1;
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
            /* #0056b3 */
        }

        .cart-summary {
            margin: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .cart-summary h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        .cart-summary .form-group {
            margin-bottom: 15px;
        }

        .cart-summary .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }

        .cart-summary .form-group input[type="number"],
        .cart-summary .form-group input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .cart-summary .btn-success {
            background-color: #28a745;
            border: none;
            color: white;
            padding: 10px 15px;
            font-size: 1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .cart-summary .btn-success:hover {
            background-color: #218838;
        }

        footer {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px 0;
            margin-top: auto;
        }

        .remove-item {
            background-color: #dc3545;
            border: none;
            color: white;
            padding: 5px 10px;
            font-size: 0.9rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .remove-item:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>

    <?php require_once ('function/sidebar_employee.php'); ?>

    <div class="container mt-5">
        <h2>จำนวนบิล</h2>
        <div class="scroll-wrapper">
            <div class="card-container">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">ID 1</h5>
                        <p class="card-text">ID Product</p>
                        <p class="card-text">name product</p>
                        <p class="card-text">Price: $10</p>
                        <form method="POST">
                            <input type="hidden" name="id" value="1">
                            <input type="hidden" name="name" value="Item 1">
                            <input type="hidden" name="price" value="10">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">อัพเดตสถานะ</button>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">ID 2</h5>
                        <p class="card-text">ID Product</p>
                        <p class="card-text">name product</p>
                        <p class="card-text">Price: $15</p>
                        <form method="POST">
                            <input type="hidden" name="id" value="2">
                            <input type="hidden" name="name" value="Item 2">
                            <input type="hidden" name="price" value="15">
                            <button type="submit" name="add_to_cart" class="btn btn-primary">อัพเดตสถานะ</button>
                        </form>
                    </div>
                </div>
                <!-- Add more items here -->
            </div>
        </div>
    </div>

    <!-- <div class="cart-summary">
        <h2>Cart Summary</h2>
        <form id="cart-summary-form">
            <div id="cart-items">
                <?php foreach ($_SESSION['cart'] as $index => $item): ?>
                    <div class="form-group">
                        <label><?php echo htmlspecialchars($item['name']); ?> -
                            $<?php echo htmlspecialchars($item['price']); ?> each</label>
                        <input type="number" class="form-control quantity-input" data-index="<?php echo $index; ?>"
                            value="<?php echo htmlspecialchars($item['quantity']); ?>">
                        <button type="button" class="btn remove-item" data-index="<?php echo $index; ?>">Remove</button>
                        <input type="hidden" name="item[<?php echo $index; ?>][id]"
                            value="<?php echo htmlspecialchars($item['id']); ?>">
                        <input type="hidden" name="item[<?php echo $index; ?>][name]"
                            value="<?php echo htmlspecialchars($item['name']); ?>">
                        <input type="hidden" name="item[<?php echo $index; ?>][price]"
                            value="<?php echo htmlspecialchars($item['price']); ?>">
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="form-group">
                <label>Total Price:</label>
                <input type="text" class="form-control" id="total-price"
                    value="$<?php echo number_format(getTotalPrice(), 2); ?>" readonly>
            </div>
            <button type="submit" class="btn btn-success mt-3">Submit Order</button>

        </form>
    </div> -->

    <script>



    </script>
</body>

</html>