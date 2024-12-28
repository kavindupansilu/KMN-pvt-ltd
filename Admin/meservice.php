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
    $formatted_service_date = date('Y-m-d', strtotime($service_date));

    $type_of_service = implode(",", $_POST['Type_of_Service']);
    $start_time = $_POST['S_Time'];
    $end_time = $_POST['E_Time'];
    $parts_used = isset($_POST['Parts_Used']) && is_array($_POST['Parts_Used']) ? implode(",", $_POST['Parts_Used']) : '';
    $item_cost = $_POST['Item_Cost'];
    $service_charge = $_POST['Service_Charge'];
    $total_cost = $_POST['Total_Cost'];
    $bookingid = $_POST['Booking_Id'];
    $vehicle_id = $_POST['Vehicle_Id'];
    $mechanic_id = $_POST['Mechanic_Id'];
    $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

    $insert_service_query = "INSERT INTO service (`Service_Date`, `Type_of_Service`,  `S_Time`, `E_Time`, `Parts_Used`, `Item_Cost`, `Service_Charge`, `Total_Cost`, `Booking_Id`, `Vehicle_Id`, `Mechanic_Id`, `User_Id`)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


    $stmt_add_service = mysqli_prepare($connect, $insert_service_query);
    mysqli_stmt_bind_param($stmt_add_service, "sssssdddisss", $formatted_service_date, $type_of_service,  $start_time, $end_time, $parts_used, $item_cost, $service_charge, $total_cost, $bookingid, $vehicle_id,  $mechanic_id, $user_id);

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
<?php
// get booking details after click ADD in booking.php

if (isset($_POST['add_to_service_btn'])) {
    $booking_id = $_POST['booking_id'];
    $booking_date = $_POST['booking_date'];
    $booking_time = $_POST['booking_time'];
    $service_type = $_POST['service_type'];

    // Display the captured booking details
    echo 'Booking ID: ' . $booking_id . '<br>';
    echo 'Booking Date: ' . $booking_date . '<br>';
    echo 'Booking Time: ' . $booking_time . '<br>';
    echo 'Service Type: ' . $service_type . '<br>';
}
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

    // Function to Calculate Total
    function calculateTotalCost() {
            var itemCost = parseFloat(document.getElementById('Item_Cost').value) || 0;
            var serviceCharge = parseFloat(document.getElementById('Service_Charge').value) || 0;
            var totalCost = itemCost + serviceCharge;

            document.getElementById('Total_Cost').value = totalCost.toFixed(2);
        }


    // Function to send AJAX request when the sales date is changed
    function fetchServiceInformation() {
        var salesDate = document.getElementById('Sales_Date').value;
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    var serviceInfo = JSON.parse(xhr.responseText);
                    // Update UI with fetched service information
                    document.getElementById('Service_Revenue').value = serviceInfo.totalServiceRevenue;
                    // You can update other parts of the UI as needed
                } else {
                    console.error('Failed to fetch service information');
                }
            }
        };
        xhr.open('GET', 'fetch_service_info.php?salesDate=' + salesDate, true);
        xhr.send();
    }

    // Function to update item cost
function updateItemCost(checkbox) {
    var selectedItems = document.querySelectorAll('input[name="Parts_Used[]"]:checked');
    var totalItemCost = 0;

    selectedItems.forEach(function(item) {
        var itemName = item.value;
        var quantity = document.getElementById('Quantity[]' + itemName).value || 1; // Default quantity is 1 if not provided

        // Send AJAX request to get item cost
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var itemCost = parseFloat(this.responseText);
                var itemTotalCost = itemCost * quantity;
                totalItemCost += itemTotalCost;
                document.getElementById("Item_Cost").value = totalItemCost.toFixed(2);
                calculateTotalCost(); // Call function to recalculate total cost
            }
        };
        xhttp.open("GET", "get_item_cost.php?item=" + itemName, true);
        xhttp.send();
    });
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
                                // Displaying service information
                                if ($query_run_service) {
                                    if (mysqli_num_rows($query_run_service) > 0) {
                                        echo '<table id="services" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>SERVICE_ID</th>';
                                        echo '<th>SERVICE_DATE</th>';
                                        echo '<th>TYPE_OF_SERVICE</th>';
                                        echo '<th>START_TIME</th>';
                                        echo '<th>END_TIME</th>';
                                        echo '<th>PARTS_USED</th>';
                                        echo '<th>ITEM COST</th>';
                                        echo '<th>SERVICE CHARGE</th>';
                                        echo '<th>TOTAL_COST</th>';
                                        echo '<th>BOOKING_ID</th>';
                                        echo '<th>VEHICLE_ID</th>';
                                        echo '<th>MECHANIC_ID</th>';
                                        echo '<th>USER_ID</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                while ($row_service = mysqli_fetch_assoc($query_run_service)) {
                                    // Displaying each row of service data
                                    echo '<tr>';
                                    echo '<td>' . $row_service['Service_Id'] . '</td>';
                                    echo '<td>' . $row_service['Service_Date'] . '</td>';
                                    echo '<td>' . implode(', ', explode(',', $row_service['Type_of_Service'])) . '</td>';
                                    echo '<td>' . $row_service['S_Time'] . '</td>';
                                    echo '<td>' . $row_service['E_Time'] . '</td>';

                                    // Parse and display parts used with quantities
                                    $parts_used_array = explode(',', $row_service['Parts_Used']);

                                    echo '<td>';
                                    foreach ($parts_used_array as $part) {
                                        echo $part . '<br>';
                                    }
                                    echo '</td>';
                                    echo '<td>' . $row_service['Item_Cost'] . '</td>';
                                    echo '<td>' . $row_service['Service_Charge'] . '</td>';
                                    echo '<td>' . $row_service['Total_Cost'] . '</td>';
                                    echo '<td>' . $row_service['Booking_Id'] . '</td>';
                                    echo '<td>' . $row_service['Vehicle_Id'] . '</td>';
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

                    
                    

                    <script src="vendor/jquery/jquery.min.js"></script>
                    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
                    <script src="js/script.min.js"></script>
                    <script src="vendor/datatables/jquery.dataTables.min.js"></script>
                    <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
                    <script src="js/datatables-demo.js"></script>
</body>

</html>
<?php
                    mysqli_close($connect);
                    include('Includes2/scripts.php');
                    include('Includes2/footer.php');
                    ?>


