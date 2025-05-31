<?php
// เชื่อมต่อฐานข้อมูล
require_once('config/connect.php');

// รับ tracking ID จาก URL
$trackingId = isset($_GET['trackingId']) ? $_GET['trackingId'] : '';

if (empty($trackingId)) {
    echo "<div class='alert alert-warning text-center'>กรุณาระบุหมายเลขติดตาม</div>";
    exit;
}

// Query ข้อมูลการจัดส่ง
$query = "SELECT d.*, di.bill_number, di.bill_customer_name, di.item_desc 
          FROM tb_delivery d 
          LEFT JOIN tb_delivery_items di ON d.delivery_id = di.delivery_id 
          WHERE d.delivery_number = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $trackingId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger text-center'>ไม่พบข้อมูลการจัดส่งสำหรับหมายเลข: " . htmlspecialchars($trackingId) . "</div>";
    exit;
}

$delivery_data = $result->fetch_assoc();
$current_status = (int)$delivery_data['delivery_status'];

// กำหนดขั้นตอนการจัดส่ง
$steps = [
    1 => [
        'title' => $lang == 'th' ? 'คำสั่งซื้อเข้าสู่ระบบ' : 'Order Received',
        'desc' => $lang == 'th' ? 'คำสั่งซื้อของคุณได้รับการยืนยันแล้ว' : 'Your order has been confirmed',
        'icon' => 'fas fa-clipboard-check',
        'color' => 'blue'
    ],
    2 => [
        'title' => $lang == 'th' ? 'กำลังจัดส่งไปศูนย์กระจาย' : 'Shipping to Distribution Center',
        'desc' => $lang == 'th' ? 'สินค้ากำลังเดินทางไปยังศูนย์กระจายสินค้า' : 'Items are being shipped to distribution center',
        'icon' => 'fas fa-shipping-fast',
        'color' => 'yellow'
    ],
    3 => [
        'title' => $lang == 'th' ? 'อยู่ที่ศูนย์กระจายสินค้า' : 'At Distribution Center',
        'desc' => $lang == 'th' ? 'สินค้าถึงศูนย์กระจายสินค้าปลายทางแล้ว' : 'Items have arrived at destination center',
        'icon' => 'fas fa-warehouse',
        'color' => 'grey'
    ],
    4 => [
        'title' => $lang == 'th' ? 'กำลังนำส่งให้ลูกค้า' : 'Out for Delivery',
        'desc' => $lang == 'th' ? 'สินค้ากำลังเดินทางมาหาคุณ' : 'Items are on the way to you',
        'icon' => 'fas fa-truck',
        'color' => 'purple'
    ],
    5 => [
        'title' => $current_status == 5 
            ? ($lang == 'th' ? 'จัดส่งสำเร็จ' : 'Delivered Successfully')
            : ($lang == 'th' ? 'ยังจัดส่งไม่สำเร็จ' : 'Not Yet Delivered'),
        'desc' => $current_status == 5 
            ? ($lang == 'th' ? 'สินค้าถึงมือคุณเรียบร้อยแล้ว' : 'Items have been successfully delivered')
            : ($lang == 'th' ? 'สินค้ายังไม่ถึงมือคุณ' : 'Items have not been delivered yet'),
        'icon' => $current_status == 5 ? 'fas fa-check-circle' : 'fas fa-clock',
        'color' => $current_status == 5 ? 'green' : 'pending'
    ]
];

// จัดการสถานะพิเศษ
if ($current_status == 99) {
    $problem_step = [
        'title' => $lang == 'th' ? 'เกิดปัญหา' : 'Issue Detected',
        'desc' => $lang == 'th' ? 'เกิดปัญหาในการจัดส่ง กรุณาติดต่อเจ้าหน้าที่' : 'There is an issue with delivery, please contact support',
        'icon' => 'fas fa-exclamation-triangle',
        'color' => 'red'
    ];
}
?>

<style>
    /* Container styles */
    .tracking-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        background: linear-gradient(135deg, rgba(240, 89, 46, 0.05), rgba(255, 139, 101, 0.05));
        min-height: 100vh;
    }

    /* Header styles */
    .tracking-header {
        background: linear-gradient(45deg, #F0592E, #FF4B2B);
        color: white;
        padding: 30px;
        border-radius: 20px;
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(240, 89, 46, 0.3);
        text-align: center;
    }

    .tracking-header h1 {
        font-size: 1.7rem;
        font-weight: bold;
        margin-bottom: 10px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    .tracking-number {
        font-size: 0.9rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.2);
        padding: 10px 20px;
        border-radius: 25px;
        display: inline-block;
        margin-top: 10px;
        border: 2px solid rgba(255, 255, 255, 0.3);
    }

    /* Info card styles */
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(240, 89, 46, 0.1);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding: 10px 0;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .info-row:last-child {
        border-bottom: none;
        margin-bottom: 0;
    }

    .info-label {
        font-weight: 600;
        color: #F0592E;
    }

    .info-value {
        color: #2d3748;
        font-weight: 500;
    }

    /* Timeline styles */
    .timeline-container {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .timeline-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(45deg, #F0592E, #FF4B2B);
    }

    .timeline-title {
        text-align: center;
        font-size: 1.2rem;
        font-weight: bold;
        color: #F0592E;
        margin-bottom: 40px;
        position: relative;
    }

    .timeline-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background: linear-gradient(45deg, #F0592E, #FF4B2B);
        border-radius: 2px;
    }

    /* Timeline line */
    .timeline {
        position: relative;
        padding: 20px 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(to bottom, #e2e8f0, #cbd5e0);
        transform: translateX(-50%);
        border-radius: 2px;
    }

    /* Timeline items */
    .timeline-item {
        position: relative;
        margin-bottom: 60px;
        width: 100%;
        display: flex;
        align-items: center;
    }

    .timeline-item:last-child {
        margin-bottom: 0;
    }

    .timeline-item.left {
        flex-direction: row;
    }

    .timeline-item.right {
        flex-direction: row-reverse;
    }

    /* Timeline content */
    .timeline-content {
        width: 45%;
        padding: 25px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
        position: relative;
    }

    .timeline-content:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    .timeline-content.completed {
        border-color: #10b981;
        background: linear-gradient(135deg, #ecfdf5, #f0fdf4);
    }

    .timeline-content.current {
        border-color:rgb(10, 214, 146);
        background: linear-gradient(135deg, #fff7ed, #fef3c7);
        animation: glow 2s infinite;
    }

    .timeline-content.problem {
        border-color: #ef4444;
        background: linear-gradient(135deg, #fef2f2, #fee2e2);
        animation: problemGlow 2s infinite;
    }

    @keyframes glow {
        0%, 100% {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1), 0 0 0 0 #10b981;
        }
        50% {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1), 0 0 20px 5px #10b981;
        }
    }

    @keyframes problemGlow {
        0%, 100% {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1), 0 0 0 0 #ef4444;
        }
        50% {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1), 0 0 20px 5px rgba(239, 68, 68, 0.2);
        }
    }

    /* Timeline icon */
    .timeline-icon {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        z-index: 10;
        border: 4px solid white;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease;
    }

    /* Status colors and animations */
    .status-blue {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }

    .status-blue.completed {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .status-yellow {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .status-yellow.completed {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .status-grey {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white;
    }

    .status-grey.completed {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .status-purple {
        background: linear-gradient(135deg, #8b5cf6, #7c3aed);
        color: white;
    }

    .status-purple.completed {
        background: linear-gradient(135deg, #10b981, #059669);
    }

    .status-green {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .status-red {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    /* Pulse animations */
    .timeline-icon.current {
        animation: pulse 2s infinite;
    }

    .timeline-icon.problem {
        animation: problemPulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2), 0 0 0 0 #10b981;
        }
        50% {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2), 0 0 0 15px rgba(240, 89, 46, 0);
        }
    }

    @keyframes problemPulse {
        0%, 100% {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2), 0 0 0 0 rgba(239, 68, 68, 0.7);
        }
        50% {
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2), 0 0 0 15px rgba(239, 68, 68, 0);
        }
    }

    /* Content text */
    .step-title {
        font-size: 1.3rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #1a202c;
    }

    .step-desc {
        color: #4a5568;
        line-height: 1.6;
        margin-bottom: 10px;
    }

    .step-time {
        font-size: 0.9rem;
        color: #718096;
        font-weight: 500;
    }

    /* Back button */
    .back-button {
        display: inline-flex;
        align-items: center;
        padding: 12px 25px;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(240, 89, 46, 0.3);
        border-radius: 15px;
        color: #F0592E;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-bottom: 30px;
        box-shadow: 0 6px 20px rgba(240, 89, 46, 0.2);
    }

    .back-button:hover {
        background: rgba(240, 89, 46, 0.1);
        color: #D84315;
        text-decoration: none;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(240, 89, 46, 0.3);
    }

    .back-button i {
        margin-right: 10px;
        font-size: 1.1rem;
    }

    /* Responsive design - คืนเป็นแนวตั้งเหมือนเดิม */
    @media (max-width: 768px) {
        .tracking-container {
            padding: 15px;
        }

        .tracking-header h1 {
            font-size: 1rem;
        }

        .timeline::before {
            left: 30px;
        }

        .timeline-item {
            flex-direction: column !important;
            align-items: flex-start;
        }

        .timeline-content {
            width: 100%;
            margin-left: 60px;
        }

        .timeline-icon {
            left: 30px;
            width: 50px;
            height: 50px;
            font-size: 18px;
        }

        .timeline-title {
            font-size: 1.5rem;
        }
    }
</style>

<div class="tracking-container">
    <!-- Back Button -->
    <a href="javascript:history.back()" class="back-button">
        <i class="fas fa-arrow-left"></i>
        <?php echo $lang == 'th' ? 'กลับ' : 'Back'; ?>
    </a>

    <!-- Header -->
    <div class="tracking-header">
        <h1>
            <i class="fas fa-search-location"></i>
            <?php echo $lang == 'th' ? 'ติดตามสถานะการจัดส่ง' : 'Track Delivery Status'; ?>
        </h1>
        <div class="tracking-number">
            <?php echo $lang == 'th' ? 'หมายเลขติดตาม:' : 'Tracking Number:'; ?> 
            <strong><?php echo htmlspecialchars($trackingId); ?></strong>
        </div>
    </div>

    <!-- Delivery Information -->
    <div class="info-card">
        <h3 style="color: #F0592E; margin-bottom: 20px; font-size: 1.4rem;">
            <i class="fas fa-info-circle"></i> 
            <?php echo $lang == 'th' ? 'ข้อมูลการจัดส่ง' : 'Delivery Information'; ?>
        </h3>
        <div class="info-row">
            <span class="info-label"><?php echo $lang == 'th' ? 'หมายเลขบิล:' : 'Bill Number:'; ?></span>
            <span class="info-value"><?php echo htmlspecialchars($delivery_data['bill_number'] ?? 'N/A'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?php echo $lang == 'th' ? 'ลูกค้า:' : 'Customer:'; ?></span>
            <span class="info-value"><?php echo htmlspecialchars($delivery_data['bill_customer_name'] ?? 'N/A'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?php echo $lang == 'th' ? 'รายการสินค้า:' : 'Items:'; ?></span>
            <span class="info-value"><?php echo htmlspecialchars($delivery_data['item_desc'] ?? 'N/A'); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label"><?php echo $lang == 'th' ? 'วันที่สร้าง:' : 'Created Date:'; ?></span>
            <span class="info-value"><?php echo date('d/m/Y H:i', strtotime($delivery_data['delivery_date'])); ?></span>
        </div>
    </div>

    <!-- Timeline -->
    <div class="timeline-container">
        <h2 class="timeline-title">
            <i class="fas fa-route"></i>
            <?php echo $lang == 'th' ? 'สถานะการจัดส่ง' : 'Delivery Status'; ?>
        </h2>

        <div class="timeline">
            <?php if ($current_status == 99): ?>
                <!-- Problem Status - แสดงเฉพาะสถานะปัญหา -->
                <div class="timeline-item">
                    <div class="timeline-content problem">
                        <h3 class="step-title"><?php echo $problem_step['title']; ?></h3>
                        <p class="step-desc"><?php echo $problem_step['desc']; ?></p>
                        <p class="step-time">
                            <i class="fas fa-clock"></i>
                            <?php echo date('d/m/Y H:i', strtotime($delivery_data['delivery_date'])); ?>
                        </p>
                    </div>
                    <div class="timeline-icon status-red problem">
                        <i class="<?php echo $problem_step['icon']; ?>"></i>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($steps as $step_num => $step): ?>
                    <?php
                    $is_completed = $step_num < $current_status;
                    $is_current = $step_num == $current_status;
                    $is_pending = $step_num > $current_status;
                    $position = ($step_num % 2 == 1) ? 'left' : 'right';
                    
                    $content_class = '';
                    $icon_class = '';
                    if ($is_completed) {
                        $content_class = 'completed';
                        $icon_class = 'completed';
                    } elseif ($is_current) {
                        $content_class = 'current';
                        $icon_class = 'current';
                    } elseif ($is_pending) {
                        $content_class = 'pending';
                        $icon_class = '';
                    }
                    ?>
                    
                    <div class="timeline-item <?php echo $position; ?>">
                        <div class="timeline-content <?php echo $content_class; ?>">
                            <h3 class="step-title"><?php echo $step['title']; ?></h3>
                            <p class="step-desc"><?php echo $step['desc']; ?></p>
                            <?php if ($is_completed || $is_current): ?>
                                <p class="step-time">
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('d/m/Y H:i', strtotime($delivery_data['delivery_date'])); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="timeline-icon status-<?php echo $step['color']; ?> <?php echo $icon_class; ?>">
                            <?php if ($is_completed): ?>
                                <i class="fas fa-check"></i>
                            <?php else: ?>
                                <i class="<?php echo $step['icon']; ?>"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Add smooth scroll animation when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Add entrance animation to timeline items
        const timelineItems = document.querySelectorAll('.timeline-item');
        timelineItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, index * 200);
        });

        // Add hover effects
        const timelineContents = document.querySelectorAll('.timeline-content');
        timelineContents.forEach(content => {
            content.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px) scale(1.02)';
            });
            
            content.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    });
</script>