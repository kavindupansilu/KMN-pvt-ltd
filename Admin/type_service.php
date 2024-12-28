<?php
session_start();
include('Includes2/header.php');
include('Includes2/navbar.php');

require "dbh.inc.php";

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Service
if (isset($_POST['add_service_btn'])) {
    
    $type_of_service = $_POST['Type_of_Service'];
    $service_charge = $_POST['Service_Charge'];


    $insert_service_query = "INSERT INTO service_type (`Type_of_Service`, `Service_Charge`)
                          VALUES (?, ?)";

    $stmt_add_service = mysqli_prepare($connect, $insert_service_query);
    mysqli_stmt_bind_param($stmt_add_service, "sd",$type_of_service, $service_charge);

    if (mysqli_stmt_execute($stmt_add_service)) {
        $_SESSION['success'] = "Service added successfully!";
    } else {
        $_SESSION['error'] = "Error adding service: " . mysqli_stmt_error($stmt_add_service);
    }

    mysqli_stmt_close($stmt_add_service);
}

// Display Services
$query_service = "SELECT * FROM service_type";
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
                            <a href="home.php" class="btn btn-danger">Log Out</a>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Service Type</h1>

                    <!-- Add Service Form -->
                    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#addServiceModal">Add Service Type</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Service Type</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                // Displaying service Type
                                if ($query_run_service) {
                                    if (mysqli_num_rows($query_run_service) > 0) {
                                        echo '<table id="services" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>T_SERVICE_ID</th>';
                                        echo '<th>TYPE_OF_SERVICE</th>';
                                        echo '<th>SERVICE CHARGE</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row_service = mysqli_fetch_assoc($query_run_service)) {
                                            // Displaying each row of service Type data
                                            echo '<tr>';
                                            echo '<td>' . $row_service['T_Service_Id'] . '</td>';
                                            echo '<td>' . $row_service['Type_of_Service'] . '</td>';
                                            echo '<td>' . $row_service['Service_Charge'] . '</td>';

                                            echo '<td>
                                            <form action="type_service_edit.php" method="post">
                                                <input type="hidden" name="edit_id" value="' . $row_service['T_Service_Id'] . '">
                                                <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                            </form>
                                            </td>';

                                            echo '<td>
                                                    <form action="type_service_delete.php" method="post">
                                                        <input type="hidden" name="delete_id" value="' . $row_service['T_Service_Id'] . '">
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
                                    <form action="#" method="post" onsubmit="return validateForm()">                                       
                                    <div class="form-group">
                                            <label for="Type_of_Service">Type of Service:</label>
                                            <input type="text" class="form-control" id="Type_of_Service" name="Type_of_Service" required>

                                        </div>

                                        <div class="form-group">
                                            <label for="Service_Charge">Service Charge:</label>
                                            <input type="text" class="form-control" id="Service_Charge" name="Service_Charge" required>

                                        </div>

                                        <button type="submit" class="btn btn-primary" name="add_service_btn">Add Service</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>

</html>
<?php
mysqli_close($connect);
include('Includes2/scripts.php');
include('Includes2/footer.php');
?>

