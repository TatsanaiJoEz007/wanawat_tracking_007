document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const cartItems = document.getElementById('cart-items');
    const totalPriceElement = document.getElementById('total-price');
    const createBillBtn = document.getElementById('create-bill-btn');
    let itemCounter = 1;
    const maxItems = 15;
    let selectedItems = [];

    // Function to show empty cart message
    const showEmptyCart = () => {
        cartItems.innerHTML = `
            <div class="empty-cart">
                <i class="bi bi-cart-x"></i>
                <h3>ตะกร้าว่าง</h3>
                <p>เลือกสินค้าจากตารางเพื่อเพิ่มลงในตะกร้า</p>
            </div>
        `;
    };

    // Function to calculate the total price of selected items
    const calculateTotal = () => {
        let totalPrice = 0;
        selectedItems.forEach(item => {
            totalPrice += parseFloat(item.total);
        });
        totalPriceElement.textContent = `฿${totalPrice.toFixed(2)}`;
        
        // Update button state
        createBillBtn.disabled = selectedItems.length === 0;
    };

    // Function to update cart display
    const updateCartDisplay = () => {
        if (selectedItems.length === 0) {
            showEmptyCart();
        } else {
            cartItems.innerHTML = '';
            selectedItems.forEach((item, index) => {
                const li = document.createElement('li');
                li.classList.add('cart-item');
                li.innerHTML = `
                    <div class="cart-item-name">${index + 1}. ${item.name}</div>
                    <div class="cart-item-details">
                        บิล: <strong>${item.billnum}</strong><br>
                        จำนวน: <strong>${item.quantity}</strong> ${item.unit}<br>
                        ราคา: <strong>฿${item.total}</strong>
                    </div>
                `;
                cartItems.appendChild(li);
            });
        }
        calculateTotal();
    };

    // Function to handle item selection and update the cart
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const itemCode = checkbox.getAttribute('data-item-code');
            const quantityDropdown = document.querySelector(`.quantity-dropdown[data-item-code="${itemCode}"]`);
            const selectedQuantity = parseFloat(quantityDropdown.value);
            
            // Get all data from checkbox attributes
            const billNumber = checkbox.getAttribute('data-bill-number');
            const billCustomer = checkbox.getAttribute('data-bill-customer');
            const billCustomerId = checkbox.getAttribute('data-bill-customer-id');
            const name = checkbox.getAttribute('data-name');
            const unit = checkbox.getAttribute('data-unit');
            const price = parseFloat(checkbox.getAttribute('data-price'));
            const itemSequence = checkbox.getAttribute('data-item-sequence');
            
            // Calculate new total based on selected quantity
            const total = (selectedQuantity * price).toFixed(2);

            if (checkbox.checked) {
                // Check if the maximum number of items has been reached
                if (selectedItems.length >= maxItems) {
                    Swal.fire('เกิดข้อผิดพลาด!', 'เลือกสินค้าได้มากที่สุด 15 ชิ้นต่อการขนส่ง 1 ครั้ง', 'error');
                    checkbox.checked = false;
                    return;
                }

                // Add the selected item to the selectedItems array with all required data
                selectedItems.push({
                    billnum: billNumber,
                    billcus: billCustomer,
                    billcusid: billCustomerId,
                    itemcode: itemCode,
                    name: name,
                    seq: itemSequence,
                    quantity: selectedQuantity,
                    unit: unit,
                    price: price.toFixed(2),
                    total: total
                });

                // Add visual feedback
                checkbox.closest('tr').style.backgroundColor = 'rgba(240, 89, 46, 0.15)';
                
            } else {
                // Remove the item from selectedItems array
                selectedItems = selectedItems.filter(item => 
                    !(item.itemcode === itemCode && item.billnum === billNumber && item.seq === itemSequence)
                );

                // Remove visual feedback
                checkbox.closest('tr').style.backgroundColor = '';
            }

            updateCartDisplay();
        });
    });

    // Handle quantity dropdown changes
    document.querySelectorAll('.quantity-dropdown').forEach(dropdown => {
        dropdown.addEventListener('change', () => {
            const itemCode = dropdown.getAttribute('data-item-code');
            const checkbox = document.querySelector(`.product-checkbox[data-item-code="${itemCode}"]`);
            
            if (checkbox && checkbox.checked) {
                // If item is selected, update the quantity and total
                const selectedQuantity = parseFloat(dropdown.value);
                const billNumber = checkbox.getAttribute('data-bill-number');
                const itemSequence = checkbox.getAttribute('data-item-sequence');
                const price = parseFloat(checkbox.getAttribute('data-price'));
                
                // Find and update the item in selectedItems array
                const itemIndex = selectedItems.findIndex(item => 
                    item.itemcode === itemCode && item.billnum === billNumber && item.seq === itemSequence
                );
                
                if (itemIndex !== -1) {
                    selectedItems[itemIndex].quantity = selectedQuantity;
                    selectedItems[itemIndex].total = (selectedQuantity * price).toFixed(2);
                    updateCartDisplay();
                }
            }
        });
    });

    // Handle create bill button click event
    createBillBtn.addEventListener('click', () => {
        if (selectedItems.length === 0) {
            Swal.fire('เกิดข้อผิดพลาด!', 'กรุณาเลือกสินค้าก่อนสร้างบิล', 'error');
            return;
        }

        const transferType = document.querySelector('input[name="transfer_type"]:checked').value;

        let summary = '<ul style="text-align: left;">';
        selectedItems.forEach(item => {
            summary += `<li>${item.name} - ฿${item.total} - ${item.quantity} ${item.unit}</li>`;
        });
        summary += '</ul>';

        // Display confirmation modal before proceeding
        Swal.fire({
            title: 'ยืนยันการสร้างบิล',
            html: `<div><strong>รายการสินค้า:</strong></div>${summary}<div><strong>ประเภทการขนส่ง:</strong> ${transferType}</div>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#F0592E',
            cancelButtonColor: '#6c757d'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                createBillBtn.innerHTML = '<i class="bi bi-arrow-clockwise" style="animation: spin 1s linear infinite;"></i> กำลังสร้างบิล...';
                createBillBtn.disabled = true;

                const selectedItemsJSON = JSON.stringify(selectedItems);

                // Send data to server via AJAX
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'function/function_adddelivery.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        // Reset button state
                        createBillBtn.innerHTML = '<i class="bi bi-receipt"></i> สร้างบิล';
                        createBillBtn.disabled = selectedItems.length === 0;

                        if (xhr.status === 200) {
                            const response = xhr.responseText.trim();
                            console.log('Server response:', response); // For debugging
                            
                            if (response === 'success') {
                                Swal.fire({
                                    title: 'สำเร็จ!',
                                    text: 'บิลได้ถูกสร้างเรียบร้อยแล้ว',
                                    icon: 'success',
                                    confirmButtonColor: '#F0592E'
                                }).then(() => {
                                    location.reload(); // Reload the page after successful submission
                                });
                            } else {
                                Swal.fire({
                                    title: 'เกิดข้อผิดพลาด!',
                                    text: `ไม่สามารถสร้างบิลได้: ${response}`,
                                    icon: 'error',
                                    confirmButtonColor: '#F0592E'
                                });
                            }
                        } else {
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด!',
                                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                                icon: 'error',
                                confirmButtonColor: '#F0592E'
                            });
                        }
                    }
                };
                xhr.send(`selected_items=${encodeURIComponent(selectedItemsJSON)}&transfer_type=${encodeURIComponent(transferType)}`);
            }
        });
    });

    // Initialize display
    showEmptyCart();
    calculateTotal();

    // Add CSS for spin animation
    if (!document.querySelector('style[data-spin]')) {
        const style = document.createElement('style');
        style.setAttribute('data-spin', 'true');
        style.textContent = `
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);
    }
});