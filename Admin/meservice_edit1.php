<?php
session_start();
include('Includes2/header.php');

require "dbh.inc.php";
// $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

$edit_id = '';

if (isset($_POST['edit_btn'])) {
    $edit_id = $_POST['edit_id'];

    $query = "SELECT * FROM service WHERE Service_Id = '$edit_id'";
    $query_run = mysqli_query($connect, $query);

    if ($query_run) {
        $row = mysqli_fetch_assoc($query_run);
        $service_date = $row['Service_Date'];
        $type_of_service = explode(",", $row['Type_of_Service']); // explode the string to get an array of selected services
        $parts_used = explode(",", $row['Parts_Used']); // explode the string to get an array of selected parts used
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
    $type_of_service = implode(",", $_POST['Type_of_Service']);
    $parts_used = isset($_POST['Parts_Used']) && is_array($_POST['Parts_Used']) ? implode(",", $_POST['Parts_Used']) : '';
    $elapsed_time = $_POST['E_Time'];
    $item_cost = $_POST['Item_Cost'];
    $service_charge = $_POST['Service_Charge'];
    $total_cost = $_POST['Total_Cost'];
    // Add other fields if necessary

    $update_query = "UPDATE service 
                     SET Service_Date=?, Type_of_Service=?, Parts_Used=?, `E_Time`=?, Item_Cost=?, Service_Charge=?, Total_Cost=?
                     WHERE Service_Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "ssssdddi", $formatted_service_date, $type_of_service, $parts_used, $elapsed_time, $item_cost, $service_charge, $total_cost, $edit_id);
    mysqli_stmt_execute($stmt);

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
                                <label for="Type_of_Service">Type of Service:</label>
                                <select class="form-control" id="Type_of_Service" name="Type_of_Service[]" multiple required>
                                    <!-- Add options dynamically from your database or define static options -->
                                    <option value="Oil Change" <?php if (in_array("Oil Change", $type_of_service)) echo "selected"; ?>>Oil Change</option>
                                    <option value="Tire Rotation"<?php if (in_array("Tire Rotation", $type_of_service)) echo "selected"; ?>>Tire Rotation</option>
                                    <option value="Fluid Checks and Replacements"<?php if (in_array("Fluid Checks and Replacements", $type_of_service)) echo "selected"; ?>>Fluid Checks and Replacements</option>
                                    <option value="Air Filter Replacement" <?php if (in_array("Air Filter Replacement", $type_of_service)) echo "selected"; ?>>Air Filter Replacement</option>
                                    <option value="Cabin Air Filter Replacement" <?php if (in_array("Cabin Air Filter Replacement", $type_of_service)) echo "selected"; ?>>Cabin Air Filter Replacement</option>
                                    <option value="Engine Diagnostics" <?php if (in_array("Engine Diagnostics", $type_of_service)) echo "selected"; ?>>Engine Diagnostics</option>
                                    <option value="Computerized Diagnostics" <?php if (in_array("Computerized Diagnostics", $type_of_service)) echo "selected"; ?>>Computerized Diagnostics</option>
                                    <option value="Brake System Repair" <?php if (in_array("Brake System Repair", $type_of_service)) echo "selected"; ?>>Brake System Repair</option>
                                    <option value="Suspension and Steering Repair" <?php if (in_array("uspension and Steering Repair", $type_of_service)) echo "selected"; ?>>Suspension and Steering Repair</option>
                                    <option value="Engine Repair" <?php if (in_array("Engine Repair", $type_of_service)) echo "selected"; ?>>Engine Repair</option>
                                    <option value="Transmission Repair" <?php if (in_array("Transmission Repair", $type_of_service)) echo "selected"; ?>>Transmission Repair</option>
                                    <option value="Electrical System Repair: " <?php if (in_array("Electrical System Repair", $type_of_service)) echo "selected"; ?>>Electrical System Repair: </option>
                                    <option value="Wheel Alignment" <?php if (in_array("Wheel Alignment", $type_of_service)) echo "selected"; ?>>Wheel Alignment</option>
                                    <option value="Air Conditioning Service" <?php if (in_array("Air Conditioning Service", $type_of_service)) echo "selected"; ?>>Air Conditioning Service</option>
                                    <option value="Heating System Service" <?php if (in_array("Heating System Service", $type_of_service)) echo "selected"; ?>>Heating System Service</option>
                                    <option value="Emission System Service" <?php if (in_array("Emission System Service", $type_of_service)) echo "selected"; ?>>Emission System Service</option>
                                    <option value="Performance Upgrades" <?php if (in_array("Performance Upgrades", $type_of_service)) echo "selected"; ?>>Performance Upgrades</option>
                                    <option value="Interior and Exterior Detailing" <?php if (in_array("Interior and Exterior Detailing", $type_of_service)) echo "selected"; ?>>Interior and Exterior Detailing</option>
                                    <option value="Paint and Bodywork" <?php if (in_array("Paint and Bodywork", $type_of_service)) echo "selected"; ?>>Paint and Bodywork</option>
                                            
                                    <!-- Add more options as needed -->
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="E_Time">End Time:</label>
                                <input type="time" class="form-control" id="E_Time" name="E_Time" required min="07:00" max="19:00" value="<?php echo $elapsed_time; ?>" required>
                            </div>

                            <div class="form-group">
                                            <label for="Parts_Used">Parts Used:</label><br>

                                            <?php
                                            // Fetch item names and costs from the Item table
                                            $itemQuery = "SELECT Item_Name, Item_Cost FROM item";
                                            $itemResult = mysqli_query($connect, $itemQuery);

                                            if ($itemResult && mysqli_num_rows($itemResult) > 0) {
                                                while ($item = mysqli_fetch_assoc($itemResult)) {
                                                    echo '<input type="checkbox" id="Parts_Used_' . $item['Item_Name'] . '" name="Parts_Used[]" value="' . $item['Item_Name'] . '" onchange="updateItemCost(this)">';
                                                    if (in_array($item['Item_Name'], $parts_used)) echo " checked";
                                                    echo '<label for="Parts_Used_' . $item['Item_Name'] . '"> ' . $item['Item_Name'] . '</label>';
                                                    echo '<input type="number" class="form-control" id="Quantity_' . $item['Item_Name'] . '" name="Quantity[' . $item['Item_Name'] . ']" placeholder="Quantity">';
                                                    echo '<br>';
                                                }
                                            }
                                            ?>

                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="Item_Cost">Item Cost:</label>
                                            <input type="text" class="form-control" id="Item_Cost" name="Item_Cost" required readonly>
                                        </div>

                                        <div class="form-group">
                                            <label for="Service_Charge">Service Charge:</label>
                                            <input type="text" class="form-control" id="Service_Charge" name="Service_Charge" required onchange="calculateTotalCost()">
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
