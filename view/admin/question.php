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
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/bootstrap-icons.min.js"></script>
    <style>
        .container {
            max-width: 1300px;
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

        .table-scrollable {
            max-height: 800px;
            /* ปรับความสูงตามที่ต้องการ */
            overflow-y: scroll;
        }

        /* หากต้องการให้ตารางเลื่อนตามความกว้างของหน้าจอ */
        @media (max-width: 100px) {
            .table-scrollable {
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>
    <?php require_once ('function/sidebar3.php'); ?>
    <div class="container">
        <!-- Button to trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addnewModal">
            เพิ่มคำถามที่พบบ่อย
        </button> <br><br>
        <!-- Modal for add new -->
        <div class="modal fade" id="addnewModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">เพิ่มข้อมูล</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <form action="" id="add" method="post">
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
                        <button type="button" class="btn btn-primary" onclick="submitFreqForm()">บันทึกข้อมูล</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Table of Users -->
        <div class="table-responsive table-scrollable">
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
                                <a href="" onclick="" id="editModal" class="btn btn-sm btn-warning editModal"
                                    data-id="<?php echo $row['freq_id'] ?>">Edit</a>
                                <a href="" onclick="delFreq('<?php echo $row['freq_id']; ?>')"
                                    class="btn btn-sm btn-danger">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">แก้ไขข้อมูล</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm">
                        <input type="hidden" id="edit_freq_id" name="freq_id">
                        <div class="mb-3">
                            <label for="edit_freq_header" class="form-label">หัวข้อ</label>
                            <input type="text" class="form-control" id="edit_freq_header" name="freq_header" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_freq_content" class="form-label">เนื้อหา</label>
                            <input type="text" class="form-control" id="edit_freq_content" name="freq_content" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    <button type="button" class="btn btn-primary" onclick="submitEditForm()">บันทึกข้อมูล</button>
                </div>
            </div>
        </div>
    </div>

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

        $(document).ready(function () {
            $(".editModal").click(function () {
                var freq_id = $(this).data('id');
                $.ajax({
                    url: 'function/getfreq.php', // Replace with your PHP script to fetch data
                    type: 'post',
                    data: { freq_id: freq_id },
                    dataType: 'json',
                    success: function (response) {
                        // Populate the form fields with existing data
                        $('#edit_freq_id').val(response.freq_id);
                        $('#edit_freq_header').val(response.freq_header);
                        $('#edit_freq_content').val(response.freq_content);
                        $('#editModal').modal('show');
                    }
                });
            });
        });
    </script>
    <script>
        function editFreq(id) {
            var freq_header = document.getElementById('freq_header').value;
            var freq_content = document.getElementById('freq_content').value;
            var formData = new FormData();
            formData.append('freq_header', freq_header);
            formData.append('freq_content', freq_content);
            formData.append('freq_id', id);
            fetch('function/editfreq.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    handleResponse(data, 'แก้ไข');
                })
                .catch(handleError);
        }

        function submitEditForm() {
            var formData = new FormData(document.getElementById('editForm'));
            fetch('function/editFreq.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    handleResponse(data, 'แก้ไข');
                })
                .catch(handleError);
        }

        function submitFreqForm() {
            var formData = new FormData(document.getElementById('add')); // Corrected form ID
            fetch('function/addfreq.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    handleResponse(data, 'เพิ่ม');
                })
                .catch(handleError);
        }

       
 







        function handleResponse(data, action) {
            console.log(data);
            if (data.success) {
                Swal.fire({
                    title: 'Success!',
                    text: `FAQ ถูก${action}เรียบร้อยแล้ว`,
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
                    text: `เกิดข้อผิดพลาดในการ${action} FAQ`,
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