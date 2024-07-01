<div class="instruction-box" onclick="toggleInstructions()">
    <h2 style="color:black;">คำแนะนำในการใช้งานระบบ <span class="expand-icon" style="color:black;">+</span></h2>
    <ol class="instruction-list" style="display:none;">
    <h4>ความหมายของสีสถานะสินค้า</h4>
        <li>
            <b style="color: red;">สีแดง</b>
            <i style="color:black;">: สถานะสินค้าที่เกิดปัญหา</i>
        </li>
        <li>
            <b style="color: green;">สีเขียว</b>
            <i style="color:black;">: สถานะสินค้าที่ถึงนำส่งให้ลูกค้าสำเร็จ</i>
        </li>
        <li>
            <b style="color: blue;">สีน้ำเงิน</b>
            <i style="color:black;">: สถานะสินค้าที่คำสั่งซื้อเข้าสู่ระบบ</i>
        </li>
        <li>
            <b style="color: yellow;">สีเหลือง</b>
            <i style="color:black;">: สถานะสินค้าที่กำลังจัดส่งไปยังศูนย์กระจายสินค้า</i>
        </li>
        <li>
            <b style="color: grey;">สีเทา</b>
            <i style="color:black;">: สถานะสินค้าอยู่ที่ศูนย์กระจายสินค้าปลาย</i>
        </li>
        <li>
            <b style="color: purple;">สีม่วง</b>
            <i style="color:black;">: สถานะสินค้าที่กำลังนำส่งให้ลูกค้า</i>
    </ol>
</div>
<script>
    function toggleInstructions() {
        var instructions = document.querySelector('.instruction-list');
        instructions.style.display = instructions.style.display === 'none' ? 'block' : 'none';
        var expandIcon = document.querySelector('.expand-icon');
        expandIcon.textContent = expandIcon.textContent === '+' ? '-' : '+';
    }

    window.onscroll = function() {
        myFunction();
    };

    var instructionsbox = document.querySelector('.instruction-box');
    var sticky = instructionsbox.offsetTop;

    function myFunction() {
        if (window.pageYOffset >= sticky) {
            instructionsbox.classList.add("sticky");
        } else {
            instructionsbox.classList.remove("sticky");
        }
    }
</script>