<style>

</style>


<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">

<body>
  <div class="sidebar close">
    <div class="logo">
      <img src="../../../view/assets/img/logo/logo.png" alt="logo of wehome" weight="50px" height="50px"
        style="padding-left:8px; padding-right:10px;" />
      <!-- <i class="fas fa-home"></i> -->
      <span class="logo-name">Admin</span>
    </div>

    <ul class="nav-list">
      <li>
        <a href="../admin/dashboard">
          <i class="bx bx-grid-alt nav_icon"></i>
          <span class="link-name">Dashboard</span>
        </a>

        <ul class="sub-menu blank">
          <li><a href="../admin/dashboard" class="link-name">Dashboard</a></li>
        </ul>
      </li>

      <li>
        <div class="icon-link">
          <a href="#">
            <i class="bx bx-user nav_icon"></i>
            <span class="link-name">Permission</span>
          </a>
          <i class="fas fa-caret-down arrow"></i>
        </div>

        <ul class="sub-menu">
          <li><a href="#" class="link-name">Permission</a></li>
          <li><a href="#">Admin</a></li>
          <li><a href="#">Crerk</a></li>
          <li><a href="../admin/users">User</a></li>
        </ul>
      </li>

      <li>
        <a href="../admin/importCSV">
          <i class="bx bxs-file-import nav_icon"></i>
          <span class="link-name">ImportCSV</span>
        </a>

        <ul class="sub-menu blank">
          <li><a href="../admin/importCSV" class="link-name">ImportCSV</a></li>
        </ul>
      </li>


      <li>
        <div class="icon-link">
          <a href="#">
            <i class="bx bx-cog nav_icon"></i>
            <span class="link-name">Manage Web</span>
          </a>
          <i class="fas fa-caret-down arrow"></i>
        </div>

        <ul class="sub-menu">
          <li><a href="#" class="link-name">Manage Web</a></li>
          <li><a href="../admin/banner">Banner</a></li>
          <li><a href="../admin/contact">Contact</a></li>
          <li><a href="../admin/question">Question</a></li>
        </ul>
      </li>

      <li>
        <div class="profile-details">
          <div class="profile-content">
            <img src="https://i.imgur.com/hczKIze.jpg" alt="" />
          </div>

          <div class="name-job">
            <div class="name">Mary Karen</div>
            <div class="job">Web Developer</div>
          </div>
          <i class="fas fa-right-to-bracket"></i>
        </div>
      </li>
    </ul>
  </div>

  <div class="home-section">
    <div class="home-content">
      <i class="fas fa-bars"></i>

    </div>
  </div>

  <script>
    let btn = document.querySelector(".fa-bars");
    let sidebar = document.querySelector(".sidebar");

    btn.addEventListener("click", () => {
      sidebar.classList.toggle("close");
    });

    let arrows = document.querySelectorAll(".arrow");
    for (var i = 0; i < arrows.length; i++) {
      arrows[i].addEventListener("click", (e) => {
        let arrowParent = e.target.parentElement.parentElement;

        arrowParent.classList.toggle("show");
      });
    }
  </script>
</body>