<?php
session_start();
include('Includes2/header.php');
include('Includes2/navbar.php');

require "dbh.inc.php";
// $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Item-Supplier Relationship
if (isset($_POST['add_item_supplier_btn'])) {
    $item_id = $_POST['Item_Id'];
    $supplier_id = $_POST['Supplier_Id'];
    // Fetch User_Id from session
    $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

    // Using prepared statements to prevent SQL injection
    $insert_item_supplier_query = "INSERT INTO item_supplier (`Item_Id`, `Supplier_Id`, `User_Id`)
                                    VALUES (?, ?, ?)";

    $stmt_add_item_supplier = mysqli_prepare($connect, $insert_item_supplier_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt_add_item_supplier, "iis", $item_id, $supplier_id, $user_id);

    // Execute the statement and handle errors
    if (mysqli_stmt_execute($stmt_add_item_supplier)) {
        $_SESSION['success'] = "Item-Supplier relationship added successfully!";
    } else {
        $_SESSION['error'] = "Error adding Item-Supplier relationship: " . mysqli_stmt_error($stmt_add_item_supplier);
    }

    // Close the statement
    mysqli_stmt_close($stmt_add_item_supplier);
}

// Display Item-Supplier Relationships
$query_item_supplier = "SELECT * FROM item_supplier";
$query_run_item_supplier = mysqli_query($connect, $query_item_supplier);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        #item_supplier thead th {
            background-color: #9999ff;
            color: black;
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
                            </div>
                        </li>
                    </ul>
                </nav>
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Item-Supplier Relationship Details</h1>

                    <!-- Add Item-Supplier Relationship Form -->
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addItemSupplierModal">Add Item-Supplier Relationship</a>
                    <br><br>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Item-Supplier Relationship Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                if ($query_run_item_supplier) {
                                    if (mysqli_num_rows($query_run_item_supplier) > 0) {
                                        echo '<table id="item_supplier" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>ID</th>';
                                        echo '<th>Item ID</th>';
                                        echo '<th>Supplier ID</th>';
                                        echo '<th>USER_ID</th>';
                                        //echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row_item_supplier = mysqli_fetch_assoc($query_run_item_supplier)) {
                                            echo '<tr>';
                                            echo '<td>' . $row_item_supplier['Id'] . '</td>';
                                            echo '<td>' . $row_item_supplier['Item_Id'] . '</td>';
                                            echo '<td>' . $row_item_supplier['Supplier_Id'] . '</td>';
                                            echo '<td>' . $row_item_supplier['User_Id'] . '</td>';
                                            // echo '<td>
                                            //         <form action="item_supplier_edit.php" method="post">
                                            //             <input type="hidden" name="edit_id" value="' . $row_item_supplier['Id'] . '">
                                            //             <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                            //         </form>
                                            //     </td>';

                                            echo '<td>
                                                    <form action="item_supplier_delete.php" method="post">
                                                        <input type="hidden" name="delete_id" value="' . $row_item_supplier['Id'] . '">
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

                <!-- Add Item-Supplier Relationship Modal -->
                <div class="modal fade" id="addItemSupplierModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add Item-Supplier Relationship</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <!-- Add Item-Supplier Relationship Form -->
                                <form action="#" method="post">
                                    <div class="form-group">
                                        <label for="Item_Id">Item ID:</label>
                                        <input type="text" class="form-control" id="Item_Id" name="Item_Id" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Supplier_Id">Supplier ID:</label>
                                        <input type="text" class="form-control" id="Supplier_Id" name="Supplier_Id" required>
                                    </div>
                                    <div class="form-group">
                                                <label for="User_Id">User ID:</label>
                                                <input type="text" class="form-control" id="User_Id" name="User_Id" readonly value="<?php echo isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : ''; ?>">
                                            </div>
                                    <button type="submit" class="btn btn-primary" name="add_item_supplier_btn">Add Item-Supplier Relationship</button>
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
            </div>
        </div>
    </div>
</body>

</html>

<?php
include('Includes2/footer.php');
mysqli_close($connect);
?>
