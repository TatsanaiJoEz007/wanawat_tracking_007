<?php require_once('../config/connect.php'); ?>
<!DOCTYPE html>
<html lang="th">
<head>
    <title>Edit Banner</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    /* ปรับแต่ง modal ให้อยู่ตรงกลางจอ */
    .modal-dialog {
        display: flex;
        justify-content: center; /* จัดกลางแนวนอน */
        align-items: center; /* จัดกลางแนวตั้ง */
        min-height: 100vh; /* ตั้งค่าความสูงขั้นต่ำของ modal dialog */
        margin: 0 auto !important; /* ใช้ margin auto และ !important เพื่อให้การจัดกลางแน่นอน */
    }
    .modal {
        position: fixed;
        top: 50% !important;
        left: 50% !important;
        transform: translate(-50%, -50%) !important;
        width: auto !important;
    }
    .modal-content {
        margin: auto !important; /* จัดกลาง modal-content ใน modal-dialog */
    }
    .modal-backdrop.show {
        position: fixed;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
    }
    </style>
  
  

</head>
<body>
    <?php require_once('function/sidebar.php'); ?>

    <h1 class="app-page-title">Banner</h1>
    <hr class="mb-4">
    <div class="container">
        <div class="row g-4 settings-section">
            <div class="col-12 col-md-12">
                <div class="app-card app-card-settings shadow-sm p-4">
                    <div class="app-card-body">
                        <!-- Button to trigger modal -->
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                            เพิ่ม Banner
                        </button>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">เพิ่ม Banner</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="upload_banner" method="post" enctype="multipart/form-data">
                                            <div class="mb-3">
                                                <label for="user_firstname" class="form-label">ชื่อ</label>
                                                <input type="text" class="form-control" id="user_firstname" name="user_firstname" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="user_img" class="form-label">รูปภาพ</label>
                                                <input type="file" class="form-control" id="user_img" name="user_img" required>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                        <button type="button" class="btn btn-primary" onclick="submitBannerForm()">บันทึกข้อมูล</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                             <!-- Modal Edit-->
                             <div class="modal fade" id="exampleModalEdit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">แก้ไข Banner</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                    <form id="edit_banner" method="post" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="edit_user_firstname" class="form-label">ชื่อ</label>
                                            <input type="text" class="form-control" id="edit_user_firstname" name="user_firstname" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="edit_user_img" class="form-label">รูปภาพ</label>
                                            <input type="file" class="form-control" id="edit_user_img" name="user_img" required>
                                            <img id="edit_img_preview" src="#" alt="Banner Image Preview" style="max-width: 100%; max-height: 200px; margin-top: 10px; display: none;">
                                        </div>
                                    </form>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                                        <button type="button" class="btn btn-primary" onclick="submitEditBannerForm()">บันทึกข้อมูล</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table of Banners -->
                        <div class="table-responsive">
                            <table class="table table-striped" id="Tableall">
                                <thead>
                                    <tr>
                                        <th scope="col" style="text-align: center;">#</th>
                                        <th scope="col" style="text-align: center;">ชื่อ</th>
                                        <th scope="col" style="text-align: center;">รูปภาพ</th>
                                        <th scope="col" style="text-align: center;">เมนู</th>
                                    </tr>
                                </thead>
                                <tbody class="text-center">
                                    <?php
                                    $sql = "SELECT * FROM tb_banner";
                                    $query = $conn->query($sql);
                                    while ($row = $query->fetch_assoc()) :
                                    ?>
                                    <tr>
                                        <td class="align-middle"><?php echo $row['banner_id']; ?></td>
                                        <td class="align-middle"><?php echo $row['banner_name']; ?></td>
                                        <td class="align-middle"><img src="assets/<?php echo $row['banner_img']; ?>" alt="Banner Image" style="object-fit: cover;" width="100%" height="140" ></td>
                                        <td class="align-middle">
                                            <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModalEdit" onclick="editBanner('<?php echo $row['banner_name']; ?>', '<?php echo $row['banner_img']; ?>')">Edit</button>

                                            <button type="button" class="btn btn-sm btn-danger" onclick="editBanner('<?php echo $row['banner_id']; ?>')" >Delete</button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function submitBannerForm() {
        var formData = new FormData(document.getElementById('upload_banner'));
        fetch('function/action_uploadbanner.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            handleResponse(data, 'เพิ่ม');
        })
        .catch(handleError);
    }

    function submitEditBannerForm() {
    var formData = new FormData(document.getElementById('edit_banner'));
    $.ajax({
        url: 'function/action_editbanner.php', // Replace with your actual script URL
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(data) {
            handleResponse(data, 'แก้ไข');
        },
        error: function(error) {
            handleError(error);
        }
    });
}


    function editBanner(name, img) {
        document.getElementById('edit_user_firstname').value = name;
        document.getElementById('edit_img_preview').src = 'assets/' + img;
        document.getElementById('edit_img_preview').style.display = 'block';
        document.getElementById('exampleModalLabel').innerText = 'แก้ไข Banner';
    }


    function delBanner(id){
    let option = {
        url:'function/action_delbanner.php',
        type:'post',
        data:{
            id:id,
            delBanner:1
        },
        success:function(res){
            alertsuccess('ลบแบนเนอร์สำเร็จ')
        }
    }
    Swal.fire({
        title: 'ต้องการลบข้อมูลใช่ไหม?',
        text: "",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'ตกลง',
        cancelButtonText: 'ยกเลิก',
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax(option)
        }
    })
}

    function handleResponse(data, action) {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: `Banner ถูก${action}เรียบร้อยแล้ว`,
                icon: 'success',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: `เกิดข้อผิดพลาดในการ${action} Banner`,
                icon: 'error',
                confirmButtonText: 'ตกลง'
            });
        }
    }

    function handleError(error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'เกิดข้อผิดพลาดในการสื่อสารกับเซิร์ฟเวอร์',
            icon: 'error',
            confirmButtonText: 'ตกลง'
        });
    }
    


</script>

</body>
</html>
