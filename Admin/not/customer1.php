<?php
session_start();
include('Includes2/header.php');
include('Includes2/navbar.php');

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Customer
if (isset($_POST['add_customer_btn'])) {
    //$cust_id = $_POST['NIC'];
    $first_name = $_POST['FirstName'];
    $last_name = $_POST['LastName'];
    $address = $_POST['Address'];
    $phone = $_POST['Phone'];
    $email = $_POST['Email'];
    $referral = $_POST['Referral'];
    $user_id = $_POST['User_id'];

    // Using prepared statements to prevent SQL injection
    $insert_customer_query = "INSERT INTO customer (`FirstName`, `LastName`, `Address`, `Phone`, `Email`, `Referral`, `User_id`)
                              VALUES ( ?, ?, ?, ?, ?, ?, ?)";

    $stmt_add_customer = mysqli_prepare($connect, $insert_customer_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt_add_customer, "ssssssi",  $first_name, $last_name, $address, $phone, $email, $referral, $user_id);

    // Execute the statement and handle errors
    if (mysqli_stmt_execute($stmt_add_customer)) {
        $_SESSION['success'] = "Customer added successfully!";
    } else {
        $_SESSION['error'] = "Error adding customer: " . mysqli_stmt_error($stmt_add_customer);
    }

    // Close the statement
    mysqli_stmt_close($stmt_add_customer);
}

// Display Customers
$query_customer = "SELECT * FROM customer";
$query_run_customer = mysqli_query($connect, $query_customer);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        #customers thead th {
            background-color:#9999ff; 
            color: black;
        }
    </style>    
</head>

<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- ... ( existing navbar content) ... -->
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Customer Details</h1>

                    <!-- Add Customer Form -->
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addCustomerModal">Add Customer</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                if ($query_run_customer) {
                                    if (mysqli_num_rows($query_run_customer) > 0) {
                                        echo '<table id="customers" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>NIC</th>';
                                        echo '<th>FIRST NAME</th>';
                                        echo '<th>LAST NAME</th>';
                                        echo '<th>ADDRESS</th>';
                                        echo '<th>PHONE</th>';
                                        echo '<th>EMAIL</th>';
                                        echo '<th>REFERRAL</th>';
                                        echo '<th>USER ID</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row_customer = mysqli_fetch_assoc($query_run_customer)) {
                                            echo '<tr>';
                                            echo '<td>' . $row_customer['Cust_Id'] . '</td>';
                                            echo '<td>' . $row_customer['FirstName'] . '</td>';
                                            echo '<td>' . $row_customer['LastName'] . '</td>';
                                            echo '<td>' . $row_customer['Address'] . '</td>';
                                            echo '<td>' . $row_customer['Phone'] . '</td>';
                                            echo '<td>' . $row_customer['Email'] . '</td>';
                                            echo '<td>' . $row_customer['Referral'] . '</td>';
                                            echo '<td>' . $row_customer['User_Id'] . '</td>';

                                            echo '<td>
                                                    <form action="customer_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row_customer['Cust_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="customer_delete.php" method="post">
                                                        <input type="hidden" name="delete_id" value="' . $row_customer['Cust_Id'] . '">
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

                <!-- Add Customer Modal -->
                <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add Customer</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Add Customer Form -->
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="cust_id">NIC:</label>
                                        <input type="text" class="form-control" id="cust_id" name="cust_id" required>
                                    </div>


                                    <div class="form-group">
                                        <label for="FirstName">First Name:</label>
                                        <input type="text" class="form-control" id="FirstName" name="FirstName" required>
                                    </div>


                                    <div class="form-group">
                                        <label for="LastName">Last Name:</label>
                                        <input type="text" class="form-control" id="LastName" name="LastName" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Address">Address:</label>
                                        <input type="text" class="form-control" id="Address" name="Address" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Phone">Phone:</label>
                                        <input type="text" class="form-control" id="Phone" name="Phone" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Email">Email:</label>
                                        <input type="email" class="form-control" id="Email" name="Email">
                                    </div>
                                    <div class="form-group">
                                        <label for="Referral">Referral:</label>
                                        <input type="text" class="form-control" id="Referral" name="Referral">
                                    </div>
                                    <div class="form-group">
                                        <label for="User_id">User ID:</label>
                                        <input type="text" class="form-control" id="User_id" name="User_id" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary" name="add_customer_btn">Add Customer</button>
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
