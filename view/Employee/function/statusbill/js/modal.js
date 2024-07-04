var modal = document.getElementById("myModal");
var span = document.getElementsByClassName("close")[0];

function openModal(data) {
    console.log('openModal called with data:', data);

    if (!data || !data.items) {
        console.error('Data or items is missing', data);
        return;
    }

    let modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = '';

    for (const [deliveryNumber, items] of Object.entries(data.items)) {
        let deliveryHTML = `
    <h3>Delivery Number: ${deliveryNumber}</h3>
    <hr>
`;

        items.forEach(function(item) {
            deliveryHTML += `
        <p>Bill Number: ${item.bill_number}</p>
        <p>Customer Name: ${item.bill_customer_name}</p>
        <p>Item Code: ${item.item_code}</p>
        <p>Item Description: ${item.item_desc}</p>
        <p>Quantity: ${item.item_quantity}</p>
        <p>Unit: ${item.item_unit}</p>
        <p>Price: ${item.item_price}</p>
        <p>Total: ${item.line_total}</p>
        <hr>
    `;
        });

        modalContent.innerHTML += deliveryHTML;
    }

    $('#manageModal').modal('show');
}

document.addEventListener('DOMContentLoaded', function() {
    const manageAllBtn = document.getElementById('manageAllBtn');
    if (!manageAllBtn) {
        console.error('Element with ID "manageAllBtn" not found');
        return;
    }

    manageAllBtn.addEventListener('click', handleSelectedItems);
});

function handleSelectedItems() {
    let selectedDeliveryIds = [];
    document.querySelectorAll('input[type="checkbox"]:checked').forEach(function(checkbox) {
        selectedDeliveryIds.push(checkbox.value);
    });

    if (selectedDeliveryIds.length === 0) {
        alert('Please select at least one delivery.');
        return;
    }

    console.log('Selected delivery IDs:', selectedDeliveryIds);

    $.ajax({
        url: '../../view/Employee/function/fetch_modal_data.php',
        type: 'POST',
        data: {
            deliveryIds: selectedDeliveryIds.join(',')
        },
        success: function(data) {
            console.log('Received data:', data);

            if (data.error) {
                alert(data.error);
                return;
            }

            if (!data.items) {
                alert('No data available');
                return;
            }

            openModal(data);
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            alert('Error fetching data');
        }
    });
}
