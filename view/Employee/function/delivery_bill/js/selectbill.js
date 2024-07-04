const checkboxes = document.querySelectorAll('.product-checkbox');
const cartItems = document.getElementById('cart-items');
const totalPriceElement = document.getElementById('total-price');
const createBillBtn = document.getElementById('create-bill-btn');
let itemCounter = 1;
const maxItems = 15;
let selectedItems = []; // Array to store selected items

checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        if (checkbox.checked && itemCounter > maxItems) {
            Swal.fire('เกิดข้อผิดพลาด!', 'เลือกสินค้าได้มากที่สุด 15 ชิ้นต่อการขนส่ง 1 ครั้ง', 'error');
            checkbox.checked = false; // Uncheck the box
            return;
        }

        const billnum = checkbox.getAttribute('data-bill-number');
        const billcus = checkbox.getAttribute('data-bill-customer');
        const billcusid = checkbox.getAttribute('data-bill-customer-id'); // Ensure this is retrieved
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
            li.textContent = `${itemCounter}. ${name} - ฿${total} - ${unit}`; // Add item number
            li.setAttribute('data-price', total);
            li.setAttribute('data-unit', unit);
            cartItems.appendChild(li);
            itemCounter++; // Increment the counter
            // Add the selected item to the array
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
                billcusid // Ensure this is included
            });
        } else {
            cartItems.querySelectorAll('.cart-item').forEach(item => {
                if (item.textContent.includes(name)) {
                    cartItems.removeChild(item);
                    itemCounter--; // Decrement when unchecked
                    // Re-number the remaining items:
                    cartItems.querySelectorAll('.cart-item').forEach((item, index) => {
                        item.textContent = `${index + 1}. ${item.textContent.substring(item.textContent.indexOf(".") + 2)}`;
                    });
                }
            });
            // Remove the unselected item from the array
            selectedItems = selectedItems.filter(item => item.name !== name);
        }

        calculateTotal();
    });
});

createBillBtn.addEventListener('click', () => {
    // Get the selected transfer type
    const transferType = document.querySelector('input[name="transfer_type"]:checked').value;

    // Prepare the summary message
    let summary = '<ul>';
    selectedItems.forEach(item => {
        summary += `<li>${item.name} - ฿${item.total} - ${item.unit}</li>`;
    });
    summary += '</ul>';
    console.log(selectedItems);

    // Show the SweetAlert with the summary
    Swal.fire({
        title: '<span style="color: red;">ยืนยันการสร้างบิล</span>',
        html: '<span style="color: red;">คุณจะไม่สามารถแก้ไขบิลได้อีกต่อไปหากกดยืนยันแล้ว</span> ระบบจะทำการสร้างบิลดังต่อไปนี้ :<br>' + summary,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'ยืนยัน',
        cancelButtonText: 'ยกเลิก',
    }).then((result) => {
        if (result.isConfirmed) {
            // Proceed with form submission
            const selectedItemsJSON = JSON.stringify(selectedItems);
            const form = document.createElement('form');
            form.setAttribute('method', 'POST');
            form.setAttribute('action', 'function/function_adddelivery.php');
            const hiddenField = document.createElement('input');
            hiddenField.setAttribute('type', 'hidden');
            hiddenField.setAttribute('name', 'selected_items');
            hiddenField.setAttribute('value', selectedItemsJSON);
            form.appendChild(hiddenField);

            // Add transfer type as a hidden input field
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