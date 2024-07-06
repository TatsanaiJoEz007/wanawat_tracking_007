<?php
require_once('../config/connect.php'); 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <title>Manage - Question</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <link rel="stylesheet" href="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://fastly.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/bootstrap-icons.min.js"></script>
</head>

<style>
    body {
        font-family: 'Kanit', sans-serif;
        margin: 0;
        padding: 0px;
        background-color: #f0f0f0;
    }

    h1 {
        text-align: center;
        margin-bottom: 20px;
        color: #333;
    }

    .faq-container {
        max-width: 1000px;
        margin: 0 auto;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 5px;
        padding: 20px;
    }

    .faq {
        border-bottom: 1px solid #ccc;
        padding: 20px 0;
    }

    .faq:last-child {
        border-bottom: none;
    }

    .faq h2 {
        margin-top: 0;
        color: #333;
    }

    .faq p {
        margin-bottom: 10px;
        color: #666;
    }

    .faq a {
        text-decoration: none;
        color: #007bff;
        transition: color 0.3s;
    }

    .faq a:hover {
        color: #0056b3;
    }

    .new-faq-btn {
        text-decoration: none;
        background-color: #007bff;
        color: #fff;
        padding: 10px 20px;
        margin-right: 145px;
        border-radius: 6px;
        display: inline-block;
        transition: background-color 0.3s;
    }

    .new-faq-btn:hover {
        background-color: #0056b3;
    }

    .swal2-textarea {
        resize: vertical;
        min-height: 100px;
        max-height: 300px;
        border-radius: 5px;
    }

    @media (max-width: 100px) {
        .table-scrollable {
            overflow-x: auto;
        }
    }

    ::-webkit-scrollbar {
        width: 12px;
    }

    ::-webkit-scrollbar-thumb {
        background-color: #FF5722;
        border-radius: 10px;
    }

    .home-section {
        max-height: 100vh;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 20px;
        background-color: #f9f9f9;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
</style>

<body>
    <?php require_once('function/sidebar.php'); ?>
    <h1>Admin FAQ Panel</h1>

    <div style="text-align: right; margin-bottom: 20px;">
        <button class="new-faq-btn" id="new-faq-btn">NEW FAQ</button>
    </div>

    <div class="faq-container">
        <?php
        include '../config/connect.php';

        // Fetch all FAQs from the database
        $query = "SELECT * FROM tb_freq";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='faq'>";
                echo "<h2>" . htmlspecialchars($row['freq_header']) . "</h2>";
                echo "<p>" . htmlspecialchars($row['freq_content']) . "</p>";
                echo "<p>Created at: " . htmlspecialchars($row['freq_create_at']) . "</p>";
                echo "<button class='edit-faq-btn btn btn-primary' data-id='" . htmlspecialchars($row['freq_id']) . "'>Edit</button> ";
                echo "<button class='delete-faq-btn btn btn-danger' data-id='" . htmlspecialchars($row['freq_id']) . "'>Delete</button>";
                echo "</div>";
            }
        } else {
            echo "<p>No FAQs found</p>";
        }

        mysqli_close($conn);
        ?>
    </div>

    <script>
        $('#new-faq-btn').click(function() {
            Swal.fire({
                title: 'Add New FAQ',
                html: '<input id="header" class="swal2-input" placeholder="Header">' +
                    '<textarea id="content" class="swal2-textarea" placeholder="Content"></textarea>',
                showCancelButton: true,
                confirmButtonText: 'Add',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    var header = $('#header').val();
                    var content = $('#content').val();

                    return $.ajax({
                        url: 'function/new_faq.php',
                        type: 'POST',
                        data: {
                            freq_header: header,
                            freq_content: content
                        }
                    }).done(function(response) {
                        if (response.trim() == "success") {
                            Swal.fire({
                                title: 'FAQ Added!',
                                text: 'เพิ่มคำถามที่พบบ่อยสำเร็จ',
                                icon: 'success',
                                timer: 3000,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: 'เกิดปัญหาในการเพิ่มคำถามที่พบบ่อย',
                                icon: 'error',
                                timer: 3000,
                                showConfirmButton: false
                            });
                        }
                    }).fail(function() {
                        Swal.fire({
                            title: 'Error',
                            text: 'เพิ่มคำถามที่พบบ่อยไม่สำเร็จ',
                            icon: 'error',
                            timer: 3000,
                            showConfirmButton: false
                        });
                    });
                }
            });
        });

        $(document).on('click', '.edit-faq-btn', function() {
            var freq_id = $(this).data('id');

            $.ajax({
                url: 'function/getfreq.php?id=' + freq_id,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    Swal.fire({
                        title: 'Edit FAQ',
                        html: '<input id="editedHeader" class="swal2-input" placeholder="Header" value="' + data.freq_header + '">' +
                            '<textarea id="editedContent" class="swal2-textarea" placeholder="Content">' + data.freq_content + '</textarea>',
                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.ajax({
                                url: 'function/edit_faq.php?id=' + freq_id,
                                type: 'POST',
                                data: {
                                    header: $('#editedHeader').val(),
                                    content: $('#editedContent').val()
                                }
                            }).done(function(response) {
                                Swal.fire({
                                    title: 'FAQ Edited!',
                                    text: 'แก้ไขสำเร็จ',
                                    icon: 'success',
                                    timer: 3000,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            }).fail(function() {
                                Swal.fire({
                                    title: 'Error',
                                    text: 'แก้ไขไม่สำเร็จ',
                                    icon: 'error',
                                    timer: 3000,
                                    showConfirmButton: false
                                });
                            });
                        }
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error:', errorThrown);
                }
            });
        });

        $(document).on('click', '.delete-faq-btn', function() {
            var freq_id = $(this).data('id');
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "หากลบแล้วคุณจะไม่สามารถเรียกคืนได้อีก!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'ใช่, ลบเลย!'
            }).then((result) => {
                if (result.isConfirmed) {
                $.ajax({
                    url: 'function/delete_faq.php?id=' + freq_id,
                    type: 'POST'
                }).done(function(response) {
                    Swal.fire({
                        title: 'ลบสำเร็จ!',
                        text: 'ลบคำถามที่พบบ่อยสำเร็จ',
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }).fail(function() {
                    Swal.fire({
                        title: 'ลบไม่สำเร็จ',
                        text: 'เกิดปัญหาในการเพิ่มคำถามที่พบบ่อย',
                        icon: 'error',
                        timer: 3000,
                        showConfirmButton: false
                    });
                });
            }
        });
    });
</script>
</body>
</html>