<?php
session_start();
include('Includes2/header.php');
//include('Includes2/navbar.php');

// Function to check if a date is valid
function isValidDate($date) {
    $format = 'Y-m-d';
    $dateTime = DateTime::createFromFormat($format, $date);
    return $dateTime && $dateTime->format($format) === $date;
}

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

$edit_id = '';

if (isset($_POST['edit_btn'])) {
    $edit_id = $_POST['edit_id'];

    $query = "SELECT * FROM booking WHERE Booking_Id = '$edit_id'";
    $query_run = mysqli_query($connect, $query);

    if ($query_run) {
        $row = mysqli_fetch_assoc($query_run);
        $booking_date = $row['booking_Date'];
        $time = $row['time'];
        $type_of_service = $row['Type_of_Service'];
        $phone = $row['phone'];
        $referral = $row['Referral'];
        $cust_id = $row['Cust_Id'];
        $manager_id = $row['Manager_Id'];
    } else {
        echo "Error in the query: " . mysqli_error($connect);
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $booking_date = $_POST['booking_Date'];
    // Format the date
    $formatted_booking_date = date('Y-m-d', strtotime($booking_date));
    $time = $_POST['time'];

    // Validate date and time format
    if (!isValidDate($formatted_booking_date)) {
        $_SESSION['error'] = "Invalid date or time format!";
    } else {
        $type_of_service = implode(",", $_POST['Type_of_Service']);
        $phone = $_POST['phone'];
        $referral = $_POST['Referral'];
        $cust_id = $_POST['Cust_Id'];
        $manager_id = $_POST['Manager_Id'];

        $update_query = "UPDATE booking 
                         SET booking_Date='$formatted_booking_date', time='$time', Type_Of_Service='$type_of_service', phone='$phone', Referral='$referral', Cust_Id='$cust_id', Manager_Id='$manager_id'
                         WHERE Booking_Id='$edit_id'";
        mysqli_query($connect, $update_query);

        header("Location: mbooking.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link rel="stylesheet" href="edit.form.css">
    <script>
        function showErrorMessage(fieldId, message) {
            document.getElementById(fieldId + '-error').innerText = message;
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
                <div class="container-fluid center-form">
                    <div class="form-container">
                        <h1 class="h3 mb-2 text-gray-800">Edit Booking Details</h1>

                        <!-- Edit Booking Form -->
                        <form action="mbooking_edit.php" method="post">

                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                           
                            <div class="form-group">
                                <label for="booking_Date">Booking Date:</label>
                                <input type="date" class="form-control" id="booking_Date" name="booking_Date" value="<?php echo $booking_date; ?>" onchange="updateTimeOptions()" required
                                       min="<?php echo date('Y-m-d'); ?>">
                                <span class="error-message" id="booking_Date-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="time">Booking Time:</label>
                                <select class="form-control" id="time" name="time" required>
                                    <?php
                                    // Assuming $timeOptions is an array of available time options
                                    foreach ($timeOptions as $option) {
                                        // Check if the option matches the current booking time
                                        $selected = ($option == $time) ? 'selected' : '';

                                        echo '<option value="' . $option . '" ' . $selected . '>' . $option . '</option>';
                                    }
                                    ?>
                                </select>
                                <span class="error-message" id="time-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="Type_of_Service">Type of Service:</label>
                                <select class="form-control" id="Type_of_Service" name="Type_of_Service[]" multiple required>
                                    <!-- Add options dynamically from your database or define static options -->
                                    <option value="car wash" <?php if (strpos($type_of_service, 'car wash') !== false) echo 'selected="selected"'; ?>>car wash</option>
                                    <option value="body wash" <?php if (strpos($type_of_service, 'body wash') !== false) echo 'selected="selected"'; ?>>body wash</option>
                                    <option value="body color" <?php if (strpos($type_of_service, 'body color') !== false) echo 'selected="selected"'; ?>>body color</option>
                                    <!-- Add more options as needed -->
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="phone">Phone:</label>
                                <input type="text" class="form-control" id="phone" name="phone" pattern='^0(7[0124578]|[0124578])[0-9]{7}$' value="<?php echo $phone; ?>" required>
                                <span class="error-message" id="phone-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="Referral">Referral:</label>
                                <input type="text" class="form-control" id="Referral" name="Referral" pattern="[A-Za-z]+" title="Only letters are allowed" value="<?php echo $referral; ?>">
                            </div>

                            <div class="form-group">
                                <label for="Cust_Id">Cust Id:</label>
                                <input type="text" class="form-control" id='Cust_Id' name='Cust_Id' pattern='^[0-9]{9}[vV]|[0-9]{12}$' required value="<?php echo $cust_id; ?>">
                                <span class="error-message" id="Cust_Id-error"></span>
                            </div>

                            <div class="form-group">
                                <label for="Manager_Id">Manager Id:</label>
                                <input type="text" class="form-control" id='Manager_Id' name='Manager_Id' value="<?php echo $manager_id; ?>">
                                <span class="error-message" id="Manager_Id-error"></span>
                            </div>

                            <button type="submit" class="btn btn-primary" name="update_btn">Update Booking</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    include('Includes2/footer.php');
    mysqli_close($connect);
    ?>
</body>

</html>
