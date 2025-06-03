document.addEventListener('DOMContentLoaded', function() {
    const manageAllBtn = document.getElementById('manageAllBtn');
    
    if (manageAllBtn) {
        manageAllBtn.addEventListener('click', function() {
            handleSelectedItems();
        });
    }
});

function handleSelectedItems() {
    const selectedItems = [];
    const checkboxes = document.querySelectorAll('input[name="select"]:checked');

    checkboxes.forEach((checkbox) => {
        selectedItems.push(checkbox.value);
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

    // Show loading
    Swal.fire({
        title: 'กำลังโหลดข้อมูล...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Fetch data and show modal
    $.ajax({
        url: 'function/fetch_modal_data.php',
        type: 'POST',
        data: {
            deliveryIds: selectedItems.join(',')
        },
        success: function(data) {
            Swal.close();
            
            if (data.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: data.error,
                    confirmButtonColor: '#F0592E'
                });
                return;
            }

            if (!data.items) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไม่พบข้อมูล',
                    text: 'ไม่มีข้อมูลที่สามารถแสดงได้',
                    confirmButtonColor: '#F0592E'
                });
                return;
            }

            openModal(data);
            const modal = new bootstrap.Modal(document.getElementById('manageModal'));
            modal.show();
        },
        error: function(xhr, status, error) {
            Swal.close();
            console.error('Error:', error);
            console.error('XHR Response:', xhr.responseText);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด',
                text: 'ไม่สามารถดึงข้อมูลได้: ' + error,
                confirmButtonColor: '#F0592E'
            });
        }
    });
}

function openModal(data) {
    const modalContent = document.getElementById('modalContent');
    if (!modalContent) {
        console.error('Modal content element not found');
        return;
    }
    
    let content = '';
    
    if (data.items && data.items.length > 0) {
        content = `
            <div style="max-height: 600px; overflow-y: auto;">
                <h6 style="color: #F0592E; margin-bottom: 15px;">
                    <i class="bi bi-list-ul"></i> รายการที่เลือก (${data.items.length} รายการ)
                </h6>`;
        
        data.items.forEach((item, index) => {
            // Determine status text and color
            let statusText = 'ไม่ทราบสถานะ';
            let statusColor = '#6c757d';
            
            switch (parseInt(item.delivery_status)) {
                case 1:
                    statusText = 'รับคำสั่งซื้อ';
                    statusColor = '#007bff';
                    break;
                case 2:
                    statusText = 'กำลังจัดส่งไปศูนย์';
                    statusColor = '#ffc107';
                    break;
                case 3:
                    statusText = 'ถึงศูนย์กระจาย';
                    statusColor = '#6c757d';
                    break;
                case 4:
                    statusText = 'กำลังส่งลูกค้า';
                    statusColor = '#6f42c1';
                    break;
                case 5:
                    statusText = 'ส่งสำเร็จ';
                    statusColor = '#28a745';
                    break;
                case 99:
                    statusText = 'เกิดปัญหา';
                    statusColor = '#dc3545';
                    break;
            }

            // Generate timeline HTML
            const timelineHtml = generateTimelineHtml(item);
            
            // Generate items detail HTML
            let itemsHtml = '';
            if (item.items && item.items.length > 0) {
                itemsHtml = `
                    <div class="delivery-items" id="items-${item.delivery_id}" style="display: none; margin-top: 15px;">
                        <div style="background: rgba(248, 249, 250, 1); border-radius: 8px; padding: 15px; border: 1px solid #dee2e6;">
                            <h6 style="color: #495057; margin-bottom: 15px; font-size: 1rem;">
                                <i class="bi bi-box-seam"></i> รายละเอียดสินค้า (${item.items.length} รายการ)
                            </h6>`;
                
                item.items.forEach((deliveryItem, itemIndex) => {
                    itemsHtml += `
                        <div style="background: white; border-radius: 6px; padding: 12px; margin-bottom: 10px; border-left: 4px solid #F0592E; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 0.9rem;">
                                <div><strong style="color: #495057;">เลขบิล:</strong> ${deliveryItem.bill_number}</div>
                                <div><strong style="color: #495057;">ลูกค้า:</strong> ${deliveryItem.bill_customer_name}</div>
                                <div><strong style="color: #495057;">รหัสสินค้า:</strong> <code style="background: #e9ecef; padding: 2px 6px; border-radius: 4px;">${deliveryItem.item_code}</code></div>
                                <div><strong style="color: #495057;">จำนวน:</strong> <span style="color: #F0592E; font-weight: 600;">${deliveryItem.item_quantity} ${deliveryItem.item_unit}</span></div>
                            </div>
                            <div style="margin-top: 8px;">
                                <strong style="color: #495057;">รายละเอียด:</strong> 
                                <span style="color: #6c757d;">${deliveryItem.item_desc}</span>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 8px; margin-top: 8px; font-size: 0.85rem;">
                                <div><strong style="color: #495057;">ราคา:</strong> <span style="color: #28a745;">฿${parseFloat(deliveryItem.item_price).toLocaleString()}</span></div>
                                <div><strong style="color: #495057;">รวม:</strong> <span style="color: #F0592E; font-weight: 600;">฿${parseFloat(deliveryItem.line_total).toLocaleString()}</span></div>
                                <div><strong style="color: #495057;">น้ำหนัก:</strong> ${deliveryItem.item_weight} กก.</div>
                            </div>
                        </div>`;
                });
                
                itemsHtml += `
                        </div>
                    </div>`;
            }
            
            content += `
                <div style="background: white; border-radius: 12px; margin-bottom: 15px; border: 1px solid #dee2e6; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <div style="background: linear-gradient(135deg, rgba(240, 89, 46, 0.1), rgba(255, 138, 101, 0.1)); padding: 15px; border-bottom: 1px solid #dee2e6;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <h6 style="margin: 0; color: #2d3748; font-size: 1.1rem;">
                                <i class="bi bi-truck" style="color: #F0592E; margin-right: 8px;"></i>
                                <strong>${item.delivery_number}</strong>
                            </h6>
                            <span style="background: ${statusColor}; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                ${statusText}
                            </span>
                        </div>
                        
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; font-size: 0.9rem;">
                            <div>
                                <strong style="color: #495057;">จำนวนรายการ:</strong><br>
                                <span style="background: rgba(240, 89, 46, 0.1); color: #F0592E; padding: 2px 8px; border-radius: 8px; font-weight: 600;">${item.item_count} รายการ</span>
                            </div>
                            <div>
                                <strong style="color: #495057;">วันที่สร้าง:</strong><br>
                                <span style="color: #6c757d;">${formatDate(item.delivery_date)}</span>
                            </div>
                            <div>
                                <strong style="color: #495057;">ประเภทขนส่ง:</strong><br>
                                <span style="background: rgba(33, 150, 243, 0.1); color: #2196F3; padding: 2px 8px; border-radius: 6px; font-weight: 500;">${item.transfer_type}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Timeline Section -->
                    <div style="padding: 15px; background: rgba(249, 249, 249, 0.5);">
                        <h6 style="color: #495057; margin-bottom: 15px; font-size: 1rem;">
                            <i class="bi bi-clock-history"></i> Timeline การขนส่ง
                        </h6>
                        ${timelineHtml}
                    </div>
                    
                    <div style="padding: 12px 15px; border-top: 1px solid #dee2e6;">
                        <button 
                            type="button" 
                            class="btn btn-sm" 
                            onclick="toggleDeliveryItems(${item.delivery_id})"
                            style="background: linear-gradient(135deg, #F0592E, #FF8A65); color: white; border: none; border-radius: 6px; padding: 6px 12px; font-size: 0.85rem; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; gap: 6px;"
                            onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(240, 89, 46, 0.3)'"
                            onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none'"
                        >
                            <i class="bi bi-chevron-down" id="icon-${item.delivery_id}"></i>
                            <span id="text-${item.delivery_id}">ดูรายละเอียดสินค้า</span>
                        </button>
                    </div>
                    
                    ${itemsHtml}
                </div>`;
        });
        
        content += `
            </div>
            <div style="margin-top: 20px; padding: 15px; background: rgba(33, 150, 243, 0.1); border-radius: 8px; border-left: 4px solid #2196F3;">
                <h6 style="color: #2196F3; margin-bottom: 8px;">
                    <i class="bi bi-info-circle"></i> การดำเนินการ
                </h6>
                <p style="margin: 0; font-size: 0.9rem; color: #1976d2;">
                    เลือกการดำเนินการที่ต้องการด้านล่าง: อัปเดตสถานะการจัดส่ง หรือ รายงานปัญหาที่เกิดขึ้น
                </p>
            </div>
        `;
    } else {
        content = `
            <div style="text-align: center; padding: 40px 20px; color: #718096;">
                <i class="bi bi-inbox" style="font-size: 3rem; color: #adb5bd; margin-bottom: 15px; display: block;"></i>
                <h5 style="color: #2d3748; margin-bottom: 8px;">ไม่พบข้อมูล</h5>
                <p style="font-size: 0.9rem;">ไม่มีรายการการจัดส่งที่สามารถแสดงได้</p>
            </div>
        `;
    }
    
    modalContent.innerHTML = content;
}

// Function to generate timeline HTML
function generateTimelineHtml(item) {
    const steps = [
        {
            id: 1,
            title: 'รับคำสั่งซื้อ',
            description: 'ระบบรับคำสั่งซื้อเข้าสู่ระบบ',
            timestamp: item.delivery_step1_received,
            icon: 'bi-clipboard-check',
            color: '#007bff'
        },
        {
            id: 2,
            title: 'กำลังจัดส่งไปศูนย์',
            description: 'สินค้าอยู่ระหว่างการขนส่งไปยังศูนย์กระจาย',
            timestamp: item.delivery_step2_transit,
            icon: 'bi-truck',
            color: '#ffc107'
        },
        {
            id: 3,
            title: 'ถึงศูนย์กระจาย',
            description: 'สินค้าถึงศูนย์กระจายสินค้าปลายทาง',
            timestamp: item.delivery_step3_warehouse,
            icon: 'bi-building',
            color: '#6c757d'
        },
        {
            id: 4,
            title: 'กำลังส่งลูกค้า',
            description: 'สินค้าอยู่ระหว่างการนำส่งให้ลูกค้า',
            timestamp: item.delivery_step4_last_mile,
            icon: 'bi-geo-alt',
            color: '#6f42c1'
        },
        {
            id: 5,
            title: 'ส่งสำเร็จ',
            description: 'สินค้าถึงลูกค้าเรียบร้อยแล้ว',
            timestamp: item.delivery_step5_completed,
            icon: 'bi-check-circle',
            color: '#28a745'
        }
    ];

    let timelineHtml = '<div style="position: relative;">';
    
    steps.forEach((step, index) => {
        const isCompleted = step.timestamp && step.timestamp !== null;
        const isCurrent = parseInt(item.delivery_status) === step.id;
        const isProblem = parseInt(item.delivery_status) === 99;
        
        let stepStatus = '';
        let stepColor = '#e9ecef';
        let textColor = '#6c757d';
        let iconClass = 'bi-circle';
        
        if (isCompleted) {
            stepStatus = 'completed';
            stepColor = step.color;
            textColor = '#2d3748';
            iconClass = step.icon;
        } else if (isCurrent && !isProblem) {
            stepStatus = 'current';
            stepColor = step.color;
            textColor = '#2d3748';
            iconClass = step.icon;
        } else if (isProblem && isCompleted) {
            stepStatus = 'problem';
            stepColor = '#dc3545';
            textColor = '#721c24';
            iconClass = 'bi-exclamation-triangle';
        }
        
        timelineHtml += `
            <div style="display: flex; align-items: flex-start; margin-bottom: ${index === steps.length - 1 ? '0' : '20px'}; position: relative;">
                ${index < steps.length - 1 ? `
                    <div style="position: absolute; left: 19px; top: 40px; height: 20px; width: 2px; background: ${isCompleted ? stepColor : '#e9ecef'};"></div>
                ` : ''}
                
                <div style="width: 38px; height: 38px; border-radius: 50%; background: ${stepColor}; display: flex; align-items: center; justify-content: center; margin-right: 15px; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.15); position: relative; z-index: 1;">
                    <i class="${iconClass}" style="color: white; font-size: 16px;"></i>
                </div>
                
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                        <h6 style="margin: 0; color: ${textColor}; font-size: 0.95rem; font-weight: 600;">
                            ${step.title}
                        </h6>
                        ${isCompleted ? `
                            <span style="background: rgba(40, 167, 69, 0.1); color: #28a745; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                ${formatDate(step.timestamp)}
                            </span>
                        ` : isCurrent ? `
                            <span style="background: rgba(255, 193, 7, 0.1); color: #e0a800; padding: 2px 8px; border-radius: 12px; font-size: 0.8rem; font-weight: 500;">
                                กำลังดำเนินการ
                            </span>
                        ` : `
                            <span style="color: #adb5bd; font-size: 0.8rem; font-style: italic;">
                                รอดำเนินการ
                            </span>
                        `}
                    </div>
                    <p style="margin: 0; color: #6c757d; font-size: 0.85rem; line-height: 1.4;">
                        ${step.description}
                    </p>
                </div>
            </div>
        `;
    });
    
    timelineHtml += '</div>';
    return timelineHtml;
}

// Function to toggle delivery items visibility
function toggleDeliveryItems(deliveryId) {
    const itemsDiv = document.getElementById(`items-${deliveryId}`);
    const icon = document.getElementById(`icon-${deliveryId}`);
    const text = document.getElementById(`text-${deliveryId}`);
    
    if (itemsDiv.style.display === 'none') {
        // Show items
        itemsDiv.style.display = 'block';
        icon.className = 'bi bi-chevron-up';
        text.textContent = 'ซ่อนรายละเอียดสินค้า';
        
        // Add smooth animation
        itemsDiv.style.opacity = '0';
        itemsDiv.style.transform = 'translateY(-10px)';
        setTimeout(() => {
            itemsDiv.style.transition = 'all 0.3s ease';
            itemsDiv.style.opacity = '1';
            itemsDiv.style.transform = 'translateY(0)';
        }, 10);
    } else {
        // Hide items
        itemsDiv.style.transition = 'all 0.3s ease';
        itemsDiv.style.opacity = '0';
        itemsDiv.style.transform = 'translateY(-10px)';
        
        setTimeout(() => {
            itemsDiv.style.display = 'none';
            icon.className = 'bi bi-chevron-down';
            text.textContent = 'ดูรายละเอียดสินค้า';
        }, 300);
    }
}

function formatDate(dateString) {
    if (!dateString) return '-';
    
    const date = new Date(dateString);
    if (isNaN(date.getTime())) return '-';
    
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0');
    const year = date.getFullYear();
    const hours = date.getHours().toString().padStart(2, '0');
    const minutes = date.getMinutes().toString().padStart(2, '0');
    
    return `${day}/${month}/${year} ${hours}:${minutes}`;
}

// Additional modal helper functions
function closeModal() {
    const modal = document.getElementById('manageModal');
    if (modal) {
        const bsModal = bootstrap.Modal.getInstance(modal);
        if (bsModal) {
            bsModal.hide();
        }
    }
}

function showModalLoading() {
    const modalContent = document.getElementById('modalContent');
    if (modalContent) {
        modalContent.innerHTML = `
            <div style="text-align: center; padding: 40px 20px;">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem; color: #F0592E !important;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p style="margin-top: 15px; color: #718096;">กำลังโหลดข้อมูล...</p>
            </div>
        `;
    }
}

// Export functions for use in other scripts
window.openModal = openModal;
window.closeModal = closeModal;
window.showModalLoading = showModalLoading;
window.toggleDeliveryItems = toggleDeliveryItems;