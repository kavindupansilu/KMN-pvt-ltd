<?php
// Start the session to manage user sessions
session_start();

// Include header and navbar files
include('Includes2/header.php');
include('Includes2/navbar.php');

// Establish database connection
$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

// Check if connection is successful
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Supplier
if (isset($_POST['add_supplier_btn'])) {
    // Retrieve form data
    $supplierName = $_POST['Supplier_Name'];
    $brand = implode(",", $_POST['Brand']);
    $contact = $_POST['Contact'];
    $manager_id = isset($_POST['Manager_Id']) ? $_POST['Manager_Id'] : null;
    $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null; // Fetch User_Id from session

    // Validate Supplier Name (letters and spaces only)
    if (!preg_match("/^[a-zA-Z ]+$/", $supplierName)) {
        $_SESSION['error'] = "Invalid characters in Supplier Name. Only letters and spaces are allowed.";
        header("Location: supplier.php");
        exit();
    }

    // Validate Contact
    $contactPattern1 = "/^07[01245678][0-9]{7}$/"; // Starts with 07 and 2nd digit is 0,1,2,4,5,6,7,8
    $contactPattern2 = "/^03[1237][0-9]{7}$/";     // Starts with 03 and 2nd digit is 1,2,3,7

    if (!preg_match($contactPattern1, $contact) && !preg_match($contactPattern2, $contact)) {
        $_SESSION['error'] = "Invalid Contact format. Please follow the specified format.";
        header("Location: supplier.php");
        exit();
    }

    // Using prepared statements to prevent SQL injection
    $insert_supplier_query = "INSERT INTO supplier (`Supplier_Name`, `Brand`, `Contact`, `Manager_Id`, `User_Id`)
                              VALUES (?, ?, ?, ?, ?)";
    $stmt_add_supplier = mysqli_prepare($connect, $insert_supplier_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt_add_supplier, "sssss", $supplierName, $brand, $contact, $manager_id, $user_id);

    // Execute the statement and handle errors
    if (mysqli_stmt_execute($stmt_add_supplier)) {
        $_SESSION['success'] = "Supplier added successfully!";
        $lastSupplierId = mysqli_insert_id($connect); // Retrieve the last inserted Supplier_Id
        // Continue with your code...
    } else {
        $_SESSION['error'] = "Error adding supplier: " . mysqli_stmt_error($stmt_add_supplier);
    }

    // Close the statement
    mysqli_stmt_close($stmt_add_supplier);
}

// Display Suppliers
$query_supplier = "SELECT * FROM supplier";
$query_run_supplier = mysqli_query($connect, $query_supplier);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Include necessary stylesheets -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        /* Additional CSS styles */
        /* Data table design */
        #suppliers thead th {
            background-color: #9999ff;
            color: black;
        }
    </style>
</head>

<body id="page-top">
    <!-- Main body content -->
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <!-- Top navigation bar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <!-- Sidebar toggle button -->
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>
                    <!-- Logout button -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a href="home.php" class="btn btn-danger">Log Out</a>
                        </li>
                    </ul>
                </nav>

                <!-- Main container -->
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Supplier Details</h1>

                    <!-- Add Supplier Form -->
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addSupplierModal">Add Supplier</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Supplier Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <!-- Display suppliers in a table -->
                                <?php
                                if ($query_run_supplier) {
                                    if (mysqli_num_rows($query_run_supplier) > 0) {
                                        echo '<table id="suppliers" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>SUPPLIER ID</th>';
                                        echo '<th>SUPPLIER NAME</th>';
                                        echo '<th>BRAND</th>';
                                        echo '<th>CONTACT</th>';
                                        echo '<th>MANAGER ID</th>';
                                        echo '<th>USER_ID</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row_supplier = mysqli_fetch_assoc($query_run_supplier)) {
                                            echo '<tr>';
                                            echo '<td>' . $row_supplier['Supplier_Id'] . '</td>';
                                            echo '<td>' . $row_supplier['Supplier_Name'] . '</td>';
                                            echo '<td>' . implode(', ', explode(',', $row_supplier['Brand'])) . '</td>';
                                            echo '<td>' . $row_supplier['Contact'] . '</td>';
                                            echo '<td>' . $row_supplier['Manager_Id'] . '</td>';
                                            echo '<td>' . $row_supplier['User_Id'] . '</td>';

                                            // Edit and delete buttons
                                            echo '<td>
                                                    <form action="supplier_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row_supplier['Supplier_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="supplier_delete.php" method="post">
                                                        <input type="hidden" name="delete_id" value="' . $row_supplier['Supplier_Id'] . '">
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

                <!-- Add Supplier Modal -->
                <div class="modal fade" id="addSupplierModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add Supplier</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Add Supplier Form -->
                                <form action="" method="post">
                                    <!-- Supplier Name -->
                                    <div class="form-group">
                                        <label for="Supplier_Name">Supplier Name:</label>
                                        <input type="text" class="form-control" id="Supplier_Name" name="Supplier_Name" required>
                                    </div>
                                    <!-- Brand -->
                                    <!-- Select more than one -->
                                    <!-- Use Ctrl to it -->
                                    <div class="form-group">
                                        <label for="Brand">Brand:</label>
                                        <select class="form-control" id="Brand" name="Brand[]" multiple required>
                                            <option value="Honda">Honda</option>
                                            <option value="Toyota">Toyota</option>
                                            <option value="Suzuki">Suzuki</option>
                                            <option value="CEAT">CEAT</option>
                                        </select>
                                    </div>
                                    <!-- Contact -->
                                    <div class="form-group">
                                        <label for="Contact">Contact:</label>
                                        <input type="text" class="form-control" id="Contact" name="Contact" pattern="^(07[01245678][0-9]{7}|03[1237][0-9]{7})$" required>
                                        <small class="form-text text-muted">Valid formats: 07xxxxxxxx or 03xxxxxxxx </small>
                                    </div>
                                    <!-- Manager Id -->
                                    <div class="form-group">
                                        <label for="Manager_Id">Manager Id:</label>
                                        <input type="text" class="form-control" id="Manager_Id" name="Manager_Id">
                                    </div>
                                    <!-- User ID (readonly) -->
                                    <div class="form-group">
                                        <label for="User_Id">User ID:</label>
                                        <input type="text" class="form-control" id="User_Id" name="User_Id" readonly value="<?php echo isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : ''; ?>">
                                    </div>
                                    <!-- Add Supplier button -->
                                    <button type="submit" class="btn btn-primary" name="add_supplier_btn">Add Supplier</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scroll to Top Button-->
                <a class="scroll-to-top rounded" href="#page-top">
                    <i class="fas fa-angle-up"></i>
                </a>

                <!-- Include necessary scripts -->
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
// Include footer file
include('Includes2/footer.php');

// Close database connection
mysqli_close($connect);
?>
