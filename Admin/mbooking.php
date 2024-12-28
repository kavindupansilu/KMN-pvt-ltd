<?php
session_start();
include('Includes2/header.php');
include('Includes2/managernavbar.php');


require "dbh.inc.php";
//$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

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
        $num = $_POST['Vehicle_Id'];
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
            $insert_booking_query = "INSERT INTO booking (`booking_Date`, `time`, `Type_of_Service`, `Vehicle_Id`, `phone`, `Referral`, `Cust_Id`, `Manager_Id`, `User_Id`)
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt_add_booking = mysqli_prepare($connect, $insert_booking_query);

            // Bind parameters
            mysqli_stmt_bind_param($stmt_add_booking, "ssssissss", $formatted_booking_date, $time, $type_of_service, $num, $phone, $referral, $cust_id, $manager_id, $user_id);

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
                                        echo '<th>VEHICLE NUMBER</th>';
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
                                            //echo '<td>' . $row_booking['Type_of_Service'] . '</td>';
                                            
                                            // Explode the comma-separated string into an array
                                            $serviceType = explode(",", $row_booking['Type_of_Service']);

                                            // Display each serviceType separately
                                            echo '<td>' . implode('<br>', $serviceType) . '</td>';

                                            echo '<td>' . $row_booking['Vehicle_Id'] . '</td>';
                                            echo '<td>' . $row_booking['phone'] . '</td>';
                                            echo '<td>' . $row_booking['Referral'] . '</td>';
                                            echo '<td>' . $row_booking['Cust_Id'] . '</td>';
                                            echo '<td>' . $row_booking['Manager_Id'] . '</td>';
                                            echo '<td>' . $row_booking['User_Id'] . '</td>';

                                            echo '<td>
                                                    <form action="mbooking_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row_booking['Booking_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="mbooking_delete.php" method="post">
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
</body>

</html>

<?php
include('Includes2/footer.php');
mysqli_close($connect);
?>
