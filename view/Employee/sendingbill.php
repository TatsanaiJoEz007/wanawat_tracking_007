<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Shopping Cart</title>
   
</head>
<style>
    .container {
  max-width: 960px; /* Adjust for desired container width */
  margin: 0 auto; /* Center the container horizontally */
  padding: 2rem; /* Add some breathing room */
}

h2 {
  text-align: center; /* Center the "Available Items" heading */
}

.card {
  background-color: #fff;
  border: 1px solid #ddd;
  border-radius: 4px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Subtle shadow for depth */
  transition: all 0.2s ease-in-out; /* Smooth transition on hover */
  margin-bottom: 2rem; /* Add spacing between cards */
}

.card:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Enhance shadow on hover */
  transform: translateY(-2px); /* Slight lift on hover */
}

.card-img-top {
  width: 100%;
  height: 200px; /* Adjust image height as needed */
  object-fit: cover; /* Crop or contain image within container */
}

.add-to-cart {
  background-color: #007bff; /* Adjust button color */
  border-color: #007bff;
  color: #fff;
  text-decoration: none; /* Remove default underline */
  padding: 0.5rem 1rem;
  border-radius: 4px;
  transition: all 0.2s ease-in-out;
}

.add-to-cart:hover {
  background-color: #0069d9;
  border-color: #0069d9;
}

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
