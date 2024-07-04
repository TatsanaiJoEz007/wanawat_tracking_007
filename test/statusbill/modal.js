var modal = document.getElementById("myModal");
var span = document.getElementsByClassName("close")[0];

// Open modal and set current status text
function openModal(statusText, deliveryId, deliveryNumber) {
    var currentStatus = document.getElementById("currentStatus");
    currentStatus.textContent = statusText;
    modal.dataset.deliveryId = deliveryId;
    document.getElementById("deliveryNumber").getElementsByTagName("span")[0].textContent = deliveryNumber;

    // Fetch data for the modal
    fetchModalData(deliveryId);
}

// Fetch data from the server
function fetchModalData(deliveryId) {
    fetch('../../view/Employee/function/fetch_modal_data.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'deliveryId': deliveryId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }
            // Assuming the data structure is data.items array
            // Assuming the data structure is data.items array
            if (data.items && data.items.length > 0) {
                var itemDetailsContainer = document.getElementById('itemDetails');
                itemDetailsContainer.innerHTML = ''; // Clear previous content

                // Initialize a counter for bill numbers
                var billNumber = 1;

                data.items.forEach(item => {
                    var itemHTML = `
                        <div class="item-detail">
                            <p><b># ${billNumber}</b></p>
                            <p><b>เลขบิล:</b> ${item['TRIM(di.bill_number)']}</p>
                            <p><b>ชื่อลูกค้า:</b> ${item['TRIM(di.bill_customer_name)']}</p>
                            <p><b>รายละเอียดสินค้า:</b> ${item['TRIM(di.item_desc)']}</p>
                            <p><b>ราคา:</b> ${item['TRIM(di.item_price)']}</p>
                            <p><b>ราคารวม:</b> ${item['TRIM(di.line_total)']}</p>
                            <br> <hr> <br>
                        </div>
                    `;
                    itemDetailsContainer.insertAdjacentHTML('beforeend', itemHTML);

                    // Increment the bill number for the next item
                    billNumber++;
                });
            }

            modal.style.display = "block";
        })
        .catch(error => console.error('Error:', error));
}

// Close modal when clicking on <span> (x)
span.onclick = function() {
    modal.style.display = "none";
}

// Close modal when clicking outside modal
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}