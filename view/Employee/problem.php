<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee-Problem</title>
    <link rel="icon" type="image/x-icon" href="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png">
</head>
<?php require_once('function/sidebar_employee.php');  ?>
<body>

<div class="container">
    <div class="row">
        <div class="col-12">
            <h1>Problem</h1>
        </div>

        <div class="col-12">
            <table class="table">
                <thead>
                    <tr>
                        <th>Problem ID</th>
                        <th>Problem</th>
                        <th>Problem Description</th>
                        <th>Problem Date</th>
                        <th>Problem Time</th>
                        <th>Problem Status</th>
                        <th>Problem Image</th>
                        <th>Problem Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Electricity</td>
                        <td>Electricity is not working</td>
                        <td>2022-12-12</td>
                        <td>12:00:00</td>
                        <td>Not Fixed</td>
                        <td><img src="https://wehome.co.th/wp-content/uploads/2023/01/logo-WeHome-BUILDER-788x624.png" alt="Problem Image" width="100px" height="100px"></td>
                        <td>
                            <a href="edit_problem.php" class="btn btn-primary">Edit</a>
                            <a href="delete_problem.php" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>

    

    </div>




<script src="https://fastly.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        $('#example').DataTable();
    });

    function deleteProblem() {
        return confirm('Are you sure you want to delete this problem?');
    }

</script>

</body>
</html>