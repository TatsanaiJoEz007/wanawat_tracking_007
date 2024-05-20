<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Shopping Cart</title>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .container {
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .card-container {
            display: flex;
            flex-wrap: nowrap;
            gap: 10px; /* ระยะห่างระหว่างการ์ด */
            overflow-x: auto; /* ทำให้สามารถเลื่อนซ้ายขวาได้ถ้ามีการ์ดเยอะ */
            padding: 20px;
        }
        .card {
            border: 1px solid #ccc;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            flex: 0 0 200px; /* ความกว้างของการ์ด */
            max-width: 200px;
            background-color: #fff;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .card-body .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }
        .card-body .card-text {
            margin-bottom: 0.5rem;
            flex-grow: 1;
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
        }
        .card-body .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<style>
</style>
<?php require_once('function/sidebar_employee.php');  ?>
<body>
    <?php require_once('function/sidebar_employee.php');  ?>
    <div class="container mt-5">
        <h2 >Available Items</h2>
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card">
                    <!-- Item  รอ import CSV  -->
                    <div class="card-body">
                        <h5 class="card-title">Item 1</h5>
                        <p class="card-text">ID Product</p>
                        <p class="card-text">name product</p>
                        <p class="card-text">Price: $10</p>
                        <button class="btn btn-primary add-to-cart" data-id="1" data-name="Item 1" data-price="10">Add to Cart</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                    <h5 class="card-title">Item 2</h5>
                        <p class="card-text">ID Product</p>
                        <p class="card-text">name product</p>
                        <p class="card-text">Price: $10</p>
                        <button class="btn btn-primary add-to-cart" data-id="2" data-name="Item 2" data-price="15">Add to Cart</button>
                    </div>
                </div>
            </div>
            <!-- Add more items here -->
        </div>
    </div>

    <div class="cart-summary">
        <h2>Cart Summary</h2>
        <form id="cart-summary">
            <div id="cart-items">
                <!-- Cart items will be appended here -->
            </div>
            <button type="submit" class="btn btn-success mt-3">Submit Order</button>
        </form>
    </div>

    
    <script>
        $(document).ready(function () {
            var cart = [];

            $('.add-to-cart').click(function () {
                var item_id = $(this).data('id');
                var item_name = $(this).data('name');
                var item_price = $(this).data('price');
                var quantity = 1; // Default quantity is 1

                var item = {
                    id: item_id,
                    name: item_name,
                    price: item_price,
                    quantity: quantity
                };

                cart.push(item);

                updateCartSummary();
            });

            function updateCartSummary() {
                var cartItemsContainer = $('#cart-items');
                cartItemsContainer.empty();

                cart.forEach(function (item, index) {
                    cartItemsContainer.append(`
                        <div class="form-group">
                            <label>${item.name}</label>
                            <input type="number" class="form-control" name="item[${index}][quantity]" value="${item.quantity}">
                            <input type="hidden" name="item[${index}][id]" value="${item.id}">
                            <input type="hidden" name="item[${index}][name]" value="${item.name}">
                            <input type="hidden" name="item[${index}][price]" value="${item.price}">
                        </div>
                    `);
                });
            }

            $('#cart-summary').submit(function (event) {
                event.preventDefault();

                // You can handle form submission here
                // For example, send the cart data to the server via AJAX
                console.log(cart);

                alert('Order submitted!');
            });
        });
    </script>
    
</body>
</html>
