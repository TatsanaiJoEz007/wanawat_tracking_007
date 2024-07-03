        // ส่ง request ไปยังไฟล์ PHP ที่ทำการอัพเดท status ใน database
        function updateStatusToZero(billNumber) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'update_status.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        // ดำเนินการหลังจากอัพเดทเสร็จสมบูรณ์
                        console.log('Status updated to 0 for bill number ' + billNumber);
                    } else {
                        console.error('Failed to update status');
                    }
                }
            };
            xhr.send('bill_number=' + encodeURIComponent(billNumber));
        }