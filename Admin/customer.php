<?php
session_start();
include('Includes2/header.php');
include('Includes2/navbar.php');
require "dbh.inc.php";
//$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Customer
if (isset($_POST['add_btn'])) {
    $cust_id = $_POST['Cust_Id'];// Customer NIC
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $referral = $_POST['referral'];
    $manager_id = $_POST['Manager_Id'];
    // Fetch User_Id from session
    $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;


    // Insert data into the 'customers' table
    $insert_customer_query = "INSERT INTO customer (Cust_Id, First_name, Last_name, Address, Phone, Referral, `Manager_Id`, User_Id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_add_customer = mysqli_prepare($connect, $insert_customer_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt_add_customer, "ssssssss", $cust_id, $first_name, $last_name, $address, $phone, $referral, $manager_id, $user_id);

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
        #customer thead th {
            background-color: #9999ff;
            color: black;
        }

        .error-popup {
            color: red;
        }
    </style>

</head>

<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a href="home.php" class="btn btn-danger">Log Out</a>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Customer Details</h1>
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
                                        echo '<table id="customer" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>NIC</th>';
                                        echo '<th>FIRST NAME</th>';
                                        echo '<th>LAST NAME</th>';
                                        echo '<th>ADDRESS</th>';
                                        echo '<th>PHONE</th>';
                                        echo '<th>REFERRAL</th>';
                                        echo '<th>MANAGER_ID</th>';
                                        echo '<th>USER_ID</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row = mysqli_fetch_assoc($query_run_customer)) {
                                            echo '<tr>';
                                            echo '<td>' . $row['Cust_Id'] . '</td>';
                                            echo '<td>' . $row['first_name'] . '</td>';
                                            echo '<td>' . $row['last_name'] . '</td>';
                                            echo '<td>' . $row['address'] . '</td>';
                                            echo '<td>' . $row['phone'] . '</td>';
                                            echo '<td>' . $row['referral'] . '</td>';
                                            echo '<td>' . $row['Manager_Id'] . '</td>';
                                            echo '<td>' . $row['User_Id'] . '</td>';
                                            echo '<td>
                                                    <form action="customer_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row['Cust_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="customer_delete.php" method="post">
                                                        <input type="hidden" name="delete_id" value="' . $row['Cust_Id'] . '">
                                                        <button type="submit" name="delete_btn" class="btn btn-danger">DELETE</button>
                                                    </form>
                                                </td>';
                                            echo '</tr>';

                                            
                                        }

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
                <!-- /.container-fluid -->

                <!-- Add Customer Modal -->
                <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add Customer</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Add Customer Form -->
                                <form action="" method="post" onsubmit="return validateForm()">
                                    <div class="form-group">
                                        <label for="Cust_Id">NIC:</label>
                                        <input type="text" class="form-control" id='Cust_Id' name='Cust_Id' pattern='[7-9][0-9]{8}[Vv]|[2-9][0-9]{11}' title="[7-9][0-9]{8}[Vv]OR[2-9][0-9]{11}" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="first_name">First Name:</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" pattern="[A-Za-z]+" title="Only letters are allowed" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name">Last Name:</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" pattern="[A-Za-z]+" title="Only letters are allowed" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="address">Address:</label>
                                        <input type="text" class="form-control" id="address" name="address">
                                    </div>
                                    <div class="form-group">
                                        <label for="phone">Phone:</label>
                                        <input type="text" class="form-control" id="phone" name="phone" pattern='^0(7[0124578])[0-9]{7}$' title="07(0124578)(0-9)" required>
                                    </div>
                                    <div class="form-group">
                                    <!-- Refferal not needed -->
                                        <label for="referral">Referral:</label>
                                        <input type="text" class="form-control" id="referral" name="referral" pattern="[A-Za-z]+" title="Only letters are allowed">
                                    </div>
                                    <div class="form-group">
                                        
                                    <!-- Provide a Manager ID -->
                                        <label for="Manager_Id">Manager Id:</label>
                                        <input type="text" class="form-control" id="Manager_Id" name="Manager_Id" >
                                    </div>

                                        <div class="form-group">
                                            
                                    <!--Take only active User ID -->
                                                <label for="User_Id">User ID:</label>
                                                <input type="text" class="form-control" id="User_Id" name="User_Id" readonly value="<?php echo isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : ''; ?>">
                                            </div>
                                    <button type="submit" class="btn btn-primary" name="add_btn">Add Customer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scroll to Top Button-->
                <a class="scroll-to-top rounded" href="#page-top">
                    <i class="fas fa-angle-up"></i>
                </a>

        
                <!-- Bootstrap core JavaScript-->
                <script src="vendor/jquery/jquery.min.js"></script>
                <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                <script src="vendor/datatables/jquery.dataTables.min.js"></script>
                <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
                <script src="js/demo/datatables-demo.js"></script>
                <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
                <script src="js/sb-admin-2.min.js"></script>
</body>

</html>

<?php
include('Includes2/footer.php');
mysqli_close($connect);
?>
