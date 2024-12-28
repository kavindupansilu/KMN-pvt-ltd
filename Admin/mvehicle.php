<?php
session_start();
include('Includes2/header.php');
include('Includes2/managernavbar.php');

require "dbh.inc.php";
// $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Vehicle
if (isset($_POST['add_vehicle_btn'])) {
    $vehicle_id = $_POST['Vehicle_Id'];
    $type = $_POST['Type'];
    $color = $_POST['Color'];
    $cust_id = $_POST['Cust_Id']; // Allow only Registered Cust_id
      // Fetch User_Id from session
      // Take current login UserId
      $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

    // Log the Cust_Id for troubleshooting
    error_log("Attempting to insert vehicle with Cust_Id: $cust_id");

    // Using prepared statements to prevent SQL injection
    $insert_vehicle_query = "INSERT INTO vehicle (`Vehicle_Id`, `Type`, `Color`, `Cust_Id`, `User_Id`)
                             VALUES (?, ?, ?, ?, ?)";

    $stmt_add_vehicle = mysqli_prepare($connect, $insert_vehicle_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt_add_vehicle, "sssss", $vehicle_id, $type, $color, $cust_id, $user_id);

    // Execute the statement and handle errors
    if (mysqli_stmt_execute($stmt_add_vehicle)) {
        $_SESSION['success'] = "Vehicle added successfully!";
    } else {
        $_SESSION['error'] = "Error adding vehicle: " . mysqli_stmt_error($stmt_add_vehicle);
    }

    // Close the statement
    mysqli_stmt_close($stmt_add_vehicle);
}

// Display Vehicles
$query_vehicle = "SELECT * FROM vehicle";
$query_run_vehicle = mysqli_query($connect, $query_vehicle);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <link rel="stylesheet" href="tables.css">
    <style>
        #vehicles thead th {
            background-color: #9999ff;
            color: black;
        }
    </style>

    <script>
        function confirmLogout() {
            return confirm("Are you sure you want to log out?");
        }
    </script>

<script>
        function showErrorMessage(fieldId, message) {
            document.getElementById(fieldId + '-error').innerText = message;
        }

        function validateForm() {
            var vehicleid = document.getElementById('Vehicle_Id').value;

            // Validate Vehicle_Id format
            var vehicleidRegex = /^[A-Z]{3}[0-9]{4}|[A-Z]{2}[0-9]{4}$/;
            if (!vehicleidRegex.test(vehicleid)) {
                showErrorMessage('Vehicle_Id', 'Vehicle_Id must have 3 letters and 4 digits or 2 letters and 4 digits.');
                return false; // Prevent form submission
            } else {
                showErrorMessage('Vehicle_Id', ''); // Clear previous error message
            }

             return true; // Allow form submission
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
                        <a class="btn btn-danger" onclick="return confirmLogout();" href="logout.php">Log Out</a>
                            </div>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Vehicle Details</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Vehicle Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                if ($query_run_vehicle) {
                                    if (mysqli_num_rows($query_run_vehicle) > 0) {
                                        echo '<table id="vehicles" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>VEHICLE_ID</th>';
                                        echo '<th>TYPE</th>';
                                        echo '<th>COLOR</th>';
                                        echo '<th>CUST_ID</th>';
                                        echo '<th>USER_ID</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row_vehicle = mysqli_fetch_assoc($query_run_vehicle)) {
                                            echo '<tr>';
                                            echo '<td>' . $row_vehicle['Vehicle_Id'] . '</td>';
                                            echo '<td>' . $row_vehicle['Type'] . '</td>';
                                            echo '<td>' . $row_vehicle['Color'] . '</td>';
                                            echo '<td>' . $row_vehicle['Cust_Id'] . '</td>';
                                            echo '<td>' . $row_vehicle['User_Id'] . '</td>';
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
