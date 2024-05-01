
<body>
<?php require_once('../function/sidebar.php');  ?>
<div class="container">
        <h2>ImportCSV</h2>
        <div class="row">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phon</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM users");
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                        }
                    }
                    ?>
                </tbody>

            </table>

        </div>
</div>
</body>