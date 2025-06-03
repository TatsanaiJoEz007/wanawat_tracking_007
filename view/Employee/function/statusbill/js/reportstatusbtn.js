// ไฟล์: function/statusbill/js/reportstatusbtn.js
// แทนที่ไฟล์เดิมด้วยโค้ดนี้

document.addEventListener('DOMContentLoaded', function() {
    // ตัวแปรสำหรับเก็บข้อมูลที่เลือก
    let selectedDeliveries = [];
    let problemReportModal = null;

    // Event listener สำหรับปุ่มรายงานปัญหา
    const reportProblemBtn = document.getElementById('reportProblemBtn');
    if (reportProblemBtn) {
        reportProblemBtn.addEventListener('click', function() {
            openProblemReportModal();
        });
    }

    // ฟังก์ชันสร้าง Modal แบบ dynamic
    function createProblemReportModal() {
        // ตรวจสอบว่า Modal มีอยู่แล้วหรือไม่
        let existingModal = document.getElementById('problemReportModal');
        if (existingModal) {
            existingModal.remove();
        }

        // สร้าง Modal HTML
        const modalHtml = `
            <div class="modal fade" id="problemReportModal" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; border-radius: 15px 15px 0 0;">
                            <h5 class="modal-title">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                รายงานปัญหาการขนส่ง
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="filter: brightness(0) invert(1);"></button>
                        </div>
                        <div class="modal-body" style="padding: 25px;">
                            <div class="alert alert-warning d-flex align-items-center mb-3" style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); border-radius: 8px;">
                                <i class="bi bi-exclamation-triangle-fill me-2" style="color: #e0a800;"></i>
                                <div>
                                    <strong>คำเตือน:</strong> การรายงานปัญหาจะเปลี่ยนสถานะการขนส่งเป็น "เกิดปัญหา" และไม่สามารถย้อนกลับได้อัตโนมัติ
                                </div>
                            </div>
                            
                            <div id="selectedItemsList" class="mb-4">
                                <!-- รายการที่เลือกจะแสดงที่นี่ -->
                            </div>
                            
                            <div class="mb-3">
                                <label for="problemDescription" class="form-label">
                                    <strong>รายละเอียดปัญหา <span class="text-danger">*</span></strong>
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="problemDescription" 
                                    name="problemDescription" 
                                    rows="5" 
                                    placeholder="กรุณาระบุรายละเอียดปัญหาที่เกิดขึ้น เช่น สินค้าชำรุด, ที่อยู่ผิด, ไม่พบผู้รับ ฯลฯ"
                                    maxlength="1000"
                                    required
                                    style="border: 2px solid rgba(220, 53, 69, 0.3); border-radius: 8px; font-family: 'Kanit', sans-serif;"
                                ></textarea>
                                <div class="form-text">
                                    <span id="charCount" style="color: #6c757d;">0</span>/1000 ตัวอักษร
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">
                                    <strong>ประเภทปัญหา</strong>
                                </label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="problemType" id="damaged" value="สินค้าชำรุด">
                                            <label class="form-check-label" for="damaged">
                                                สินค้าชำรุด
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="problemType" id="wrongAddress" value="ที่อยู่ผิด">
                                            <label class="form-check-label" for="wrongAddress">
                                                ที่อยู่ผิด/ไม่ครบถ้วน
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="problemType" id="noReceiver" value="ไม่พบผู้รับ">
                                            <label class="form-check-label" for="noReceiver">
                                                ไม่พบผู้รับ
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="problemType" id="vehicleIssue" value="ปัญหายานพาหนะ">
                                            <label class="form-check-label" for="vehicleIssue">
                                                ปัญหายานพาหนะ
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="problemType" id="weatherIssue" value="สภาพอากาศ">
                                            <label class="form-check-label" for="weatherIssue">
                                                ปัญหาสภาพอากาศ
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="problemType" id="other" value="อื่นๆ">
                                            <label class="form-check-label" for="other">
                                                อื่นๆ
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer" style="border: none; padding: 20px 25px; gap: 10px;">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 8px;">
                                <i class="bi bi-x-circle"></i>
                                ยกเลิก
                            </button>
                            <button type="button" id="confirmProblemReport" class="btn" disabled style="
                                background: linear-gradient(135deg, #dc3545, #c82333);
                                color: white;
                                border: none;
                                border-radius: 8px;
                                padding: 10px 20px;
                                font-weight: 600;
                                opacity: 0.6;
                                transition: all 0.3s ease;
                            ">
                                <i class="bi bi-exclamation-triangle"></i>
                                ยืนยันรายงานปัญหา
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                #problemReportModal .form-control:focus {
                    border-color: #dc3545 !important;
                    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
                }
                
                #problemReportModal .form-check-input:checked {
                    background-color: #dc3545;
                    border-color: #dc3545;
                }
                
                #problemReportModal .form-check-input:focus {
                    border-color: #dc3545;
                    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
                }
                
                .selected-items-card {
                    background: rgba(220, 53, 69, 0.05);
                    border: 1px solid rgba(220, 53, 69, 0.2);
                    border-radius: 10px;
                    padding: 15px;
                    margin-bottom: 10px;
                }
                
                .selected-items-title {
                    color: #dc3545;
                    font-weight: 600;
                    margin-bottom: 10px;
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                .delivery-item-badge {
                    display: inline-block;
                    background: rgba(220, 53, 69, 0.1);
                    color: #dc3545;
                    padding: 4px 8px;
                    border-radius: 6px;
                    font-size: 0.9rem;
                    font-weight: 500;
                    margin: 2px;
                    border: 1px solid rgba(220, 53, 69, 0.3);
                }
            </style>
        `;

        // เพิ่ม Modal ลงใน body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // ตั้งค่า event listeners สำหรับ Modal ใหม่
        setupModalEventListeners();
    }

    // ฟังก์ชันตั้งค่า event listeners สำหรับ Modal
    function setupModalEventListeners() {
        // Event listener สำหรับปุ่มยืนยันรายงานปัญหา
        const confirmProblemReport = document.getElementById('confirmProblemReport');
        if (confirmProblemReport) {
            confirmProblemReport.addEventListener('click', function() {
                submitProblemReport();
            });
        }

        // Event listener สำหรับนับตัวอักษรใน textarea
        const problemDescription = document.getElementById('problemDescription');
        if (problemDescription) {
            problemDescription.addEventListener('input', function() {
                const charCount = document.getElementById('charCount');
                if (charCount) {
                    charCount.textContent = this.value.length;
                    
                    // เปลี่ยนสีตัวนับเมื่อใกล้ครบ
                    if (this.value.length > 900) {
                        charCount.style.color = '#dc3545';
                    } else if (this.value.length > 800) {
                        charCount.style.color = '#ffc107';
                    } else {
                        charCount.style.color = '#6c757d';
                    }
                }
                
                // เช็คความยาวขั้นต่ำสำหรับเปิดใช้งานปุ่ม
                validateForm();
            });
        }

        // Event listener สำหรับ radio buttons
        const problemTypeRadios = document.querySelectorAll('input[name="problemType"]');
        problemTypeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'อื่นๆ') {
                    // ถ้าเลือก "อื่นๆ" ให้โฟกัสที่ textarea
                    if (problemDescription) {
                        problemDescription.focus();
                    }
                }
                validateForm();
            });
        });
    }

    // ฟังก์ชันเปิด Modal รายงานปัญหา
    function openProblemReportModal() {
        // รับข้อมูลที่เลือก
        selectedDeliveries = getSelectedDeliveries();

        if (selectedDeliveries.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'ไม่ได้เลือกรายการ',
                text: 'กรุณาเลือกการจัดส่งอย่างน้อยหนึ่งรายการ',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // สร้าง Modal ใหม่
        createProblemReportModal();

        // รอให้ DOM อัปเดตแล้วค่อยแสดง Modal
        setTimeout(() => {
            // รีเซ็ต form
            resetProblemReportForm();

            // แสดงรายการที่เลือก
            displaySelectedItems();

            // เปิด Modal
            const modalElement = document.getElementById('problemReportModal');
            if (modalElement) {
                problemReportModal = new bootstrap.Modal(modalElement);
                problemReportModal.show();
                
                // โฟกัสที่ textarea หลังจาก modal เปิดแล้ว
                setTimeout(() => {
                    const textarea = document.getElementById('problemDescription');
                    if (textarea) {
                        textarea.focus();
                    }
                }, 500);
            }
        }, 100);
    }

    // ฟังก์ชันรับข้อมูลที่เลือก
    function getSelectedDeliveries() {
        const checkboxes = document.querySelectorAll('input[name="select"]:checked');
        const deliveries = [];

        checkboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            const deliveryNumberCell = row.querySelector('td:nth-child(4)');
            const statusCell = row.querySelector('td:nth-child(6)');
            
            deliveries.push({
                id: checkbox.value,
                number: deliveryNumberCell ? deliveryNumberCell.textContent.trim() : 'N/A',
                status: statusCell ? statusCell.textContent.trim() : 'N/A'
            });
        });

        return deliveries;
    }

    // ฟังก์ชันแสดงรายการที่เลือก
    function displaySelectedItems() {
        const container = document.getElementById('selectedItemsList');
        
        let html = `
            <div class="selected-items-card">
                <div class="selected-items-title">
                    <i class="bi bi-list-check"></i>
                    รายการที่เลือกสำหรับรายงานปัญหา (${selectedDeliveries.length} รายการ)
                </div>
                <div>
        `;

        selectedDeliveries.forEach(delivery => {
            html += `
                <span class="delivery-item-badge">
                    ${delivery.number} (${delivery.status})
                </span>
            `;
        });

        html += `
                </div>
            </div>
        `;

        if (container) {
            container.innerHTML = html;
        }
    }

    // ฟังก์ชันรีเซ็ต form
    function resetProblemReportForm() {
        const textarea = document.getElementById('problemDescription');
        if (textarea) {
            textarea.value = '';
        }
        
        // รีเซ็ตการนับตัวอักษร
        const charCount = document.getElementById('charCount');
        if (charCount) {
            charCount.textContent = '0';
            charCount.style.color = '#6c757d';
        }
        
        // รีเซ็ต radio buttons
        const radioButtons = document.querySelectorAll('input[name="problemType"]');
        radioButtons.forEach(radio => {
            radio.checked = false;
        });
        
        // ปิดใช้งานปุ่มยืนยัน
        const confirmBtn = document.getElementById('confirmProblemReport');
        if (confirmBtn) {
            confirmBtn.disabled = true;
            confirmBtn.style.opacity = '0.6';
        }
    }

    // ฟังก์ชันตรวจสอบความถูกต้องของ form
    function validateForm() {
        const textarea = document.getElementById('problemDescription');
        const confirmBtn = document.getElementById('confirmProblemReport');
        
        if (textarea && confirmBtn) {
            const isValid = textarea.value.trim().length >= 10;
            confirmBtn.disabled = !isValid;
            confirmBtn.style.opacity = isValid ? '1' : '0.6';
        }
    }

    // ฟังก์ชันส่งรายงานปัญหา
    function submitProblemReport() {
        const problemDesc = document.getElementById('problemDescription').value.trim();
        const selectedProblemType = document.querySelector('input[name="problemType"]:checked');
        
        // ตรวจสอบความถูกต้อง
        if (problemDesc.length < 10) {
            Swal.fire({
                icon: 'warning',
                title: 'รายละเอียดไม่เพียงพอ',
                text: 'กรุณาระบุรายละเอียดปัญหาอย่างน้อย 10 ตัวอักษร',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        if (problemDesc.length > 1000) {
            Swal.fire({
                icon: 'warning',
                title: 'รายละเอียดยาวเกินไป',
                text: 'รายละเอียดปัญหาต้องไม่เกิน 1000 ตัวอักษร',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // รับค่า problem type
        const problemType = selectedProblemType ? selectedProblemType.value : '';
        
        // ปิด modal ก่อน
        if (problemReportModal) {
            problemReportModal.hide();
        }

        // แสดงการยืนยันขั้นสุดท้าย
        showFinalConfirmation(problemDesc, problemType);
    }

    // ฟังก์ชันแสดงการยืนยันขั้นสุดท้าย
    function showFinalConfirmation(problemDesc, problemType) {
        const selectedItemsText = selectedDeliveries.map(item => `• ${item.number}`).join('<br>');
        
        let problemTypeText = '';
        if (problemType && problemType !== 'อื่นๆ') {
            problemTypeText = `<p><strong>ประเภทปัญหา:</strong> <span style="color: #dc3545;">${problemType}</span></p>`;
        }
        
        Swal.fire({
            title: 'ยืนยันการรายงานปัญหา',
            html: `
                <div style="text-align: left;">
                    <div style="background: rgba(220, 53, 69, 0.1); padding: 15px; border-radius: 8px; border-left: 4px solid #dc3545; margin-bottom: 15px;">
                        <h6 style="color: #dc3545; margin-bottom: 8px;">
                            <i class="bi bi-exclamation-triangle"></i> คำเตือน
                        </h6>
                        <p style="margin: 0; font-size: 0.9rem; color: #721c24;">
                            การรายงานปัญหาจะเปลี่ยนสถานะการขนส่งเป็น "เกิดปัญหา" และจะไม่สามารถเปลี่ยนกลับได้อัตโนมัติ
                        </p>
                    </div>
                    
                    <p><strong>รายการที่จะรายงานปัญหา (${selectedDeliveries.length} รายการ):</strong></p>
                    <div style="background: rgba(240, 89, 46, 0.1); padding: 10px; border-radius: 8px; margin: 10px 0; font-size: 0.9rem;">
                        ${selectedItemsText}
                    </div>
                    
                    ${problemTypeText}
                    
                    <p><strong>รายละเอียดปัญหา:</strong></p>
                    <div style="background: rgba(248, 249, 250, 1); padding: 12px; border-radius: 8px; margin: 10px 0; font-size: 0.9rem; border: 1px solid #dee2e6; max-height: 120px; overflow-y: auto;">
                        "${problemDesc}"
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="bi bi-exclamation-triangle"></i> ยืนยันรายงานปัญหา',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            width: '600px'
        }).then((result) => {
            if (result.isConfirmed) {
                executeProblemReport(problemDesc, problemType);
            }
        });
    }

    // ฟังก์ชันดำเนินการรายงานปัญหา
    function executeProblemReport(problemDesc, problemType) {
        // แสดง loading
        Swal.fire({
            title: 'กำลังรายงานปัญหา...',
            html: 'กรุณารอสักครู่ ระบบกำลังบันทึกข้อมูล',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // เตรียมข้อมูลสำหรับส่ง
        const payload = {
            delivery_ids: selectedDeliveries.map(item => parseInt(item.id)),
            problem_description: problemDesc,
            problem_type: problemType
        };

        // ส่ง request
        fetch('function/statusbill/report_problem.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(payload)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            Swal.close();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'รายงานปัญหาสำเร็จ!',
                    html: `
                        <div style="text-align: center;">
                            <p>${data.message}</p>
                            ${data.updated_deliveries && data.updated_deliveries.length > 0 ? 
                                `<div style="margin-top: 15px; padding: 10px; background: rgba(40, 167, 69, 0.1); border-radius: 8px;">
                                    <strong style="color: #28a745;">รายการที่อัปเดตแล้ว:</strong><br>
                                    ${data.updated_deliveries.map(d => `• ${d}`).join('<br>')}
                                </div>` : ''
                            }
                            ${data.warnings && data.warnings.length > 0 ? 
                                `<div style="margin-top: 15px; padding: 10px; background: rgba(255, 193, 7, 0.1); border-radius: 8px; text-align: left;">
                                    <strong style="color: #e0a800;">คำเตือน:</strong><br>
                                    ${data.warnings.map(w => `• ${w}`).join('<br>')}
                                </div>` : ''
                            }
                            <div style="margin-top: 15px; padding: 10px; background: rgba(33, 150, 243, 0.1); border-radius: 8px;">
                                <i class="bi bi-info-circle" style="color: #2196F3;"></i>
                                <small style="color: #1976d2;">ระบบได้บันทึกรายละเอียดปัญหาพร้อมประทับเวลาแล้ว</small>
                            </div>
                        </div>
                    `,
                    confirmButtonColor: '#28a745',
                    confirmButtonText: 'ตกลง'
                }).then(() => {
                    // รีโหลดหน้าเพื่อแสดงข้อมูลที่อัปเดต
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
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาดในการเชื่อมต่อ!',
                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้ กรุณาลองใหม่อีกครั้ง',
                confirmButtonColor: '#dc3545'
            });
        });
    }

    // Export functions สำหรับใช้ในไฟล์อื่น
    window.openProblemReportModal = openProblemReportModal;
    window.resetProblemReportForm = resetProblemReportForm;
});