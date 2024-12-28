<?php
session_start();
require_once "dbh.inc.php"; // Include your database connection file

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

$edit_id = '';

if (isset($_POST['edit_btn'])) {
    $edit_id = $_POST['edit_id'];

    $query = "SELECT * FROM service WHERE Service_Id = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "i", $edit_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        $service_date = $row['Service_Date'];
        $type_of_service = explode(",", $row['Type_of_Service']); // explode the string to get an array of selected services
            // Process quantities
$quantities; //= $row['Quantity'];
$parts_with_quantities = [];
       // $parts_used = explode(",", $row['Parts_Used']); // explode the string to get an array of selected parts used
        $parts_used_with_quantities = implode(",", $parts_with_quantities);
    $parts_used = isset($row['Parts_Used']) && is_array($row['Parts_Used']) ? implode(",", $row['Parts_Used']) : '';
        $elapsed_time = $row['E_Time'];

        $item_cost = $row['Item_Cost'];
        $service_charge = $row['Service_Charge'];
        $total_cost = $row['Total_Cost'];
        // Add other fields if necessary
    } else {
        echo "Error in the query: " . mysqli_error($connect);
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $service_date = $_POST['Service_Date'];
    $formatted_service_date = date('Y-m-d', strtotime($service_date));
    $type_of_service = isset($_POST['Type_of_Service']) && is_array($_POST['Type_of_Service']) ? implode(",", $_POST['Type_of_Service']) : '';
    $end_time = $_POST['E_Time'];

    // Process quantities
$quantities = $_POST['Quantity'];
$parts_with_quantities = [];

// Iterate through each selected part and its quantity
foreach ($quantities as $item_name => $quantity) {
    // Check if quantity is greater than zero
    if ($quantity > 0) {
        // Prepare the item name and quantity for storage
        $parts_with_quantities[] = "$item_name:$quantity";
    }
}

// If there are parts with quantities, implode them and store in the database
if (!empty($parts_with_quantities)) {
    $parts_used_with_quantities = implode(",", $parts_with_quantities);
} else {
    // If no parts with quantities, set it to an empty string
    $parts_used_with_quantities = '';
}


    $parts_used_with_quantities = implode(",", $parts_with_quantities);
    $parts_used = isset($_POST['Parts_Used']) && is_array($_POST['Parts_Used']) ? implode(",", $_POST['Parts_Used']) : '';

   
    $item_cost = $_POST['Item_Cost'];
    $service_charge = $_POST['Service_Charge'];
    $total_cost = $_POST['Total_Cost'];
    // Add other fields if necessary

    $update_query = "UPDATE service 
                     SET Service_Date=?, Type_of_Service=?, Parts_Used=?, E_Time=?, Item_Cost=?, Service_Charge=?, Total_Cost=?
                     WHERE Service_Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "ssssdddi", $formatted_service_date, $type_of_service, $parts_used_with_quantities, $end_time, $item_cost, $service_charge, $total_cost, $edit_id);
    mysqli_stmt_execute($stmt);

    $_SESSION['success'] = "Service updated successfully!";
    header("Location: meservice.php");
    exit();
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

    function calculateTotalCost() {
        var itemCost = parseFloat(document.getElementById('Item_Cost').value) || 0;
        var serviceCharge = parseFloat(document.getElementById('Service_Charge').value) || 0;
        var totalCost = itemCost + serviceCharge;

        document.getElementById('Total_Cost').value = totalCost.toFixed(2);
    }

    // Form validation function
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

    // Function to update item cost
    function updateItemCost(checkbox) {
    var selectedItems = document.querySelectorAll('input[name="Parts_Used[]"]:checked');
    var totalItemCost = 0;

    selectedItems.forEach(function(item) {
        var itemName = item.value;
        var quantity = document.getElementById('Quantity_' + itemName).value || 1; // Default quantity is 1 if not provided

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

function fetchServiceCharge() {
        var selectedService = document.getElementById('Type_of_Service').value;
        var serviceOptions = document.getElementById('Type_of_Service').options;
        var selectedServiceCharge = 0;

        // Loop through all selected options to get the total service charge
        for (var i = 0; i < serviceOptions.length; i++) {
            if (serviceOptions[i].selected) {
                selectedServiceCharge += parseFloat(serviceOptions[i].getAttribute('data-service-charge'));
            }
        }

        // Populate the total service charge into the Service_Charge input field
        document.getElementById('Service_Charge').value = selectedServiceCharge.toFixed(2);
    }

</script>

</head>

<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid center-form">
                    <div class="form-container">
                        <h1 class="h3 mb-2 text-gray-800">Edit Service Details</h1>

                        <!-- Edit Service Form -->
                        <form action="meservice_edit.php" method="post">

                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                            <div class="form-group">
                                <label for="Service_Date">Service Date:</label>
                                <input type="date" class="form-control" id="Service_Date" name="Service_Date" required min="<?php echo date('Y-m-d'); ?>" value="<?php echo $service_date; ?>">
                            </div>

                            <div class="form-group">
                                        <label for="Type_of_Service">Type of Service:</label><br>
                                        <select class="form-control" id="Type_of_Service" name="Type_of_Service[]" multiple onchange="fetchServiceCharge()" >
                                            <?php
                                            // Fetch service types from the Service Type table
                                            $serviceQuery = "SELECT Type_of_Service, Service_Charge FROM service_type";
                                            $serviceResult = mysqli_query($connect, $serviceQuery);

                                            if ($serviceResult && mysqli_num_rows($serviceResult) > 0) {
                                                while ($typeService = mysqli_fetch_assoc($serviceResult)) {
                                                    echo '<option value="' . $typeService['Type_of_Service'] . '" data-service-charge="' . $typeService['Service_Charge'] . '">' . $typeService['Type_of_Service'] . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>

                            <div class="form-group">
                                <label for="E_Time">End Time:</label>
                                <input type="time" class="form-control" id="E_Time" name="E_Time" required min="07:00" max="19:00" value="<?php echo $elapsed_time; ?>" required>
                            </div>

                            <!-- Add checkboxes for selecting items and input fields for quantities -->
                            <div class="form-group">
                                            <label for="Parts_Used">Parts Used:</label><br>
                                            <table>
                                                <thead>
                                                    <tr>
                                                        <th>Item</th>
                                                        <th>Quantity</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Fetch item names and in-stock quantities from the Item table
                                                    $itemQuery = "SELECT Item_Name, InStock FROM item";
                                                    $itemResult = mysqli_query($connect, $itemQuery);

                                                    if ($itemResult && mysqli_num_rows($itemResult) > 0) {
                                                        while ($item = mysqli_fetch_assoc($itemResult)) {
                                                            echo '<tr>';
                                                            echo '<td><input type="checkbox" name="Parts_Used[]" value="' . $item['Item_Name'] . '" onchange="updateItemCost(this)">' . $item['Item_Name'] . '</td>';

                                                             echo '<td><input type="number" name="Quantity[' . $item['Item_Name'] . ']" id="Quantity_' . $item['Item_Name'] . '" min="0" onchange="updateItemCost(this)"></td>';
                                                            echo '</tr>';

                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="form-group">
                                            <label for="Item_Cost">Item Cost:</label>
                                            <input type="text" class="form-control" id="Item_Cost" name="Item_Cost" required readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="Service_Charge">Service Charge:</label>
                                            <input type="text" class="form-control" id="Service_Charge" name="Service_Charge"  required readonly>
                                        </div>

                            <div class="form-group">
                                <label for="Total_Cost">Total Cost:</label>
                                <input type="text" class="form-control" id="Total_Cost" name="Total_Cost" value="<?php echo $total_cost; ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary" name="update_btn">Update Service</button>
                        </form>
                            <!-- Go Back Button -->
                            <a href="meservice.php" class="btn btn-secondary go-back-btn">Go Back</a>
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
