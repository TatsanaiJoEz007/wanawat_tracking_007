<?php
// ตรวจสอบ session
if (!isset($_SESSION)) {
    session_start();
}

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['login'])) {
    echo '<script>location.href="../../view/login"</script>';
    exit;
}

// ดึงข้อมูลผู้ใช้
$userId = $_SESSION['user_id'] ?? 0;

// ฟังก์ชันดึงข้อมูลผู้ใช้
function fetchUserProfile($conn, $userId)
{
    $sql = "SELECT tb_user.*, 
            provinces.name_th AS province_name, 
            amphures.name_th AS amphure_name, 
            districts.name_th AS district_name,
            districts.zip_code AS zipcode 
            FROM tb_user
            LEFT JOIN provinces ON tb_user.province_id = provinces.id 
            LEFT JOIN amphures ON tb_user.amphure_id = amphures.id 
            LEFT JOIN districts ON tb_user.district_id = districts.id 
            WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_array(MYSQLI_ASSOC);
}

function getImageBase64($imageData)
{
    return 'data:image/jpeg;base64,' . base64_encode($imageData);
}

// ดึงข้อมูลโปรไฟล์
$myprofile = null;
if ($userId && isset($conn)) {
    try {
        $myprofile = fetchUserProfile($conn, $userId);
    } catch (Exception $e) {
        error_log("Error fetching user profile: " . $e->getMessage());
    }
}

$imageBase64 = !empty($myprofile['user_img']) ? getImageBase64($myprofile['user_img']) : '../../view/assets/img/logo/mascot.png';

// ดึงข้อมูล permissions จาก session
$permissions = isset($_SESSION['permissions']) ? $_SESSION['permissions'] : [];
?>

<style>
/* Sidebar Styles */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    height: 100%;
    width: 300px;
    background: linear-gradient(180deg, #F0592E 0%, #E64A19 100%);
    z-index: 100;
    transition: all 0.5s ease;
    overflow: hidden;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.sidebar::-webkit-scrollbar {
    display: none;
}

.sidebar.close {
    width: 78px;
}

.sidebar .logo-details {
    height: 60px;
    width: 100%;
    display: flex;
    align-items: center;
    padding: 0 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar .logo-details img {
    max-height: 40px;
    width: auto;
    transition: all 0.5s ease;
}

.sidebar.close .logo-details img {
    max-height: 30px;
}

.sidebar .logo-details i {
    font-size: 30px;
    color: #fff;
    height: 50px;
    min-width: 78px;
    text-align: center;
    line-height: 50px;
}

.sidebar .logo-details .logo_name {
    font-size: 22px;
    color: #fff;
    font-weight: 600;
    transition: 0.3s ease;
    transition-delay: 0.1s;
}

.sidebar.close .logo-details .logo_name {
    transition-delay: 0s;
    opacity: 0;
    pointer-events: none;
}

.sidebar .nav-links {
    height: calc(100% - 150px);
    padding: 20px 0;
    overflow: hidden;
}

.sidebar.close .nav-links {
    overflow: hidden;
}

.sidebar .nav-links::-webkit-scrollbar {
    display: none;
}

.sidebar .nav-links li {
    position: relative;
    list-style: none;
    transition: all 0.4s ease;
    margin: 0;
}

.sidebar.close .nav-links li {
    margin: 0;
}

.sidebar .nav-links li:hover {
    background: rgba(255, 255, 255, 0.1);
}

.sidebar .nav-links li .iocn-link {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar.close .nav-links li .iocn-link {
    display: block;
}

.sidebar .nav-links li i {
    height: 50px;
    min-width: 78px;
    text-align: center;
    line-height: 50px;
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.sidebar.close .nav-links li i {
    margin: 0;
    text-align: center;
}

.sidebar .nav-links li.showMenu i.arrow {
    transform: rotate(-180deg);
}

.sidebar.close .nav-links i.arrow {
    display: none;
}

.sidebar .nav-links li a {
    display: flex;
    align-items: center;
    text-decoration: none;
}

.sidebar .nav-links li a .link_name {
    font-size: 18px;
    font-weight: 400;
    color: #fff;
    transition: all 0.4s ease;
}

.sidebar.close .nav-links li a .link_name {
    opacity: 0;
    pointer-events: none;
}

.sidebar .nav-links li .sub-menu {
    padding: 6px 6px 14px 80px;
    margin-top: -10px;
    background: rgba(0, 0, 0, 0.1);
    display: none;
}

.sidebar .nav-links li.showMenu .sub-menu {
    display: block;
}

.sidebar .nav-links li .sub-menu a {
    color: #fff;
    font-size: 15px;
    padding: 5px 0;
    white-space: nowrap;
    opacity: 0.8;
    transition: all 0.3s ease;
}

.sidebar .nav-links li .sub-menu a:hover {
    opacity: 1;
}

.sidebar.close .nav-links li .sub-menu {
    position: absolute;
    left: 100%;
    top: -10px;
    margin-top: 0;
    padding: 10px 20px;
    border-radius: 0 6px 6px 0;
    opacity: 0;
    display: block;
    pointer-events: none;
    transition: 0s;
    background: #F0592E;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    min-width: 200px;
    z-index: 1000;
}

.sidebar.close .nav-links li:hover .sub-menu {
    top: 0;
    opacity: 1;
    pointer-events: auto;
    transition: all 0.4s ease;
}

.sidebar .nav-links li .sub-menu .link_name {
    display: none;
}

.sidebar.close .nav-links li .sub-menu .link_name {
    font-size: 18px;
    opacity: 1;
    display: block;
}

.sidebar .nav-links li .sub-menu.blank {
    opacity: 1;
    pointer-events: auto;
    padding: 3px 20px 6px 16px;
    opacity: 0;
    pointer-events: none;
}

.sidebar .nav-links li:hover .sub-menu.blank {
    top: 50%;
    transform: translateY(-50%);
}

.sidebar .profile-details {
    position: fixed;
    bottom: 0;
    width: 300px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(0, 0, 0, 0.1);
    padding: 12px;
    transition: all 0.5s ease;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar.close .profile-details {
    background: rgba(0, 0, 0, 0.1);
    width: 78px;
    justify-content: center;
}

.sidebar.close .profile-details .profile-content {
    margin: 0;
}

.sidebar .profile-details .profile-content {
    display: flex;
    align-items: center;
}

.sidebar .profile-details img {
    height: 45px;
    width: 45px;
    object-fit: cover;
    border-radius: 50%;
    margin: 0 10px 0 5px;
    background: #fff;
    transition: all 0.5s ease;
}

.sidebar.close .profile-details img {
    padding: 8px;
}

.sidebar .profile-details .profile_name,
.sidebar .profile-details .job {
    color: #fff;
    font-size: 16px;
    font-weight: 500;
    white-space: nowrap;
}

.sidebar.close .profile-details i,
.sidebar.close .profile-details .profile_name,
.sidebar.close .profile-details .job {
    display: none;
}

.sidebar .profile-details .job {
    font-size: 12px;
    opacity: 0.8;
}

.sidebar .profile-details i {
    color: #fff;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.sidebar .profile-details i:hover {
    color: #ff6b6b;
}

/* Mobile Responsive */
@media screen and (max-width: 768px) {
    .sidebar {
        width: 240px;
        transform: translateX(-100%);
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .sidebar.close {
        width: 240px;
        transform: translateX(-100%);
    }

    .sidebar.close.active {
        transform: translateX(0);
    }

    .sidebar .profile-details {
        width: 240px;
    }
}
</style>

<!-- Sidebar HTML -->
<div class="sidebar close">
    <div class="logo-details">
        <img src="../../view/assets/img/logo/logo.png" alt="logo" height="50px" style="padding-left:8px; padding-right:10px;" />
        <span class="logo_name"><?php echo $_SESSION['user_type'] ?? 'Employee'; ?></span>
    </div>
    <ul class="nav-links">
        <!-- Dashboard -->
        <li>
            <a href="dashboard.php">
                <i class="bx bx-grid-alt"></i>
                <span class="link_name">Dashboard</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="dashboard.php">Dashboard</a></li>
            </ul>
        </li>
        
        <!-- นำเข้าข้อมูลCSV - สำหรับผู้มีสิทธิ์ -->
        <?php if (isset($permissions['manage_csv']) && $permissions['manage_csv'] == 1): ?>
        <li>
            <a href="../employee/importCSV.php">
                <i class="bx bx-upload"></i>
                <span class="link_name">นำเข้าข้อมูลCSV</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="../employee/importCSV.php">นำเข้าข้อมูลCSV</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- สถานะขนส่ง - สำหรับผู้มีสิทธิ์ -->
        <?php if (isset($permissions['manage_statusbill']) && $permissions['manage_statusbill'] == 1): ?>
        <li>
            <a href="../employee/statusbill.php">
                <i class="bi bi-truck"></i>
                <span class="link_name">จัดการสถานะขนส่ง</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="../employee/importCSV.php">สถานะขนส่ง</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- ประวัติการดำเนินขนส่ง - สำหรับผู้มีสิทธิ์ -->
        <?php if (isset($permissions['manage_history']) && $permissions['manage_history'] == 1): ?>
        <li>
            <a href="../employee/history.php">
                <i class="bx bx-history"></i>
                <span class="link_name">ประวัติการดำเนินขนส่ง</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="../employee/history.php">ประวัติการดำเนินขนส่ง</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- จัดการใบส่งสินค้า IC - สำหรับผู้มีสิทธิ์ -->
        <?php if (isset($permissions['manage_ic_delivery']) && $permissions['manage_ic_delivery'] == 1): ?>
        <li>
            <a href="../employee/ic_delivery.php">
                <i class="bx bx-file"></i>
                <span class="link_name">จัดการใบส่งสินค้า IC</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="../employee/ic_delivery.php">จัดการใบส่งสินค้า IC</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- จัดการใบส่งสินค้า IV - สำหรับผู้มีสิทธิ์ -->
        <?php if (isset($permissions['manage_iv_delivery']) && $permissions['manage_iv_delivery'] == 1): ?>
        <li>
            <a href="../employee/iv_delivery.php">
                <i class="bx bx-file-blank"></i>
                <span class="link_name">จัดการใบส่งสินค้า IV</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="../employee/iv_delivery.php">จัดการใบส่งสินค้า IV</a></li>
            </ul>
        </li>
        <?php endif; ?>
    </ul>

    <!-- Profile Details -->
    <div class="profile-details">
        <div class="profile-content">
            <img src="<?php echo $imageBase64; ?>" alt="profileImg">
        </div>
        <div class="name-job">
            <div class="profile_name"><?php echo $myprofile['user_firstname'] ?? 'Employee'; ?> <?php echo $myprofile['user_lastname'] ?? ''; ?></div>
            <div class="job"><?php echo $myprofile['user_email'] ?? 'employee@example.com'; ?></div>
        </div>
        <i class='bx bx-log-out' id="sidebar-logout-btn"></i>
    </div>
</div>

<script>
// Sidebar JavaScript
document.addEventListener("DOMContentLoaded", function() {
    // Sidebar functionality
    let sidebar = document.querySelector(".sidebar");
    let sidebarBtn = document.querySelector(".bx-menu");
    
    // Sidebar toggle
    if (sidebarBtn) {
        sidebarBtn.addEventListener("click", () => {
            sidebar.classList.toggle("close");
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle("active");
            }
        });
    }

    // Submenu toggle (ไม่มี submenu ใน employee sidebar แต่เก็บไว้สำหรับอนาคต)
    let arrows = document.querySelectorAll(".arrow");
    arrows.forEach(arrow => {
        arrow.addEventListener("click", (e) => {
            let arrowParent = e.target.parentElement.parentElement;
            arrowParent.classList.toggle("showMenu");
        });
    });

    // Mobile sidebar overlay
    if (window.innerWidth <= 768) {
        document.addEventListener('click', function(e) {
            if (!sidebar.contains(e.target) && !sidebarBtn.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    }

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
        }
    });

    // Logout functionality
    const sidebarLogoutBtn = document.getElementById('sidebar-logout-btn');
    if (sidebarLogoutBtn) {
        sidebarLogoutBtn.addEventListener('click', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'ยืนยันการออกจากระบบ',
                    text: 'คุณต้องการออกจากระบบใช่หรือไม่?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#F0592E',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'ออกจากระบบ',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // ส่งคำขอ logout ไปยัง server
                        fetch('../../view/admin/function/logout.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                csrf_token: '<?php echo $_SESSION['csrf_token'] ?? ''; ?>'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = '../../view/login';
                            } else {
                                Swal.fire('เกิดข้อผิดพลาด', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            window.location.href = '../../view/login';
                        });
                    }
                });
            } else {
                // หาก SweetAlert ไม่มี ใช้ confirm ธรรมดา
                if (confirm('คุณต้องการออกจากระบบใช่หรือไม่?')) {
                    window.location.href = '../../view/login';
                }
            }
        });
    }
});
</script>