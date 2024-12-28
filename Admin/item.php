<?php
session_start();
include('Includes2/header.php');
include('Includes2/navbar.php');

require "dbh.inc.php";
// $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Item
if (isset($_POST['add_item_btn'])) {
    $item_cost = $_POST['Item_Cost'];

// Validate Item Cost
if (!is_numeric($item_cost) || $item_cost < 0.01) {
    $_SESSION['error'] = "Item Cost must be a positive number with up to two decimal places.";
}
    // Validate Quantity and InStock
    $quantity = $_POST['Quantity'];
    $instock = $_POST['InStock'];

    if (!ctype_digit($quantity) || !ctype_digit($instock) || $quantity <= 0 || $instock <= 0) {
        $_SESSION['error'] = "Quantity and InStock must be positive integers.";
    } elseif ($instock > $quantity) {
        $_SESSION['error'] = "InStock cannot be greater than Quantity.";
    } else {
        // Proceed with adding the item
        $item_name = $_POST['Item_Name'];
        $brand = $_POST['Brand'];
        $mechanic_id = $_POST['Mechanic_Id'];
    // Fetch User_Id from session
    $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

    // Using prepared statements to prevent SQL injection
    $insert_item_query = "INSERT INTO item (`Item_Name`, `Brand`, `Quantity`, `InStock`, `Item_Cost`, `Mechanic_Id`, `User_Id`)
                          VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt_add_item = mysqli_prepare($connect, $insert_item_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt_add_item, "ssiidss", $item_name, $brand, $quantity, $instock, $item_cost, $mechanic_id, $user_id);

    // Execute the statement and handle errors
    if (mysqli_stmt_execute($stmt_add_item)) {
        $_SESSION['success'] = "Item added successfully!";

        // Retrieve the last inserted Item_Id
        $lastItemId = mysqli_insert_id($connect);

        // Continue with your code...
    } else {
        $_SESSION['error'] = "Error adding item: " . mysqli_stmt_error($stmt_add_item);
    }
    // Calculate total instock
    $queryTotalInstock = "SELECT SUM(InStock) AS total FROM item";
    $resultTotalInstock = mysqli_query($connect, $queryTotalInstock);
    $rowTotalInstock = mysqli_fetch_assoc($resultTotalInstock);
    $totalInstock = $rowTotalInstock['total'];

    // Pass total instock to session
    $_SESSION['total_instock'] = $totalInstock;
    // Close the statement
    mysqli_stmt_close($stmt_add_item);
    }
}

// Display Items
$query_item = "SELECT * FROM item";
$query_run_item = mysqli_query($connect, $query_item);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        #items thead th {
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
                    <h1 class="h3 mb-2 text-gray-800">Item Details</h1>

                    <!-- Add Item Form -->
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addItemModal">Add Item</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Item Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                if ($query_run_item) {
                                    if (mysqli_num_rows($query_run_item) > 0) {
                                        echo '<table id="items" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>Item ID</th>';
                                        echo '<th>ITEM NAME</th>';
                                        echo '<th>BRAND</th>';
                                        echo '<th>QUANTITY</th>';
                                        echo '<th>INSTOCK</th>';
                                        echo '<th>ITEM COST</th>';
                                        echo '<th>MECHANIC ID</th>';
                                        echo '<th>USER_ID</th>';
                                         echo '<th>EDIT</th>';
                                         echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row_item = mysqli_fetch_assoc($query_run_item)) {
                                            echo '<tr>';
                                             echo '<td>' . $row_item['Item_Id'] . '</td>';
                                            echo '<td>' . $row_item['Item_Name'] . '</td>';
                                            echo '<td>' . $row_item['Brand'] . '</td>';
                                            echo '<td>' . $row_item['Quantity'] . '</td>';
                                            echo '<td>' . $row_item['InStock'] . '</td>';
                                            echo '<td>' . $row_item['Item_Cost'] . '</td>';
                                            echo '<td>' . $row_item['Mechanic_Id'] . '</td>';
                                            echo '<td>' . $row_item['User_Id'] . '</td>';

                                             echo '<td>
                                                    <form action="item_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row_item['Item_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                             echo '<td>
                                                    <form action="item_delete.php" method="post">
                                                         <input type="hidden" name="delete_id" value="' . $row_item['Item_Id'] . '">
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

                <!-- Add Item Modal -->
                <div class="modal fade" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add Item</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Add Item Form -->
                                <form action="" method="post">
                                <div class="form-group">
                                        <label for="Item_Name">Item Name:</label>
                                        <input type="text" class="form-control" id="Item_Name" name="Item_Name" required>
                                    </div> 
                                    <div class="form-group">
                                    <label for="Brand">Brand:</label>
                                            <select id="Brand" name="Brand" required>
                                            <option value="">Select Item Brand</option>
                                            <option value="Honda">Honda</option>
                                            <option value="Toyota">Toyota</option>
                                            <option value="Suzuki">Suzuki</option>
                                            <option value="CEAT">CEAT</option>
                                            <option value="Mobil">Mobil</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="Quantity">Quantity:</label>
                                        <input type="text" class="form-control" id="Quantity" name="Quantity" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="InStock">InStock:</label>
                                        <input type="text" class="form-control" id="InStock" name="InStock" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Item_Cost">Item Cost:</label>
                                        <input type="number" step="0.01" class="form-control" id="Item_Cost" name="Item_Cost" required min="0.01">
                                    </div>
                                    <div class="form-group">
                                        <label for="Mechanic_Id">Mechanic Id:</label>
                                        <input type="text" class="form-control" id="Mechanic_Id" name="Mechanic_Id" >
                                    </div>
                                    <div class="form-group">
                                                <label for="User_Id">User ID:</label>
                                                <input type="text" class="form-control" id="User_Id" name="User_Id" readonly value="<?php echo isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : ''; ?>">
                                            </div>

                                    <button type="submit" class="btn btn-primary" name="add_item_btn">Add Item</button>
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
