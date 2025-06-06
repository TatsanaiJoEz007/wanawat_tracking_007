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

// แสดงประเภทผู้ใช้งาน
$userTypeText = 'Admin';
if (isset($_SESSION['user_type'])) {
    switch ($_SESSION['user_type']) {
        case 999:
            $userTypeText = 'Admin';
            break;
        case 1:
            $userTypeText = 'Employee';
            break;
        default:
            $userTypeText = 'User';
            break;
    }
}
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
<!-- Sidebar -->
<div class="sidebar close">
    <div class="logo-details">
        <img src="../../view/assets/img/logo/logo.png" alt="logo" height="50px" style="padding-left:8px; padding-right:10px;" />
        <span class="logo_name"><?php echo htmlspecialchars($userTypeText); ?></span>
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

        <!-- ผู้ใช้งานในระบบ - เมนูสำหรับผู้ดูแลระบบ -->
        <?php if (isset($permissions['manage_permission']) && $permissions['manage_permission'] == 1): ?>
        <li>
            <a href="../admin/edituser.php">
                <i class="bx bx-user nav_icon"></i>
                <span class="link_name">ตารางข้อมูลผู้ใช้งานในระบบ</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="../admin/edituser.php">ตารางข้อมูลผู้ใช้งานในระบบ</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- ควบคุมสิทธิ์การเข้าถึง -->
        <?php if (isset($permissions['manage_permission']) && $permissions['manage_permission'] == 1): ?>
        <li>
            <a href="../admin/Manage.php">
                <i class="bx bxs-heart"></i>
                <span class="link_name">ควบคุมสิทธิ์การเข้าถึง</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="../admin/Manage.php">ควบคุมสิทธิ์การเข้าถึง</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- จัดการหน้าเว็บไซต์ -->
        <?php if (isset($permissions['manage_website']) && $permissions['manage_website'] == 1): ?>
        <li>
            <div class="iocn-link">
                <a href="#">
                    <i class="bx bx-cog nav_icon"></i>
                    <span class="link_name">จัดการหน้าเว็บไซต์</span>
                </a>
                <i class='bx bxs-chevron-down arrow'></i>
            </div>
            <ul class="sub-menu">
                <li><a class="link_name" href="#">จัดการหน้าเว็บไซต์</a></li>
                <li><a href="../admin/banner">หน้าแบนเนอร์</a></li>
                <li><a href="../admin/question.php">หน้าคำถามที่พบบ่อย</a></li>
            </ul>
        </li>
        <?php endif; ?>

        <!-- เมนูอื่นๆ ตาม permissions -->
        <?php if (isset($permissions['manage_logs']) && $permissions['manage_logs'] == 1): ?>
        <li>
            <a href="../admin/activity.php">
                <i class="bi bi-activity nav_icon"></i>
                <span class="link_name">ประวัติกิจกรรม</span>
            </a>
            <ul class="sub-menu blank">
                <li><a class="link_name" href="../admin/activity.php">ประวัติกิจกรรม</a></li>
            </ul>
        </li>
        <?php endif; ?>
    </ul>

    <!-- Profile Details -->
    <div class="profile-details">
        <div class="profile-content">
            <img src="<?php echo htmlspecialchars($imageBase64); ?>" alt="profileImg">
        </div>
        <div class="name-job">
            <div class="profile_name"><?php echo htmlspecialchars($myprofile['user_firstname'] ?? 'Admin'); ?> <?php echo htmlspecialchars($myprofile['user_lastname'] ?? ''); ?></div>
            <div class="job"><?php echo htmlspecialchars($myprofile['user_email'] ?? 'admin@example.com'); ?></div>
        </div>
        <i class='bx bx-log-out' id="sidebar-logout-btn" title="ออกจากระบบ"></i>
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

    // Submenu toggle
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
        sidebarLogoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
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
                        performLogout();
                    }
                });
            } else {
                // หาก SweetAlert ไม่มี ใช้ confirm ธรรมดา
                if (confirm('คุณต้องการออกจากระบบใช่หรือไม่?')) {
                    performLogout();
                }
            }
        });
    }

    // ฟังก์ชันสำหรับทำการ logout
    function performLogout() {
        // แสดง loading (ถ้ามี SweetAlert)
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'กำลังออกจากระบบ...',
                text: 'กรุณารอสักครู่',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        // ส่งคำขอ logout ไปยัง server
        fetch('../../view/function/action_logout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                csrf_token: '<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? ''); ?>'
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Logout response:', data);
            
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'ออกจากระบบสำเร็จ!',
                        text: data.message || 'ขอบคุณที่ใช้บริการ',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        redirectToLogin();
                    });
                } else {
                    redirectToLogin();
                }
            } else {
                console.error('Logout failed:', data);
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด',
                        text: data.message || 'ไม่สามารถออกจากระบบได้',
                        icon: 'error',
                        confirmButtonColor: '#F0592E'
                    }).then(() => {
                        // ถึงแม้จะมี error ก็ควรส่งไป login page เพื่อความปลอดภัย
                        redirectToLogin();
                    });
                } else {
                    alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถออกจากระบบได้'));
                    redirectToLogin();
                }
            }
        })
        .catch(error => {
            console.error('Logout error:', error);
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                    icon: 'error',
                    confirmButtonColor: '#F0592E'
                }).then(() => {
                    redirectToLogin();
                });
            } else {
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                redirectToLogin();
            }
        });
    }

    // ฟังก์ชันเปลี่ยนเส้นทางไป login page
    function redirectToLogin() {
        // ล้าง localStorage (ถ้ามี)
        if (typeof(Storage) !== "undefined") {
            localStorage.clear();
            sessionStorage.clear();
        }
        
        // เปลี่ยนเส้นทางไป login page
        window.location.href = '../../view/login';
    }
});
</script>