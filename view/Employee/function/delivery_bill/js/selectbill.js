document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.product-checkbox');
    const cartItems = document.getElementById('cart-items');
    const totalPriceElement = document.getElementById('total-price');
    const createBillBtn = document.getElementById('create-bill-btn');
    let itemCounter = 1;
    const maxItems = 15;
    let selectedItems = [];

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            if (checkbox.checked && itemCounter > maxItems) {
                Swal.fire('เกิดข้อผิดพลาด!', 'เลือกสินค้าได้มากที่สุด 15 ชิ้นต่อการขนส่ง 1 ครั้ง', 'error');
                checkbox.checked = false;
                return;
            }

            const billnum = checkbox.getAttribute('data-bill-number').trim();
            const billcus = checkbox.getAttribute('data-bill-customer').trim();
            const billcusid = checkbox.getAttribute('data-bill-customer-id').trim();
            const itemcode = checkbox.getAttribute('data-item-code').trim();
            const name = checkbox.getAttribute('data-name').trim();
            const seq = checkbox.getAttribute('data-item_sequence').trim();
            const quantity = checkbox.getAttribute('data-quantity').trim();
            const unit = checkbox.getAttribute('data-unit').trim();
            const price = checkbox.getAttribute('data-price').trim();
            const total = checkbox.getAttribute('data-total').trim();
            const transferType = document.querySelector('input[name="transfer_type"]:checked').value;

            if (checkbox.checked) {
                const li = document.createElement('li');
                li.classList.add('cart-item');
                li.textContent = `${itemCounter}. ${name} - ฿${total} - ${unit}`;
                li.setAttribute('data-price', total);
                li.setAttribute('data-unit', unit);
                cartItems.appendChild(li);
                itemCounter++;
                selectedItems.push({
                    name,
                    price,
                    unit,
                    billnum,
                    itemcode,
                    quantity,
                    total,
                    billcus,
                    transferType,
                    billcusid,
                    seq
                });
            } else {
                cartItems.querySelectorAll('.cart-item').forEach(item => {
                    if (item.textContent.includes(name)) {
                        cartItems.removeChild(item);
                        itemCounter--;
                        cartItems.querySelectorAll('.cart-item').forEach((item, index) => {
                            item.textContent = `${index + 1}. ${item.textContent.substring(item.textContent.indexOf(".") + 2)}`;
                        });
                    }
                });
                selectedItems = selectedItems.filter(item => item.name !== name);
            }

            calculateTotal();
        });
    });

    createBillBtn.addEventListener('click', () => {
        const transferType = document.querySelector('input[name="transfer_type"]:checked').value;

        let summary = '<ul>';
        selectedItems.forEach(item => {
            summary += `<li>${item.name} - ฿${item.total} - ${item.unit}</li>`;
        });
        summary += '</ul>';
        console.log('Selected Items:', selectedItems);  // Log the selected items

        Swal.fire({
            title: '<span style="color: red;">ยืนยันการสร้างบิล</span>',
            html: '<span style="color: red;">คุณจะไม่สามารถแก้ไขบิลได้อีกต่อไปหากกดยืนยันแล้ว</span> ระบบจะทำการสร้างบิลดังต่อไปนี้ :<br>' + summary,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ยืนยัน',
            cancelButtonText: 'ยกเลิก',
        }).then((result) => {
            if (result.isConfirmed) {
                const selectedItemsJSON = JSON.stringify(selectedItems);

                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'function/function_adddelivery.php', true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        if (xhr.status === 200) {
                            console.log('Response:', xhr.responseText);  // Log the response from PHP
                            Swal.fire('สำเร็จ!', 'บิลได้ถูกสร้างเรียบร้อยแล้ว', 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            console.error('Error:', xhr.responseText);  // Log any errors
                            Swal.fire('เกิดข้อผิดพลาด!', 'ไม่สามารถสร้างบิลได้', 'error');
                        }
                    }
                };
                xhr.send(`selected_items=${encodeURIComponent(selectedItemsJSON)}&transfer_type=${encodeURIComponent(transferType)}`);
            }
        });
    });

    function calculateTotal() {
        const cartItems = document.querySelectorAll('#cart-items .cart-item');
        let totalPrice = 0;

        cartItems.forEach(item => {
            const price = parseFloat(item.getAttribute('data-price'));
            totalPrice += price;
        });
        totalPriceElement.textContent = `฿${totalPrice}`;
    }

    document.addEventListener('DOMContentLoaded', calculateTotal);
});