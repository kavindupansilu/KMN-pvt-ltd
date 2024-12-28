<?php
session_start();
include('Includes2/header.php');
include('Includes2/navbar.php');

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Booking
if (isset($_POST['add_booking_btn'])) {
    $booking_date = $_POST['booking_Date'];
    $time = $_POST['time'];

    // Validate date and time format
    if (!isValidDate($booking_date) || !isValidTime($time)) {
        $_SESSION['error'] = "Invalid date or time format!";
    } else {
        // Format the date
        $formatted_booking_date = date('Y-m-d', strtotime($booking_date));

        $type_of_service = implode(",", $_POST['Type_of_Service']);
        $type = $_POST['Vehicle_Type'];
        $phone = $_POST['phone'];
        $referral = $_POST['Referral'];
        $cust_id = $_POST['Cust_Id'];
        $manager_id = $_POST['Manager_Id'];
        $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

        

        // Check if the booking already exists for any customer
            $check_booking_query = "SELECT * FROM booking WHERE booking_Date = ? AND time = ? AND Type_of_Service = ?";
            $stmt_check_booking = mysqli_prepare($connect, $check_booking_query);
            mysqli_stmt_bind_param($stmt_check_booking, "sss", $formatted_booking_date, $time, $type_of_service);
            mysqli_stmt_execute($stmt_check_booking);
            mysqli_stmt_store_result($stmt_check_booking);
        if (mysqli_stmt_num_rows($stmt_check_booking) > 0) {
            $_SESSION['error'] = "Sorry, we can't make the booking. Booking with the same time, type of service, and Cust_Id already exists.";
        } else {
            // Using prepared statements to prevent SQL injection
            $insert_booking_query = "INSERT INTO booking (`booking_Date`, `time`, `Type_of_Service`, `Vehicle_Type`, `phone`, `Referral`, `Cust_Id`, `Manager_Id`, `User_Id`)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt_add_booking = mysqli_prepare($connect, $insert_booking_query);

            // Bind parameters
            mysqli_stmt_bind_param($stmt_add_booking, "ssssissss", $formatted_booking_date, $time, $type_of_service, $type, $phone, $referral, $cust_id, $manager_id, $user_id);

            // Execute the statement and handle errors
            if (mysqli_stmt_execute($stmt_add_booking)) {
                $_SESSION['success'] = "Booking added successfully!";
            } else {
                $_SESSION['error'] = "Error adding booking: " . mysqli_stmt_error($stmt_add_booking);
            }

            // Close the statement
            mysqli_stmt_close($stmt_add_booking);
        }

        // Close the check statement
        mysqli_stmt_close($stmt_check_booking);
    }
}

// Display Bookings
$query_booking = "SELECT * FROM booking";
$query_run_booking = mysqli_query($connect, $query_booking);

function isValidDate($date)
{
    return (bool)strtotime($date);
}

function isValidTime($time)
{
    return (bool)strtotime($time);
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

        #booking thead th {
            background-color: #9999ff;
            color: black;
        }

        .error-popup {
            color: red;
        }
    </style>

<script>
        function showErrorMessage(fieldId, message) {
            document.getE
            lementById(fieldId + '-error').innerText = message;
        }

        function updateSelectOptions(selectId, options) {
            var selectElement = document.getElementById(selectId);
            selectElement.innerHTML = '';

            for (var i = 0; i < options.length; i++) {
                var option = document.createElement('option');
                option.value = options[i];
                option.text = options[i];
                selectElement.add(option);
            }
        }

        function updateTimeOptions() {
            var bookingDate = new Date(document.getElementById('booking_Date').value);
            var currentDate = new Date();

            var timeOptions = [];
            var startHour = 7; // Start from 7:00 AM
            var endHour = 19;  // End at 7:00 PM

            if (bookingDate.toDateString() === currentDate.toDateString()) {
                // If booking is for today, adjust startHour based on current time
                var currentHour = currentDate.getHours();
                startHour = Math.max(startHour, currentHour + 1);
            }

            for (var i = startHour; i < endHour; i++) {
                for (var j = 0; j < 60; j += 15) { // Increment by 15 minutes
                    var hour = (i < 10) ? '0' + i : i;
                    var minute = (j < 10) ? '0' + j : j;
                    timeOptions.push(hour + ':' + minute);
                }
            }

            updateSelectOptions('time', timeOptions);
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
                    <h1 class="h3 mb-2 text-gray-800">Booking Details</h1>

                    <!-- Add Booking Form -->
                    <a href="" class="btn btn-primary" data-toggle="modal" data-target="#addbookingmodel">Booking</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Booking Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                if ($query_run_booking) {
                                    if (mysqli_num_rows($query_run_booking) > 0) {
                                        echo '<table id="booking" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>BOOKING ID</th>';
                                        echo '<th>BOOKING DATE</th>';
                                        echo '<th>TIME</th>';
                                        echo '<th>TYPE OF SERVICE</th>';
                                        echo '<th>VEHICLE TYPE</th>';
                                        echo '<th>Phone</th>';
                                        echo '<th>REFERRAL</th>';
                                        echo '<th>CUST ID</th>';
                                        echo '<th>MANAGER ID</th>';
                                        echo '<th>USER_ID</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row_booking = mysqli_fetch_assoc($query_run_booking)) {
                                            echo '<tr>';
                                            echo '<td>' . $row_booking['Booking_Id'] . '</td>';
                                            echo '<td>' . $row_booking['booking_Date'] . '</td>';
                                            echo '<td>' . $row_booking['time'] . '</td>';
                                            echo '<td>' . $row_booking['Type_of_Service'] . '</td>';
                                            echo '<td>' . $row_booking['Vehicle_Type'] . '</td>';
                                            echo '<td>' . $row_booking['phone'] . '</td>';
                                            echo '<td>' . $row_booking['Referral'] . '</td>';
                                            echo '<td>' . $row_booking['Cust_Id'] . '</td>';
                                            echo '<td>' . $row_booking['Manager_Id'] . '</td>';
                                            echo '<td>' . $row_booking['User_Id'] . '</td>';

                                            echo '<td>
                                                    <form action="booking_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row_booking['Booking_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="booking_delete.php" method="post">
                                                        <input type="hidden" name="delete_id" value="' . $row_booking['Booking_Id'] . '">
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

                <!-- Add Booking Modal -->
                <div class="modal fade" id="addbookingmodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add Booking</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Add Booking Form -->
                                <form action="" method="post" onsubmit="return validateForm()">
                                    <div class="form-group">
                                        <label for="booking_Date">Booking Date:</label>
                                        <input type="date" class="form-control" id="booking_Date" name="booking_Date" onchange="updateTimeOptions()" required
                                               min="<?php echo date('Y-m-d'); ?>">
                                        <span class="error-message" id="booking_Date-error"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="time">Booking Time:</label>
                                        <select class="form-control" id="time" name="time" required>
                                            <!-- Time options will be dynamically added here -->
                                        </select>
                                        <span class="error-message" id="time-error"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="Type_of_Service">Type of Service:</label>
                                        <select class="form-control" id="Type_of_Service" name="Type_of_Service[]" multiple required>
                                            <!-- Add options dynamically from your database or define static options -->
                                            
                                            <option value="Oil Change">Oil Change</option>
                                            <option value="Tire Rotation">Tire Rotation</option>
                                            <option value="Fluid Checks and Replacements">Fluid Checks and Replacements</option>
                                            <option value="Air Filter Replacement">Air Filter Replacement</option>
                                            <option value="Cabin Air Filter Replacement">Cabin Air Filter Replacement</option>
                                            <option value="Engine Diagnostics">Engine Diagnostics</option>
                                            <option value="Computerized Diagnostics">Computerized Diagnostics</option>
                                            <option value="Brake System Repair">Brake System Repair</option>
                                            <option value="Suspension and Steering Repair">Suspension and Steering Repair</option>
                                            <option value="Engine Repair">Engine Repair</option>
                                            <option value="Transmission Repair">Transmission Repair</option>
                                            <option value="Electrical System Repair: ">Electrical System Repair: </option>
                                            <option value="Wheel Alignment">Wheel Alignment</option>
                                            <option value="Air Conditioning Service">Air Conditioning Service</option>
                                            <option value="Heating System Service">Heating System Service</option>
                                            <option value="Emission System Service">Emission System Service</option>
                                            <option value="Performance Upgrades">Performance Upgrades</option>
                                            <option value="Interior and Exterior Detailing">Interior and Exterior Detailing</option>
                                            <option value="Paint and Bodywork">Paint and Bodywork</option>
                                            
                                            <!-- Add more options as needed -->

                                        </select>
                                    </div>

                                    <div class="form-group">
                                    <label for="Vehicle_Type">Vehicle Type:</label>
                                            <select id="Vehicle_Type" name="Vehicle_Type" required>
                                            <option value="">Select Vehicle Type</option>
                                            <option value="Bike">Bike</option>
                                            <option value="Car">Car</option>
                                            <option value="Jeep">Jeep</option>
                                            <option value="Truck">Truck</option>
                                            <option value="Bus">Bus</option>
                                            <option value="Van">Van</option>
                                            <option value="TukTuk">TukTuk</option>
                                            <option value="Lorry">Lorry</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="phone">Phone:</label>
                                        <input type="text" class="form-control" id="phone" name="phone" pattern="^(07[01245678][0-9]{7})$" required>
                                        <span class="error-message" id="phone-error"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="Referral">Referral:</label>
                                        <input type="text" class="form-control" id="Referral" name="Referral" pattern="[A-Za-z]+" title="Only letters are allowed">
                                    </div>

                                    <div class="form-group">
                                        <label for="Cust_Id">Cust Id:</label>
                                        <input type="text" class="form-control" id='Cust_Id' name='Cust_Id' pattern='^[0-9]{9}[vV]|[0-9]{12}$' required>
                                        <span class="error-message" id="Cust_Id-error"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="Manager_Id">Manager Id:</label>
                                        <input type="text" class="form-control" id='Manager_Id' name='Manager_Id'>
                                        <span class="error-message" id="Cust_Id-error"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="User_Id">User ID:</label>
                                        <input type="text" class="form-control" id="User_Id" name="User_Id" readonly value="<?php echo isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : ''; ?>">
                                    </div>

                                    <button type="submit" class="btn btn-primary" name="add_booking_btn">Add Booking</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Modal -->
                <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Error</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <?php
                                if (isset($_SESSION['error'])) {
                                    echo '<p class="error-popup">' . $_SESSION['error'] . '</p>';
                                    unset($_SESSION['error']);
                                }
                                ?>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Close</button>
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

                <!-- Display error modal if there's an error -->
                <?php
                if (isset($_SESSION['error'])) {
                    echo '<script>$(document).ready(function(){$("#errorModal").modal("show");});</script>';
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>

<?php
include('Includes2/footer.php');
mysqli_close($connect);
?>
