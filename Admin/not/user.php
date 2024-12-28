<?php
session_start();
include('Includes2/header.php');
include('Includes2/navbar.php');

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add User
if (isset($_POST['add_user_btn'])) {
    $username = $_POST['Username'];
    $phone = $_POST['Phone'];
    $email = $_POST['Email'];
    $password = $_POST['Password'];

    // Using prepared statements to prevent SQL injection
    $insert_user_query = "INSERT INTO user (`Username`, `Phone`, `Email`, `Password`)
                          VALUES (?, ?, ?, ?)";

    $stmt_add_user = mysqli_prepare($connect, $insert_user_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt_add_user, "ssss", $username, $phone, $email, $password);

    // Execute the statement and handle errors
    if (mysqli_stmt_execute($stmt_add_user)) {
        $_SESSION['success'] = "User added successfully!";
    } else {
        $_SESSION['error'] = "Error adding user: " . mysqli_stmt_error($stmt_add_user);
    }

    // Close the statement
    mysqli_stmt_close($stmt_add_user);
}

// Display Users
$query = "SELECT * FROM user";
$query_run = mysqli_query($connect, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="tables.css">
</head>

<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- ... ( existing navbar content) ... -->
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">User Details</h1>

                    <!-- Add User Form -->
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addUserModal">Add User</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Info.Users</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                if ($query_run) {
                                    if (mysqli_num_rows($query_run) > 0) {
                                        echo '<table id="customers">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>USER ID</th>';
                                        echo '<th>USER NAME</th>';
                                        echo '<th>PHONE</th>';
                                        echo '<th>EMAIL</th>';
                                        echo '<th>PASSWORD</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row = mysqli_fetch_assoc($query_run)) {
                                            echo '<tr>';
                                            echo '<td>' . $row['User_Id'] . '</td>';
                                            echo '<td>' . $row['Username'] . '</td>';
                                            echo '<td>' . $row['Phone'] . '</td>';
                                            echo '<td>' . $row['Email'] . '</td>';
                                            echo '<td>' . $row['Password'] . '</td>';

                                            echo '<td>
                                                    <form action="user_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row['User_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="delete_user.php" method="post">
                                                        <input type="hidden" name="delete_id" value="' . $row['User_Id'] . '">
                                                        <button type="submit" name="delete_btn" class="btn btn-danger">DELETE</button>
                                                    </form>
                                                </td>';
                                            echo '</tr>';
                                        }

                                        echo '</tbody>';
                                        echo '</table>';
                                    } else {
                                        echo 'No records found';
                                    }
                                } else {
                                    echo 'Error in the query: ' . mysqli_error($connect);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add User Modal -->
                <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add User</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Add User Form -->
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="Username">Username:</label>
                                        <input type="text" class="form-control" id="Username" name="Username" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Phone">Phone:</label>
                                        <input type="text" class="form-control" id="Phone" name="Phone" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Email">Email:</label>
                                        <input type="email" class="form-control" id="Email" name="Email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Password">Password:</label>
                                        <input type="password" class="form-control" id="Password" name="Password" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary" name="add_user_btn">Add User</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scroll to Top Button-->
                <a class="scroll-to-top rounded" href="#page-top">
                    <i class="fas fa-angle-up"></i>
                </a>

                <!-- Logout Modal-->
                <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                <a class="btn btn-primary" href="login.html">Logout</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bootstrap core JavaScript-->
                <script src="vendor/jquery/jquery.min.js"></script>
                <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                <script src="vendor/datatables/jquery.dataTables.min.js"></script>
                <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
                <script src="js/demo/datatables-demo.js"></script>
                <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
                <script src="js/sb-admin-2.min.js"></script>
            </div>
        </div>
    </div>
</body>

</html>

<?php
include('Includes2/footer.php');
mysqli_close($connect);
?>
