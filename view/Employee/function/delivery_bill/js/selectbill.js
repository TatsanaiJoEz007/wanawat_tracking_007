document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const cartItems = document.getElementById('cart-items');
    const totalPriceElement = document.getElementById('total-price');
    const createBillBtn = document.getElementById('create-bill-btn');
    let itemCounter = 1;
    const maxItems = 15;
    let selectedItems = [];

    // Function to calculate the total price of selected items
    const calculateTotal = () => {
        let totalPrice = 0;
        const cartItemsList = document.querySelectorAll('#cart-items .cart-item');
        cartItemsList.forEach(item => {
            totalPrice += parseFloat(item.getAttribute('data-price'));
        });
        totalPriceElement.textContent = `฿${totalPrice.toFixed(2)}`; // Ensure 2 decimal places for total
    };

    // Function to handle item selection and update the cart
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const itemCode = checkbox.getAttribute('data-item-code');
            const quantityDropdown = document.querySelector(`.quantity-dropdown[data-item-code="${itemCode}"]`);
            const selectedQuantity = parseInt(quantityDropdown.value, 10);
            const name = checkbox.getAttribute('data-name');
            const price = parseFloat(checkbox.getAttribute('data-price'));
            const total = (selectedQuantity * price).toFixed(2);  // Ensure 2 decimal places for item total

            // Check if the maximum number of items has been reached
            if (checkbox.checked && itemCounter > maxItems) {
                Swal.fire('เกิดข้อผิดพลาด!', 'เลือกสินค้าได้มากที่สุด 15 ชิ้นต่อการขนส่ง 1 ครั้ง', 'error');
                checkbox.checked = false;
                return;
            }

            if (checkbox.checked) {
                // Add the selected item to the cart
                const li = document.createElement('li');
                li.classList.add('cart-item');
                li.textContent = `${itemCounter}. ${name} - ฿${total} - ${selectedQuantity} unit(s)`;
                li.setAttribute('data-price', total);  // Store the total price with 2 decimal places
                cartItems.appendChild(li);
                itemCounter++;
                
                // Add item to the selectedItems array
                selectedItems.push({
                    name,
                    price: price.toFixed(2),  // Price stored with 2 decimals
                    quantity: selectedQuantity,
                    total
                });
            } else {
                // Remove the item from the cart if unchecked
                cartItems.querySelectorAll('.cart-item').forEach(item => {
                    if (item.textContent.includes(name)) {
                        cartItems.removeChild(item);
                        itemCounter--;
                    }
                });
                // Remove from selectedItems array
                selectedItems = selectedItems.filter(item => item.name !== name);
            }

            calculateTotal();  // Recalculate total after item is added/removed
        });
    });

    // Handle create bill button click event
    createBillBtn.addEventListener('click', () => {
        const transferType = document.querySelector('input[name="transfer_type"]:checked').value;

        let summary = '<ul>';
        selectedItems.forEach(item => {
            summary += `<li>${item.name} - ฿${item.total} - ${item.quantity} unit(s)</li>`;
        });
        summary += '</ul>';

        // Display confirmation modal before proceeding
        Swal.fire({
            title: 'ยืนยันการสร้างบิล',
            html: summary,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก'
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedItemsJSON = JSON.stringify(selectedItems);

                // Send data to server via AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'function/function_adddelivery.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            Swal.fire('สำเร็จ!', 'บิลได้ถูกสร้างเรียบร้อยแล้ว', 'success').then(() => {
                                location.reload();  // Reload the page after successful submission
                            });
                        } else {
                            Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถสร้างบิลได้', 'error');
                        }
                    }
                };
                xhr.send(`selected_items=${encodeURIComponent(selectedItemsJSON)}&transfer_type=${encodeURIComponent(transferType)}`);
            }
        });
    });

    // Initialize total price on page load
    calculateTotal();
});