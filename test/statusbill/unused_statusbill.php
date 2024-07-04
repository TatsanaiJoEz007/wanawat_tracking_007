    <!-- Modal section -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Update Status</h2>
            <h1 id="deliveryNumber" class="card-text"><b>Delivery Number : </b><span id="deliveryNumberText"></span></h1> <br>
            <p><b>Current Status: </b><span id="currentStatus"></span></p>
            <h3>รายละเอียดสินค้า</h3>
            <hr><br>
            <div id="itemDetails">

            </div>
            <button id="updateStatusBtn" class="btn-custom">อัพเดทสถานะการจัดส่งสินค้า</button>
            <button id="reportProblemBtn" class="btn-custom btn-red">แจ้งว่าสินค้ามีปัญหา</button>
        </div>
    </div>


    
    <!-- JavaScript section for modal interaction -->
    <script src="function/statusbill/js/modal.js"></script>

    <script src="function/statusbill/js/updatestatusbtn.js"></script>

    <script src="function/statusbill/js/reportstatusbtn.js"></script>