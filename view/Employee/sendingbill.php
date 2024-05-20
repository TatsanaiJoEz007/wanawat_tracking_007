<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Shopping Cart</title>
   
</head>
<style>
</style>
<?php require_once('function/sidebar_employee.php');  ?>
<body>
    <div class="container mt-5">
        <h2>Available Items</h2>
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Item 1</h5>
                        <p class="card-text">Price: $10</p>
                        <button class="btn btn-primary add-to-cart" data-id="1">Add to Cart</button>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Item 2</h5>
                        <p class="card-text">Price: $15</p>
                        <button class="btn btn-primary add-to-cart" data-id="2">Add to Cart</button>
                    </div>
                </div>
            </div>
            <!-- Add more items here -->
        </div>
    </div>

    <script>
        $(document).ready(function () {
            $('.add-to-cart').click(function () {
                var item_id = $(this).data('id');
                var quantity = 1; // Default quantity is 1
                var bill_number = 'YOUR_BILL_NUMBER';

                // Send AJAX request to add item to cart
                $.ajax({
                    url: 'add_to_cart.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {item_id: item_id, quantity: quantity, bill_number: bill_number},
                    success: function (response) {
                        alert(response.message);
                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>
