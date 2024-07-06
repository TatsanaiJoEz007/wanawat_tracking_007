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

            const billnum = checkbox.getAttribute('data-bill-number');
            const billcus = checkbox.getAttribute('data-bill-customer');
            const billcusid = checkbox.getAttribute('data-bill-customer-id');
            const itemcode = checkbox.getAttribute('data-item-code');
            const name = checkbox.getAttribute('data-name');
            const quantity = checkbox.getAttribute('data-quantity');
            const unit = checkbox.getAttribute('data-unit');
            const price = checkbox.getAttribute('data-price');
            const total = checkbox.getAttribute('data-total');
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
                    billcusid
                });

                // Call the function to update status
                updateStatusToZero(billnum);
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
        console.log(selectedItems);

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
                const form = document.createElement('form');
                form.setAttribute('method', 'POST');
                form.setAttribute('action', 'function/function_adddelivery.php');
                const hiddenField = document.createElement('input');
                hiddenField.setAttribute('type', 'hidden');
                hiddenField.setAttribute('name', 'selected_items');
                hiddenField.setAttribute('value', selectedItemsJSON);
                form.appendChild(hiddenField);

                const transferTypeField = document.createElement('input');
                transferTypeField.setAttribute('type', 'hidden');
                transferTypeField.setAttribute('name', 'transfer_type');
                transferTypeField.setAttribute('value', transferType);
                form.appendChild(transferTypeField);

                document.body.appendChild(form);
                form.submit();
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

    function updateStatusToZero(billNumber) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', '../status_zero.php', true); // ปรับให้เป็น URL ที่ถูกต้อง
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    console.log('Status updated to 0 for bill number ' + billNumber);
                } else {
                    console.error('Failed to update status');
                }
            }
        };
        xhr.send('bill_number=' + encodeURIComponent(billNumber));
    }
});