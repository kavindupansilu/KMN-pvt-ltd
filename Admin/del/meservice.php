<?php
session_start();
include('Includes2/header.php');
include('Includes2/mechanicnavbar.php');

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Service
if (isset($_POST['add_service_btn'])) {
    $service_date = $_POST['Service_Date'];
    $formatted_service_date = date('Y-m-d', strtotime($service_date));

    $type_of_service = implode(",", $_POST['Type_of_Service']);
    $parts_used = isset($_POST['Parts_Used']) && is_array($_POST['Parts_Used']) ? implode(",", $_POST['Parts_Used']) : '';
    // $parts_used = $_POST['Parts_Used'];
    $start_time = $_POST['S_Time'];
    $end_time = $_POST['E_Time'];
    $total_cost = $_POST['Total_Cost'];
    $mechanic_id = $_POST['Mechanic_Id'];
    $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

    $insert_service_query = "INSERT INTO service (`Service_Date`, `Type_of_Service`, `Parts_Used`, `S_Time`, `E_Time`, `Total_Cost`, `Mechanic_Id`, `User_Id`)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_add_service = mysqli_prepare($connect, $insert_service_query);
    mysqli_stmt_bind_param($stmt_add_service, "ssssssss", $formatted_service_date, $type_of_service, $parts_used, $start_time, $end_time, $total_cost, $mechanic_id, $user_id);

    if (mysqli_stmt_execute($stmt_add_service)) {
        $_SESSION['success'] = "Service added successfully!";
    } else {
        $_SESSION['error'] = "Error adding service: " . mysqli_stmt_error($stmt_add_service);
    }

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

<script>
    function validateForm() {
        var serviceDate = document.getElementById('Service_Date').value;
        var currentTime = new Date().toISOString().slice(0, 10); // Get current date

        if (serviceDate < currentTime) {
            alert("Please select today's date or a future date for the service.");
            return false;
        }

        var startTime = document.getElementById('S_Time').value;
        var endTime = document.getElementById('E_Time').value;

        // Convert start time and end time to minutes for easier comparison
        var startTimeMinutes = convertToMinutes(startTime);
        var endTimeMinutes = convertToMinutes(endTime);

        // Get the current time in minutes
        var currentTimeMinutes = new Date().getHours() * 60 + new Date().getMinutes();

        if (startTimeMinutes >= endTimeMinutes) {
            alert("End Time must be greater than Start Time.");
            return false;
        }

        if (endTimeMinutes > currentTimeMinutes) {
            alert("End Time must be less than or equal to the current time.");
            return false;
        }

        return true;
    }

    // Function to convert time in HH:mm format to minutes
    function convertToMinutes(time) {
        var splitTime = time.split(':');
        var hours = parseInt(splitTime[0]);
        var minutes = parseInt(splitTime[1]);
        return hours * 60 + minutes;
    }
</script>

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
                    <h1 class="h3 mb-2 text-gray-800">Service Details</h1>

                  

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
                                        echo '<th>SERVICE_ID</th>';
                                        echo '<th>SERVICE_DATE</th>';
                                        echo '<th>TYPE_OF_SERVICE</th>';
                                        echo '<th>PARTS_USED</th>';
                                        echo '<th>START_TIME</th>';
                                        echo '<th>END_TIME</th>';
                                        echo '<th>TOTAL_COST</th>';
                                        echo '<th>MECHANIC_ID</th>';
                                        echo '<th>USER_ID</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row_service = mysqli_fetch_assoc($query_run_service)) {
                                            echo '<tr>';
                                            echo '<td>' . $row_service['Service_Id'] . '</td>';
                                            echo '<td>' . $row_service['Service_Date'] . '</td>';
                                            echo '<td>' . implode(', ', explode(',', $row_service['Type_of_Service'])) . '</td>';

                                            // Explode the comma-separated string into an array
                                            $partsUsedArray = explode(",", $row_service['Parts_Used']);

                                            // Display each part separately
                                            echo '<td>' . implode('<br>', $partsUsedArray) . '</td>';

                                           // echo '<td>' . $row_service['Parts_Used'] . '</td>';
                                            echo '<td>' . $row_service['S_Time'] . '</td>';
                                            echo '<td>' . $row_service['E_Time'] . '</td>';
                                            echo '<td>' . $row_service['Total_Cost'] . '</td>';
                                            echo '<td>' . $row_service['Mechanic_Id'] . '</td>';
                                            echo '<td>' . $row_service['User_Id'] . '</td>';

                                            echo '<td>
                                                    <form action="meservice_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row_service['Service_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="meservice_delete.php" method="post">
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
                                        <span aria-hidden="true">×</span>
                                    </button>
                                </div>
                                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                                <div class="modal-footer">
                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                    <a class="btn btn-danger" href="home.php">Logout</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    mysqli_close($connect);
                    include('Includes2/scripts.php');
                    include('Includes2/footer.php');
                    ?>

                    <script src="vendor/jquery/jquery.min.js"></script>
                    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
                    <script src="js/script.min.js"></script>
                    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
                    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
                    <script src="js/datatables-demo.js"></script>
</body>

</html>
