<?php
session_start();
include('Includes2/header.php');

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

$edit_id = '';

if (isset($_POST['edit_btn'])) {
    $edit_id = $_POST['edit_id'];

    $query = "SELECT * FROM service WHERE Service_Id   = '$edit_id'";
    $query_run = mysqli_query($connect, $query);

    if ($query_run) {
        $row = mysqli_fetch_assoc($query_run);
        $service_date = $row['Service_Date'];
        $type_of_service = $row['Type_of_Service'];
        $parts_used = $row['Parts_Used'];
        // $elapsed_time = $row['S_Time'];
        $elapsed_time = $row['E_Time'];
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
    $total_cost = $_POST['Total_Cost'];
    // Add other fields if necessary

    $update_query = "UPDATE service 
                     SET Service_Date=?, Type_of_service=?, Parts_used=?, `E_Time`=?, Total_Cost=?
                     WHERE Service_Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "sssssi", $service_date, $type_of_service, $parts_used, $elapsed_time, $total_cost, $edit_id);
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
                                            <input type="date" class="form-control" id="Service_Date" name="Service_Date" required min="<?php echo date('Y-m-d'); ?>">


                                        </div>

                                        <div class="form-group">
                                            <label for="Type_of_Service">Type of Service:</label>
                                            <select class="form-control" id="Type_of_Service" name="Type_of_Service[]" multiple required>
                                                <!-- Add options dynamically from your database or define static options -->
                                                <option value="car wash">car wash</option>
                                                <option value="body wash">body wash</option>
                                                <option value="body color">body color</option>
                                                <!-- Add more options as needed -->
                                            </select>
                                        </div>

                                        <div class="form-group">
                                        <label for="Parts_Sales">Parts Sales:</label><br>

                                        <?php
                                        // Fetch item names from the Item table
                                        $itemQuery = "SELECT Item_Name FROM item";
                                        $itemResult = mysqli_query($connect, $itemQuery);

                                        if ($itemResult && mysqli_num_rows($itemResult) > 0) {
                                            while ($item = mysqli_fetch_assoc($itemResult)) {
                                                echo '<input type="checkbox" id="Parts_Used' . $item['Item_Name'] . '" name="Parts_Used[]" value="' . $item['Item_Name'] . '">';
                                                echo '<label for="Parts_Used' . $item['Item_Name'] . '"> ' . $item['Item_Name'] . '</label><br>';
                                            }
                                        }
                                        ?>

                                    </div>

                                    <div class="form-group">
                                            <label for="E_Time">End Time:</label>
                                            <input type="time" class="form-control" id="E_Time" name="E_Time" required min="07:00" max="19:00" value="<?php echo $elapsed_time; ?>" required>
                                        </div>

                                        <div class="form-group">
                                            <label for="Total_Cost">Total Cost:</label>
                                            <input type="text" class="form-control" id="Total_Cost" name="Total_Cost" value="<?php echo $total_cost; ?>" required>
                                        </div>

                            <button type="submit" class="btn btn-primary" name="update_btn">Update Service</button>
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
