<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee - Problem</title>
    <link rel="icon" type="image/x-icon" href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.min.css" rel="stylesheet"> 

    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .table th, .table td {
            vertical-align: middle;
        }
        .table img {
            border-radius: 5px;
        }
        .table-hover tbody tr:hover {
            background-color: #f0f0f5; /* Subtle hover effect */
        }
        .btn-warning {
            background-color: #ffc107; /* More vibrant warning color */
            border-color: #ffc107;
        }
        .swal2-popup { /* Customize SweetAlert appearance */
            font-family: 'Arial', sans-serif;
        }
    </style>
</head>

<?php require_once('function/sidebar_employee.php');  ?>

<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="text-center mb-4">Problem List</h1> 
            </div>

            <div class="col-12">
                <table id="problemTable" class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Problem ID</th>
                            <th>Problem</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                            <th>Image</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>Electricity</td>
                            <td>Electricity is not working</td>
                            <td>2022-12-01</td>
                            <td>12:00:00</td>
                            <td>Not fixed</td>
                            <td><img src="https://avatars.githubusercontent.com/u/116146758?v=4" alt="Electricity" width="100"></td>
                            <td>
                                <a href="editProblem.php" class="btn btn-warning">Edit</a>
                                <a href="deleteProblem.php" class="btn btn-danger" onclick="return deleteProblem();">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td>2</td>
                            <td>Water</td>
                            <td>Water is not working</td>
                            <td>2022-12-02</td>
                            <td>13:00:00</td>
                            <td>Not fixed</td>
                            <td><img src="https://avatars.githubusercontent.com/u/46838817?v=4" alt="Water" width="100"></td>
                            <td>
                                <a href="editProblem.php" class="btn btn-warning">Edit</a>
                                <a href="deleteProblem.php" class="btn btn-danger" onclick="return deleteProblem();">Delete</a>
                            </td>
                        </tr>
                        <tr>
                            <td>3</td>
                            <td>Water</td>
                            <td>Water is not working Please Help I can't wash put my soap on my fucking face you motherfucker</td>
                            <td>2022-12-03</td>
                            <td>14:00:00</td>
                            <td>Not fixed</td>
                            <td><img src="https://scontent.xx.fbcdn.net/v/t1.15752-9/441291944_7932114376833722_8156975045150246158_n.png?_nc_cat=101&ccb=1-7&_nc_sid=5f2048&_nc_ohc=ehiSETrJEkMQ7kNvgHvc_Pk&_nc_ad=z-m&_nc_cid=0&_nc_ht=scontent.xx&oh=03_Q7cD1QGak78CuIMO2pcT5jztuMekXQmvcSG-T-8UmXYGHWyE1g&oe=6686B2E4" alt="Internet" width="100"></td>
                            <td>
                                <a href="editProblem.php" class="btn btn-warning">Edit</a>
                                <a href="deleteProblem.php" class="btn btn-danger" onclick="return deleteProblem();">Delete</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.27/dist/sweetalert2.all.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#problemTable').DataTable({
                "pagingType": "full_numbers"
            });
        });

        function deleteProblem(problemId) { // Pass the problem ID
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this problem!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, redirect to delete_problem.php with the ID
                    window.location.href = 'delete_problem.php?id=' + problemId; 
                }
            });
        }
    </script>
</body>
</html>
