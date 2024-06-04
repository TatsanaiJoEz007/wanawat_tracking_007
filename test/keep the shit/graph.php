

<style>
    /* Additional Styles for the graph */
    canvas {
        max-width: 100%;
        height: auto;
    }

    .chart-container {
        margin-top: 20px;
        border: 1px solid #dcdcdc;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        background-color: #ffffff;
    }

    .chart-title {
        text-align: center;
        margin-top: 10px;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-lg-6">
            <div class="chart-container">
                <canvas id="lineChart" width="200" height="100"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="chart-container">
                <canvas id="pieChart" width="200" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Other content goes here -->

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart Data and Options
    var lineData = {
        labels: ['January', 'February', 'March', 'April', 'May', 'June'],
        datasets: [{
            label: 'ยอดการจัดส่งสินค้า',
            data: [2, 22, 25, 5, 2, 3],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
        }]
    };
    var lineOptions = {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    };

    // Pie Chart Data and Options
    var pieData = {
        labels: ['ส่งสำเร็จ', 'ออกจากปลายทาง', 'ยังอยู่ปลายทาง', 'นำส่ง', 'ยังอยู่ต้นทาง', 'ติดปัญหา'],
        datasets: [{
            data: [$total_cancel, 19, 3, 7, 3, 1],
            backgroundColor: [
                '#11D4AA',
                '#36A2EB',
                '#FFCE56',
                '#E7E1DA',
                '#9966FF',
                '#FF6384'
            ],
        }]
    };
    var pieOptions = {};

    // Initialize Charts
    var lineCtx = document.getElementById('lineChart').getContext('2d');
    var lineChart = new Chart(lineCtx, {
        type: 'line',
        data: lineData,
        options: lineOptions
    });

    var pieCtx = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(pieCtx, {
        type: 'pie',
        data: pieData,
        options: pieOptions
    });
</script>