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
        margin-right: 500px;
        border-radius: 5px;
        display: inline-block;
        transition: background-color 0.3s;
    }

    .new-faq-btn:hover {
        background-color: #0056b3;
    }

    .swal2-textarea {
    resize: vertical; /* Allow vertical resizing */
    min-height: 100px; /* Set a minimum height */
    max-height: 300px; /* Set a maximum height */
    border-radius: 5px;
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
    <?php require_once ('function/sidebar.php'); ?>
    <h1>Admin FAQ Panel</h1>

    <!-- New FAQ Button -->
    <div style="text-align: right; margin-bottom: 20px;">
        <button class="new-faq-btn" id="new-faq-btn">NEW FAQ</button>

    </div>

    <div class="faq-container table-scrollable">
        <?php
        // Include database connection
        include '../config/connect.php';

        // Fetch all FAQs from the database
        $query = "SELECT * FROM tb_freq";
        $result = mysqli_query($conn, $query);
  
        // Check if FAQs exist
        if (mysqli_num_rows($result) > 0) {
            // Output data of each row
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<div class='faq'>";
                echo "<h2>" . $row['freq_header'] . "</h2>";
                echo "<p>" . $row['freq_content'] . "</p>";
                echo "<p>Created at: " . $row['freq_create_at'] . "</p>";
                echo "<button class='edit-faq-btn btn btn-primary' data-id='" . $row['freq_id'] . "'>Edit</button> ";
                echo "&nbsp;"; // Add space between buttons
                echo "<button class='delete-faq-btn btn btn-danger' data-id='" . $row['freq_id'] . "'>Delete</button>";
                
          
            }
           
        } else {
            echo "No FAQs found";
        }
    
        // Close database connection
        mysqli_close($conn);
        ?>
    </div>
    <script>
        // Function to handle new FAQ addition
        $('#new-faq-btn').click(function () {
            Swal.fire({
                title: 'Add New FAQ',
                html: '<input id="header" class="swal2-input" placeholder="Header">' +
                    '<input id="content" class="swal2-input" placeholder="Content">',
                showCancelButton: true,
                confirmButtonText: 'Add',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    // Retrieve input field values
                    var header = $('#header').val();
                    var content = $('#content').val();

                    // AJAX request to add FAQ
                    return $.ajax({
                        url: 'function/new_faq.php',
                        type: 'POST',
                        data: {
                            freq_header: header,
                            freq_content: content
                        }
                    }).done(function (response) {
                        if (response.trim() == "success") {
                            Swal.fire('FAQ Added!', '', 'success').then(() => {
                                location.reload(); // Reload page after adding FAQ
                            });
                        } else {
                            Swal.fire(['Yayyyyyy...', 'Sabattag to add FAQ', 'Success']);
                            location.reload();
                        }
                    }).fail(function () {
                        Swal.fire('Yayyyyyy...', 'Sabattag to add FAQ', 'Success');
                        location.reload();
                    });
                }
            });
        });

        // Function to handle editing FAQ
        $(document).on('click', '.edit-faq-btn',function () {
            var freq_id = $(this).data('id');

            // Retrieve existing FAQ data
            $.ajax({
                url: 'function/getfreq.php?id=' + freq_id,
                type: 'POST',
                data : {freq_id : freq_id},
                dataType: 'json',
                success: function (data) {
                    console.log(data);
                    Swal.fire({
                        title: 'Edit FAQ',
                        html: '<input id="editedHeader" class="swal2-input" placeholder="Header" value="' + data.freq_header + '">' +
    '<textarea id="editedContent" class="swal2-textarea" placeholder="Content">' + data.freq_content + '</textarea>',

                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            // AJAX request to edit FAQ
                            return $.ajax({
                                url: 'function/edit_faq.php?id=' + freq_id,
                                type: 'POST',
                                data: {
                                    header: $('#editedHeader').val(),
                                    content: $('#editedContent').val()
                                }
                            }).done(function (response) {
                                Swal.fire('FAQ Edited!', '', 'success').then(() => {
                                    location.reload(); // Reload page after editing FAQ
                                });
                            }).fail(function () {
                                Swal.fire('Oops...', 'Failed to edit FAQ', 'error');
                            });
                        }
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log('Error:', errorThrown);
                }
            });
        });

        // Function to handle deleting FAQ
        $('.delete-faq-btn').click(function () {
            var freq_id = $(this).data('id');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // AJAX request to delete FAQ
                    $.ajax({
                        url: 'function/delete_faq.php?id=' + freq_id,
                        type: 'POST'
                    }).done(function (response) {
                        Swal.fire('Deleted!', 'Your FAQ has been deleted.', 'success').then(() => {
                            location.reload(); // Reload page after deleting FAQ
                        });
                    }).fail(function (reload) {
                        Swal.fire('Oops...', 'Failed to delete FAQ', 'error');
                    });
                }
            });
        });
    </script>
</body>