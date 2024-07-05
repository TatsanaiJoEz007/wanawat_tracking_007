function updateStatusToZero(billNumber) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', '../status_zero.php', true);
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

// Call this function when a checkbox is checked or unchecked
checkboxes.forEach(checkbox => {
    checkbox.addEventListener('change', () => {
        if (checkbox.checked) {
            const billNumber = checkbox.getAttribute('data-bill-number');
            updateStatusToZero(billNumber);
        }
    });
});