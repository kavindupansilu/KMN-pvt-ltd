<?php
session_start();
include('Includes2/header.php');
include('Includes2/navbar.php');

require "database.php";
// $conn = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Daily Sales
if (isset($_POST['add_btn'])) {
    $sdate = $_POST['Sales_Date'];
    $psales = isset($_POST['Parts_Sales']) && is_array($_POST['Parts_Sales']) ? implode(",", $_POST['Parts_Sales']) : '';
    $srevenue = 0;  // Initialize to 0
    $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

    $insert_query = "INSERT INTO sales (`Sales_Date`, `Parts_Sales`, `Service_Revenue`, `User_Id`)
                     VALUES (?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($stmt, "ssss", $sdate, $psales, $srevenue, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        // Check if service date matches the sales date
        $totalServiceRevenueQuery = "SELECT SUM(Total_Cost) as totalRevenue FROM service WHERE Service_Date = ?";
        $stmtTotalRevenue = mysqli_prepare($conn, $totalServiceRevenueQuery);
        mysqli_stmt_bind_param($stmtTotalRevenue, "s", $sdate);
        mysqli_stmt_execute($stmtTotalRevenue);
        $resultTotalRevenue = mysqli_stmt_get_result($stmtTotalRevenue);

        if ($rowTotalRevenue = mysqli_fetch_assoc($resultTotalRevenue)) {
            $totalServiceRevenue = $rowTotalRevenue['totalRevenue'];

            // Update Service_Revenue field in the sales table
            $updateSalesQuery = "UPDATE sales SET Service_Revenue = ? WHERE Sales_Date = ?";
            $stmtUpdateSales = mysqli_prepare($conn, $updateSalesQuery);
            mysqli_stmt_bind_param($stmtUpdateSales, "ss", $totalServiceRevenue, $sdate);
            mysqli_stmt_execute($stmtUpdateSales);
            mysqli_stmt_close($stmtUpdateSales);
        } else {
            $totalServiceRevenue = 0; // Set default value if no records found
        }

        mysqli_stmt_close($stmtTotalRevenue);
    } else {
        echo "Error adding daily sales: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
} else {
    $totalServiceRevenue = 0; // Initialize to 0 if the condition is not met
}

// Display Daily Sales
$query = "SELECT * FROM sales";
$query_run = mysqli_query($conn, $query);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        #sales thead th {
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
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Daily Sales Details</h1>
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addDailySalesModal">Add Daily Sales</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Daily Sales Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                if ($query_run) {
                                    if (mysqli_num_rows($query_run) > 0) {
                                        echo '<table id="sales" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>SALES ID</th>';
                                        echo '<th>SALES DATE</th>';
                                        echo '<th>PARTS SALES</th>';
                                        echo '<th>SERVICE REVENUE</th>';
                                        echo '<th>USER ID</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row = mysqli_fetch_assoc($query_run)) {
                                            echo '<tr>';
                                            echo '<td>' . $row['Sales_Id'] . '</td>';
                                            echo '<td>' . $row['Sales_Date'] . '</td>';

                                            // Explode the comma-separated string into an array
                                            $partsSalesArray = explode(",", $row['Parts_Sales']);

                                            // Display each part separately
                                            echo '<td>' . implode('<br>', $partsSalesArray) . '</td>';

                                            echo '<td>' . $row['Service_Revenue'] . '</td>';
                                            echo '<td>' . $row['User_Id'] . '</td>';

                                            echo '<td>
                                                    <form action="dailysales_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row['Sales_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="dailysales_delete.php" method="post">
                                                        <input type="hidden" name="delete_id" value="' . $row['Sales_Id'] . '">
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
                                    echo 'Error in the query: ' . mysqli_error($conn);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Daily Sales Modal -->
                <div class="modal fade" id="addDailySalesModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add Daily Sales</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                               <!-- Add Daily Sales Form -->
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="Sales_Date">Sales Date:</label>
                                        <input type="date" class="form-control" id="Sales_Date" name="Sales_Date" required value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d'); ?>" max="<?php echo date('Y-m-d'); ?>">
                                    </div>

                                    <div class="form-group">
                                        <label for="Parts_Sales">Parts Sales:</label><br>

                                        <?php
                                        // Fetch item names from the Item table
                                        $itemQuery = "SELECT Item_Name FROM item";
                                        $itemResult = mysqli_query($conn, $itemQuery);

                                        if ($itemResult && mysqli_num_rows($itemResult) > 0) {
                                            while ($item = mysqli_fetch_assoc($itemResult)) {
                                                echo '<input type="checkbox" id="Parts_Sales_' . $item['Item_Name'] . '" name="Parts_Sales[]" value="' . $item['Item_Name'] . '">';
                                                echo '<label for="Parts_Sales_' . $item['Item_Name'] . '"> ' . $item['Item_Name'] . '</label><br>';
                                            }
                                        }
                                        ?>

                                    </div>

                                    
                                    <div class="form-group">
                                        <label for="Service_Revenue">Service Revenue:</label>
                                        <input type="text" class="form-control" id="Service_Revenue" name="Service_Revenue" readonly value="<?php echo $totalServiceRevenue ?>">
                                    </div>


                                    <div class="form-group">
                                        <label for="User_Id">User ID:</label>
                                        <input type="text" class="form-control" id="User_Id" name="User_Id" readonly value="<?php echo isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : ''; ?>">
                                    </div>

                                    <button type="submit" class="btn btn-primary" name="add_btn">Add Daily Sales</button>
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
mysqli_close($conn);
?>
