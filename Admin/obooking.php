<?php
session_start();

$booking_success_message = "";

// Initialize variables to avoid undefined variable warnings
$phone = "";
$cust_id = "";
$num = "";

require "dbh.inc.php";
if (!$connect) {
  die("Connection failed: " . mysqli_connect_error());
}

// Check if user is logged in
if (isset($_SESSION['Cust_Id']) && isset($_SESSION['phone']) && isset($_SESSION['Vehicle_Id'])) {
  $phone = $_SESSION['phone'];
  $cust_id = $_SESSION['Cust_Id'];
  $num = $_SESSION['Vehicle_Id'];
  //$num = isset($_POST['Vehicle_Id']) ? $_POST['Vehicle_Id'] : "";
} else {
  // If user is not logged in, redirect to login page
  header("Location: login.php");
  exit();
}

// Add Booking
if (isset($_POST['add_booking_btn'])) {
    //$booking_date = $_POST['date'];
    $booking_date = $_POST['booking_Date'];
    $time = $_POST['time'];
    //$type_of_service = implode(",", $_POST['Type_of_Service']);
    $type_of_service = isset($_POST['Type_of_Service']) && is_array($_POST['Type_of_Service']) ? implode(",", $_POST['Type_of_Service']) : '';
    $referral = $_POST['referral'];

    // Prepare the statement
    $query_booking = "INSERT INTO booking (booking_Date, time, Type_of_Service, Vehicle_Id, phone, Referral, Cust_Id)
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($connect, $query_booking);

    // Bind parameters
    mysqli_stmt_bind_param($stmt, 'sssssss', $booking_date, $time, $type_of_service, $num, $phone, $referral, $cust_id);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Check if the query was successful
    if(mysqli_stmt_affected_rows($stmt) > 0) {
        
        
        // Booking added successfully
        $booking_success_message = "Thank You! Your Booking Added Successfully.";
    } else {
        // Error occurred
        echo "Error adding booking: " . mysqli_error($connect);
        
    }

    // Close the statement
    mysqli_stmt_close($stmt);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Your Appointment Form</title>
<link rel="icon" href="icon.png">

  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 600px;
      margin: 50px auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    label {
      font-weight: bold;
    }

    input[type="date"],
    input[type="time"],
    select,
    input[type="tel"],
    input[type="text"] {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
      box-sizing: border-box;
    }

    .error {
      color: red;
    }

    h2{
      color:blue;
      text-align:center;
    }

    .white-box {
            background-color: white;
            padding: 10px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

.red-text {
            color: green;
        }

  </style>
</head>
<body>
  
  <div class="container">
    <h2>Book Your Appointment</h2>
    <form  action="#" method="post">
      <div>
        <label for="booking_Date">Date:</label>
        <input type="date" id="booking_Date" name="booking_Date" min="<?php echo date('Y-m-d'); ?>" required>
      </div>
      <div>
        <label for="time">Time:</label>
        <input type="time" id="time" name="time" required>
      </div>
      <div>
        <label for="Type_of_Service">Type of Service:</label>
        <select class="form-control" id="Type_of_Service" name="Type_of_Service[]" multiple required>
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
        </select>
      </div>
      <div>
        <label for="phone">Phone:</label>
        <input type="text"  name="phone" readonly value="<?php echo $phone; ?>">
      </div>
      
       <div>
        <label for="Vehicle_Id">Vehicle Number:</label>
        <input type="text" name="Vehicle_Id"readonly value="<?php echo $num; ?>">
    </div>
      <div>
        <label for="Cust_Id">NIC :</label>
        <input type="text" id='Cust_Id' name='Cust_Id' readonly value="<?php echo $cust_id; ?>">
      </div>
      <div>
        <label for="referral">Referral:</label>
        <input type="text" id="referral" name="referral" maxlength="20">
      </div>
      <div>
      <div class="modal fade" id="addbooking" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">

         <button type="submit" name="add_booking_btn">Add Booking</button> 
         <!-- Display success message in a white box with red text -->
         <div class="white-box">
                        <p class="red-text"><?php echo $booking_success_message; ?></p>
                    </div>
      </div>
    </form>
  </div>

  <script>
  

  // Ensure time is not before current time within the current day
  var currentDate = new Date();
    var currentDateString = currentDate.toISOString().split('T')[0];
    document.getElementById('date').setAttribute('min', currentDateString);

    // Update the minimum time whenever the date changes
    document.getElementById('date').addEventListener('change', function() {
      var selectedDate = new Date(this.value);
      if (selectedDate.toDateString() === currentDate.toDateString()) {
        var hours = currentDate.getHours();
        var minutes = currentDate.getMinutes();
        var formattedTime = (hours < 10 ? '0' : '') + hours + ':' + (minutes < 10 ? '0' : '') + minutes;
        document.getElementById('time').setAttribute('min', formattedTime);
      } else {
        // If the selected date is not the current date, there's no minimum time constraint
        document.getElementById('time').removeAttribute('min');
      }
    });
</script>

</body>
</html>