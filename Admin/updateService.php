<?php
session_start();
include('Includes2/header.php');
include('Includes2/mechanicnavbar.php');

require "dbh.inc.php";
// $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Service
if (isset($_POST['add_service_btn'])) {
    $service_date = $_POST['Service_Date'];
    // Format the date if needed
    $formatted_service_date = date('Y-m-d', strtotime($service_date));

    $type_of_service = $_POST['Type_of_Service'];
    $parts_used = $_POST['Parts_Used'];
    $start_time = $_POST['S_Time'];
    $end_time = $_POST['E_Time'];
    $total_cost = $_POST['Total_Cost'];

    // Using prepared statements to prevent SQL injection
    $insert_service_query = "INSERT INTO service (`Service_Date`, `Type_of_Service`, `Parts_Used`,`S_Time`, `E_Time`, `Total_Cost`)
                              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt_add_service = mysqli_prepare($connect, $insert_service_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt_add_service, "sssiis", $formatted_service_date, $type_of_service, $parts_used, $start_time, $end_time, $total_cost);

    // Execute the statement and handle errors
    if (mysqli_stmt_execute($stmt_add_service)) {
        $_SESSION['success'] = "Service added successfully!";
    } else {
        $_SESSION['error'] = "Error adding service: " . mysqli_stmt_error($stmt_add_service);
    }

    // Close the statement
    mysqli_stmt_close($stmt_add_service);
}

// Display Services
$query_service = "SELECT * FROM service";
$query_run_service = mysqli_query($connect, $query_service);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        #services thead th {
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
                        <a href="logout.php" class="btn btn-danger">Log Out</a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Service Details</h1>

                    <!-- Add Service Form -->
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addServiceModal">Add Service</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Service Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                if ($query_run_service) {
                                    if (mysqli_num_rows($query_run_service) > 0) {
                                        echo '<table id="services" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>SERVICE ID</th>';
                                        echo '<th>SERVICE DATE</th>';
                                        echo '<th>TYPE OF SERVICE</th>';
                                        echo '<th>PARTS USED</th>';
                                        echo '<th>START TIME</th>';
                                        echo '<th>END TIME</th>';
                                        echo '<th>TOTAL COST</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row_service = mysqli_fetch_assoc($query_run_service)) {
                                            echo '<tr>';
                                            echo '<td>' . $row_service['Service_Id'] . '</td>';
                                            echo '<td>' . $row_service['Service_Date'] . '</td>';
                                            echo '<td>' . $row_service['Type_of_Service'] . '</td>';
                                            echo '<td>' . $row_service['Parts_Used'] . '</td>';
                                            echo '<td>' . $row_service['S_Time'] . '</td>';
                                            echo '<td>' . $row_service['E_Time'] . '</td>';
                                            echo '<td>' . $row_service['Total_Cost'] . '</td>';

                                            echo '<td>
                                                    <form action="service_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row_service['Service_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="service_delete.php" method="post">
                                                        <input type="hidden" name="delete_id" value="' . $row_service['Service_Id'] . '">
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

                <!-- Add Service Modal -->
                <div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add Service</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Add Service Form -->
                                <form action="#" method="post">
                                    <div class="form-group">
                                        <label for="Service_Date">Service Date:</label>
                                        <input type="date" class="form-control" id="Service_Date" name="Service_Date" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="Type_of_service">Type of Service:</label>
                                        <input type="text" class="form-control" id="Type_of_service" name="Type_of_service" required>
                                    </div>


                                    <div class="form-group">
                                        <label for="Parts_used">Parts Used:</label>
                                        <input type="text" class="form-control" id="Parts_used" name="Parts_used" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="S_Time">Start Time:</label>
                                        <input type="text" class="form-control" id="S_Time" name="S_Time" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="E_Time">End Time:</label>
                                        <input type="text" class="form-control" id="E_Time" name="E_Time" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Total_Cost">Total Cost:</label>
                                        <input type="text" class="form-control" id="Total_Cost" name="Total_Cost" required>
                                    </div>

                                    <button type="submit" class="btn btn-primary" name="add_service_btn">Add Service</button>
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
