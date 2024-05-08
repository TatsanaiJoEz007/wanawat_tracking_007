<?php require_once ('../config/connect.php'); ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <title>Manage - Question</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">

    <!-- เรียกใช้ Bootstrap CSS จาก CDN -->
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .accordion-container {
            margin-top: 20px;
        }

        .accordion {
            cursor: pointer;
            border: none;
            outline: none;
            font-size: 18px;
            font-weight: bold;
            transition: 0.3s;
            width: 100%;
            text-align: left;
            color: #F0592E;
            /* Removed hover effect */
        }

        .panel {
            padding: 0 18px;
            background-color: white;
            display: none;
            overflow: hidden;
        }

        .panel.show {
            display: block;
        }

        .accordion:after {
            content: '\002B';
            color: #777;
            font-weight: bold;
            float: right;
            margin-left: 5px;
        }

        .accordion.active:after {
            content: "\2212";
        }

        .panel-content {
            border-top: 2px solid #F0592E;
            padding-top: 10px;
        }

        .edit-icon {
            float: right;
            margin-top: -5px;
            color: #888;
            cursor: pointer;
        }

        .edit-icon:hover {
            color: #333;
        }

        .editable {
            border: 1px solid #ccc;
            padding: 5px;
            width: 100%;
            min-height: 100px;
            resize: vertical;
            /* Allow vertical resizing */
            border-radius: 10px;
            transition: none;
            /* Remove transition */
        }

        .submit-btn {
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <?php require_once ('function/sidebar3.php'); ?>
    <div class="container">
        <!-- Button to trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
            เพิ่มคำถามที่พบบ่อย
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">เพิ่มข้อมูล</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form action="function/addfreq.php" id="add" method="post">
                            <div class="mb-3">
                                <label for="user_firstname" class="form-label">หัวข้อ</label>
                                <input type="text" class="form-control" id="freq_header" name="freq_header" required>
                            </div>
                            <div class="mb-3">
                                <label for="user_lastname" class="form-label">เนื้อหา</label>
                                <input type="text" class="form-control" id="freq_content" name="freq_content" required>
                            </div>
                        </form>



                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" form="add" class="btn btn-primary">บันทึกข้อมูล</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Table of Users -->
        <div class="table-responsive">
            <table class="table table-striped" id="Tableall">
                <thead>
                    <tr>
                        <th scope="col" style="text-align: center;">#</th>
                        <th scope="col" style="text-align: center;">หัวข้อคำถาม</th>
                        <th scope="col" style="text-align: center;">คำตอบ</th>
                        <th scope="col" style="text-align: center;">เมนู</th>
                    </tr>
                </thead>
                <tbody class="text-center">
                    <?php
                    $i = 1;
                    $sql = "SELECT * FROM tb_freq";
                    $query = $conn->query($sql);
                    foreach ($query as $row):
                        ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td class="align-middle"><?php echo $row['freq_header'] ?></td>
                            <td class="align-middle"><?php echo $row['freq_content'] ?></td>
                            <td class="align-middle">
                                <a href=""  class="btn btn-sm btn-warning">Edit</a>
                                <a href="" onclick="delFreq('<?php echo $row['freq_id']; ?>')" class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/bootstrap-icons.min.js"></script>
    <script>
        var acc = document.getElementsByClassName("accordion");
        var i;

        for (i = 0; i < acc.length; i++) {
            acc[i].addEventListener("click", function () {
                this.classList.toggle("active");
                var panel = this.nextElementSibling;
                if (panel.style.display === "block") {
                    panel.style.display = "none";
                } else {
                    panel.style.display = "block";
                }
            });
        }

        function delFreq(id) {
            let option = {
                url: 'function/delfreq.php',
                type: 'post',
                data: {
                    id: id,
                    delBanner: 1
                },
                success: function (res) {
                    // Display success message using Swal.fire
                    Swal.fire({
                        title: 'Success!',
                        text: 'ลบคำถามสำเร็จ',
                        icon: 'success',
                        confirmButtonText: 'ตกลง'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload(); // Reload the page after successful deletion
                        }
                    });
                },
                error: function (xhr, status, error) {
                    // Display error message using Swal.fire
                    Swal.fire({
                        title: 'Error!',
                        text: 'เกิดข้อผิดพลาดในการลบคำถาม',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                    console.error(xhr.responseText); // Log the error response for debugging
                }
            };

            // Show confirmation dialog before proceeding with the deletion
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
                    // Proceed with AJAX request to delete the banner
                    $.ajax(option);
                }
            });
        }

       
    </script>
    <script src="function/func_addfreq.php"></script>
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>