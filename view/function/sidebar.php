
<style>
  body, html {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
}

.sidebar {
    width: 250px;
    height: 100vh;
    background-color: #ff8c00; /* Orange background */
    color: white;
    padding: 20px;
}

.brand h1 {
    margin: 0;
    font-size: 20px;
}

.user-panel {
    margin-top: 20px;
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.user-image {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    margin-right: 10px;
}

.user-info p {
    margin: 0;
    font-weight: bold;
}

.nav-menu ul {
    list-style-type: none;
    padding: 0;
}

.nav-menu li {
    padding: 10px 0;
}

.nav-menu a {
    color: white;
    text-decoration: none;
    transition: color 0.3s;
}

.nav-menu a:hover {
    color: #e67e22; /* Lighter orange for hover */
}
.nav-menu ul {
    list-style-type: none;
    padding: 0;
    margin: 0;
}

.nav-menu li {
    position: relative;
    padding: 10px 0;
}

.nav-menu a {
    color: white;
    text-decoration: none;
    display: block;
    transition: color 0.3s;
}

.nav-menu a:hover, .dropbtn:hover {
    color: #e67e22; /* Lighter orange for hover */
}

.dropdown-content {
    display: none;
    position: absolute;
    background-color: #ff8c00; /* Same as sidebar */
    box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
    z-index: 1;
}

.dropdown-content a {
    color: white;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
}

.dropdown-content a:hover {
    background-color: #e67e22; /* Hover state for dropdown items */
}

.dropdown:hover .dropdown-content {
    display: block; /* Show dropdown content on hover */
}
/* Base styles */
/* (Already in your CSS, shown for context) */
.sidebar {
    width: 250px; /* Fixed width for larger screens */
    height: 100vh;
    background-color: #ff8c00; /* Orange background */
    color: white;
    padding: 20px;
    transition: width 0.3s ease;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .sidebar {
        width: 50%; /* Full width on smaller screens */
        padding: 10px;
    }
    .nav-menu ul {
        padding: 0;
    }
    .nav-menu li {
        padding: 8px 0; /* Smaller padding */
    }
    .dropdown-content {
        position: relative; /* Change position for better view on small screens */
    }
}

@media (max-width: 480px) {
    .sidebar {
        padding: 5px;
    }
    .brand h1, .user-info p {
        font-size: 16px; /* Smaller font size */
    }
    .user-panel {
        flex-direction: column; /* Stack elements vertically */
        align-items: center;
    }
    .user-image {
        margin: 10px 0; /* Adjust margin for vertical stacking */
    }
    .nav-menu a, .dropdown-content a {
        font-size: 14px; /* Smaller font size for links */
    }
}
/* Hamburger Menu Style */
.hamburger {
    display: none; /* Hidden by default */
    font-size: 30px; /* Icon size */
    color: white;
    padding: 10px;
    cursor: pointer;
}

/* Existing styles... */

/* Media query for devices with max-width of 768px */
@media (max-width: 768px) {
    .hamburger {
        display: block; /* Show hamburger icon */
        position: fixed; /* Fixed at the top */
        top: 0; right: 0;
        z-index: 2; /* Above other content */
        background-color: #ff8c00; /* Match sidebar color */
    }

    .sidebar {
        width: 100%; /* Full width */
        height: 100vh; /* Full height */
        position: fixed;
        top: 0; left: -100%; /* Start off-screen */
        overflow-y: auto; /* Scrollable sidebar */
        transition: left 0.3s; /* Smooth transition for sidebar */
    }

    .sidebar.open {
        left: 0; /* Move sidebar on-screen */
    }
}

/* Ensure dropdowns work nicely within the responsive sidebar */
.dropdown-content {
    display: block; /* Always block in mobile view */
    position: relative; /* Avoids overlap and positioning issues */
}

</style>

<div class="hamburger">&#9776; Menu</div> <!-- Hamburger icon -->
<aside class="sidebar">
    <div class="brand">
    <img src="../assets/img/logo/logo.png" alt="User Image" class="logo" width="65" height="52" >
    </div>
    <div class="user-panel">
        <img src="../assets/img/logo/mascot.png" alt="User Image" class="user-image">
        <div class="user-info">
            <p>Admin JA</p>
        </div>
    </div>
    <nav class="nav-menu">
    <ul>
        <a href="#" class="sildout">ออกจากระบบ</a>
        <li class="dropdown">
            <a href="#" class="dropbtn">รอใส่อะไรสักอย่างครับ</a>
            <div class="dropdown-content">
                <a href="#">ใส่ผมเข้ามาที 1</a>
                <a href="#">ใส่ผมเข้ามาที 2</a>
                <a href="#">ใส่ผมเข้ามาที 3</a>
            </div>
        </li>
        <li class="dropdown">
            <a href="#" class="dropbtn">รอใส่อะไรสักอย่างครับ</a>
            <div class="dropdown-content">
                <a href="#">ใส่ผมเข้ามาที 1</a>
                <a href="#">ใส่ผมเข้ามาที 2</a>
                <a href="#">ใส่ผมเข้ามาที 3</a>
            </div>
        </li>
        <li class="dropdown">
            <a href="#" class="dropbtn">รอใส่อะไรสักอย่างครับ</a>
            <div class="dropdown-content">
                <a href="#">ใส่ผมเข้ามาที 1</a>
                <a href="#">ใส่ผมเข้ามาที 2</a>
                <a href="#">ใส่ผมเข้ามาที 3</a>
            </div>
        </li>
       
    </ul>
</nav>

</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var hamburger = document.querySelector('.hamburger');
    var sidebar = document.querySelector('.sidebar');
    var dropdowns = document.querySelectorAll('.dropdown');

    // Hamburger menu toggle
    hamburger.addEventListener('click', function() {
        sidebar.classList.toggle('open');
    });

    // Dropdown toggle
    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('click', function(event) {
            // Close all open dropdowns
            dropdowns.forEach(function(d) {
                if (d !== dropdown) {
                    d.querySelector('.dropdown-content').style.display = 'none';
                }
            });

            // Toggle this dropdown
            var dropdownContent = dropdown.querySelector('.dropdown-content');
            if (dropdownContent.style.display == 'block') {
                dropdownContent.style.display = 'none';
            } else {
                dropdownContent.style.display = 'block';
            }
            event.stopPropagation(); // Stop click event from bubbling up further
        });
    });

    // Click anywhere to close dropdown
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.dropdown')) {
            var openDropdowns = document.querySelectorAll('.dropdown-content');
            openDropdowns.forEach(function(openDropdown) {
                openDropdown.style.display = 'none';
            });
        }
    });
});
</script>
