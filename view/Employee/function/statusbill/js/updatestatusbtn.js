document.addEventListener('DOMContentLoaded', function() {
    const updateStatusBtn = document.getElementById('updateStatusBtn');
    
    if (updateStatusBtn) {
        updateStatusBtn.addEventListener('click', function() {
            handleStatusUpdate();
        });
    }
});

function handleStatusUpdate() {
    const selectedItems = [];
    const checkboxes = document.querySelectorAll('input[name="select"]:checked');
    
    // Collect selected delivery IDs and their current status
    checkboxes.forEach((checkbox) => {
        selectedItems.push({
            id: checkbox.value,
            current_status: getCurrentStatus(checkbox),
            delivery_number: checkbox.dataset.deliveryNumber || getDeliveryNumber(checkbox)
        });
    });
    
    if (selectedItems.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'ไม่ได้เลือกรายการ',
            text: 'กรุณาเลือกการจัดส่งอย่างน้อยหนึ่งรายการ',
            confirmButtonColor: '#F0592E'
        });
        return;
    }
    
    // Show status selection modal
    showStatusSelectionModal(selectedItems);
}

function getCurrentStatus(checkbox) {
    const row = checkbox.closest('tr');
    const statusCell = row.querySelector('td:nth-child(6)'); // Status column
    const statusText = statusCell.textContent.trim();
    
    // Map status text to status number
    if (statusText.includes('คำสั่งซื้อเข้าสู่ระบบ')) return 1;
    if (statusText.includes('กำลังจัดส่งไปยังศูนย์') || statusText.includes('กำลังจัดส่งไปศูนย์')) return 2;
    if (statusText.includes('อยู่ที่ศูนย์กระจาย') || statusText.includes('ถึงศูนย์กระจาย')) return 3;
    if (statusText.includes('กำลังนำส่งให้ลูกค้า') || statusText.includes('กำลังส่งลูกค้า')) return 4;
    if (statusText.includes('ถึงนำส่งให้ลูกค้าสำเร็จ') || statusText.includes('ส่งสำเร็จ')) return 5;
    if (statusText.includes('เกิดปัญหา')) return 99;
    
    return 1; // Default
}

function getDeliveryNumber(checkbox) {
    const row = checkbox.closest('tr');
    const deliveryCell = row.querySelector('td:nth-child(4)'); // Delivery number column
    return deliveryCell ? deliveryCell.textContent.trim() : 'N/A';
}

function showStatusSelectionModal(selectedItems) {
    const statusOptions = [
        { value: 1, text: 'รับคำสั่งซื้อ', color: 'blue', description: 'สถานะสินค้าที่คำสั่งซื้อเข้าสู่ระบบ' },
        { value: 2, text: 'กำลังจัดส่งไปศูนย์', color: 'yellow', description: 'สถานะสินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า' },
        { value: 3, text: 'ถึงศูนย์กระจาย', color: 'grey', description: 'สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลายทาง' },
        { value: 4, text: 'กำลังส่งลูกค้า', color: 'purple', description: 'สถานะสินค้าที่กำลังนำส่งให้ลูกค้า' },
        { value: 5, text: 'ส่งสำเร็จ', color: 'green', description: 'สถานะสินค้าที่ถึงนำส่งให้ลูกค้าสำเร็จ' }
    ];
    
    let optionsHtml = '';
    statusOptions.forEach(option => {
        const colorClass = getColorClass(option.color);
        optionsHtml += `
            <div class="status-option" onclick="selectStatus(${option.value})" style="cursor: pointer; margin: 10px 0; padding: 15px; border: 2px solid transparent; border-radius: 10px; background: rgba(240, 89, 46, 0.05); transition: all 0.3s ease;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div class="status-circle ${option.color}" style="width: 24px; height: 24px; border-radius: 50%; ${colorClass}"></div>
                    <div>
                        <strong style="color: #2d3748; font-size: 1.1rem;">${option.text}</strong>
                        <div style="color: #718096; font-size: 0.9rem; margin-top: 4px;">${option.description}</div>
                    </div>
                </div>
            </div>
        `;
    });
    
    const selectedItemsText = selectedItems.map(item => `• ${item.delivery_number}`).join('<br>');
    
    Swal.fire({
        title: 'เลือกสถานะใหม่',
        html: `
            <div style="text-align: left; margin-bottom: 20px;">
                <strong>รายการที่เลือก (${selectedItems.length} รายการ):</strong><br>
                <div style="background: rgba(240, 89, 46, 0.1); padding: 10px; border-radius: 8px; margin-top: 8px; font-size: 0.9rem;">
                    ${selectedItemsText}
                </div>
            </div>
            <div style="text-align: left;">
                <strong>เลือกสถานะใหม่:</strong>
                <div id="statusOptions" style="margin-top: 10px;">
                    ${optionsHtml}
                </div>
            </div>
        `,
        showCancelButton: true,
        showConfirmButton: false,
        cancelButtonText: 'ยกเลิก',
        cancelButtonColor: '#6c757d',
        customClass: {
            popup: 'status-selection-modal'
        },
        width: '600px'
    });
    
    // Add click event listeners to status options
    document.querySelectorAll('.status-option').forEach(option => {
        option.addEventListener('mouseenter', function() {
            this.style.borderColor = '#F0592E';
            this.style.background = 'rgba(240, 89, 46, 0.1)';
            this.style.transform = 'translateY(-2px)';
        });
        
        option.addEventListener('mouseleave', function() {
            this.style.borderColor = 'transparent';
            this.style.background = 'rgba(240, 89, 46, 0.05)';
            this.style.transform = 'translateY(0)';
        });
    });
    
    // Store selected items for later use
    window.currentSelectedItems = selectedItems;
}

function getColorClass(color) {
    const colorMap = {
        'red': 'background: linear-gradient(135deg, #dc3545, #c82333);',
        'green': 'background: linear-gradient(135deg, #28a745, #1e7e34);',
        'blue': 'background: linear-gradient(135deg, #007bff, #0056b3);',
        'yellow': 'background: linear-gradient(135deg, #ffc107, #e0a800);',
        'grey': 'background: linear-gradient(135deg, #6c757d, #545b62);',
        'purple': 'background: linear-gradient(135deg, #6f42c1, #59339d);'
    };
    
    return colorMap[color] || colorMap['grey'];
}

function selectStatus(newStatus) {
    Swal.close();
    
    const selectedItems = window.currentSelectedItems;
    if (!selectedItems) {
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด',
            text: 'ไม่พบข้อมูลรายการที่เลือก',
            confirmButtonColor: '#F0592E'
        });
        return;
    }
    
    // Get status text
    const statusTexts = {
        1: 'รับคำสั่งซื้อ',
        2: 'กำลังจัดส่งไปศูนย์',
        3: 'ถึงศูนย์กระจาย',
        4: 'กำลังส่งลูกค้า',
        5: 'ส่งสำเร็จ'
    };
    
    const statusText = statusTexts[newStatus] || 'ไม่ทราบสถานะ';
    const selectedItemsText = selectedItems.map(item => `• ${item.delivery_number}`).join('<br>');
    
    // Confirm update
    Swal.fire({
        title: 'ยืนยันการอัปเดตสถานะ',
        html: `
            <div style="text-align: left;">
                <p><strong>รายการที่จะอัปเดต:</strong></p>
                <div style="background: rgba(240, 89, 46, 0.1); padding: 10px; border-radius: 8px; margin: 10px 0; font-size: 0.9rem;">
                    ${selectedItemsText}
                </div>
                <p><strong>สถานะใหม่:</strong> <span style="color: #F0592E;">${statusText}</span></p>
                <p style="color: #718096; font-size: 0.9rem; margin-top: 15px;">
                    <i class="bi bi-info-circle"></i> ระบบจะบันทึกเวลาที่อัปเดตสถานะโดยอัตโนมัติ
                </p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'ยืนยันการอัปเดต',
        cancelButtonText: 'ยกเลิก',
        confirmButtonColor: '#F0592E',
        cancelButtonColor: '#6c757d'
    }).then((result) => {
        if (result.isConfirmed) {
            executeStatusUpdate(selectedItems.map(item => item.id), newStatus);
        }
    });
}

function executeStatusUpdate(deliveryIds, newStatus) {
    // Show loading
    Swal.fire({
        title: 'กำลังอัปเดตสถานะ...',
        html: 'กรุณารอสักครู่',
        allowOutsideClick: false,
        allowEscapeKey: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send AJAX request
    fetch('function/statusbill/update_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            delivery_ids: deliveryIds,
            new_status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'อัปเดตสถานะสำเร็จ!',
                html: `
                    <div style="text-align: center;">
                        <p>${data.message}</p>
                        ${data.warnings && data.warnings.length > 0 ? 
                            `<div style="margin-top: 15px; padding: 10px; background: rgba(255, 193, 7, 0.1); border-radius: 8px; text-align: left;">
                                <strong style="color: #e0a800;">คำเตือน:</strong><br>
                                ${data.warnings.map(w => `• ${w}`).join('<br>')}
                            </div>` : ''
                        }
                    </div>
                `,
                confirmButtonColor: '#F0592E'
            }).then(() => {
                // Reload page to show updated data
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                html: `
                    <p>${data.message}</p>
                    ${data.errors && data.errors.length > 0 ? 
                        `<div style="margin-top: 15px; padding: 10px; background: rgba(220, 53, 69, 0.1); border-radius: 8px; text-align: left;">
                            <strong style="color: #dc3545;">รายละเอียดข้อผิดพลาด:</strong><br>
                            ${data.errors.map(e => `• ${e}`).join('<br>')}
                        </div>` : ''
                    }
                `,
                confirmButtonColor: '#F0592E'
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'เกิดข้อผิดพลาด!',
            text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
            confirmButtonColor: '#F0592E'
        });
    });
}