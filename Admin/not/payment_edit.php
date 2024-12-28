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

    $query = "SELECT * FROM payment WHERE Payment_Id = '$edit_id'";
    $query_run = mysqli_query($connect, $query);

    if ($query_run) {
        $row = mysqli_fetch_assoc($query_run);
        $date = $row['Date'];
        $item_cost = $row['Item_Cost'];
        $service_charge = $row['Service_Charge'];
        $total_cost = $row['Total_Cost'];
        $service_id = $row['Service_Id'];
        $cust_id = $row['Cust_Id'];
    } else {
        echo "Error in the query: " . mysqli_error($connect);
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $date = $_POST['Date'];
    $formatted_payment_date = date('Y-m-d', strtotime($date));
    $item_cost = $_POST['Item_Cost'];
    $service_charge = $_POST['Service_Charge'];
    $total_cost = $_POST['Total_Cost'];
    $service_id = $_POST['Service_Id'];
    $cust_id = $_POST['Cust_Id'];

    // Perform any additional validation if needed before updating the database

    $update_query = "UPDATE payment 
                     SET Date='$date', Item_Cost='$item_cost', Service_Charge='$service_charge', Total_Cost='$total_cost', Service_Id='$service_id', Cust_Id='$cust_id' 
                     WHERE Payment_Id='$edit_id'";
    mysqli_query($connect, $update_query);

    header("Location: payment.php");
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
        function showErrorMessage(fieldId, message) {
            document.getElementById(fieldId + '-error').innerText = message;
        }

        function calculateTotalCost() {
            var itemCost = parseFloat(document.getElementById('Item_Cost').value) || 0;
            var serviceCharge = parseFloat(document.getElementById('Service_Charge').value) || 0;
            var totalCost = itemCost + serviceCharge;

            document.getElementById('Total_Cost').value = totalCost.toFixed(2);
        }

        function validateForm() {
            // Add any additional form validation logic here
            return true; // Allow form submission
        }
    </script>
</head>

<body id="page-top" onload="calculateTotalCost()">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid center-form">
                    <div class="form-container">
                        <h1 class="h3 mb-2 text-gray-800">Edit Payment Details</h1>

                        <!-- Edit Payment Form -->
                        <form action="payment_edit.php" method="post" onsubmit="return validateForm()">

                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

                            <div class="form-group">
                                            <label for="Date">Date:</label>
                                            <input type="date" class="form-control" id="Date" name="Date" required min="<?php echo date('Y-m-d'); ?>">
                                        </div>

                            <div class="form-group">
                                <label for="Item_Cost">Item Cost:</label>
                                <input type="text" class="form-control" id="Item_Cost" name="Item_Cost" value="<?php echo $item_cost; ?>" required oninput="calculateTotalCost()">
                            </div>

                            <div class="form-group">
                                <label for="Service_Charge">Service Charge:</label>
                                <input type="text" class="form-control" id="Service_Charge" name="Service_Charge" value="<?php echo $service_charge; ?>" required oninput="calculateTotalCost()">
                            </div>
                            <div class="form-group">
                                <label for="Total_Cost">Total Cost:</label>
                                <input type="text" class="form-control" id="Total_Cost" name="Total_Cost" value="<?php echo $total_cost; ?>" readonly>
                            </div>

                            <div class="form-group">
                                <label for="Service_Id">Service Id:</label>
                                <input type="text" class="form-control" id="Service_Id" name="Service_Id" value="<?php echo $service_id; ?>" required>
                            </div>

                            <div class="form-group">
                                <label for="Cust_Id">Customer Id:</label>
                                <input type="text" class="form-control" id="Cust_Id" name="Cust_Id" value="<?php echo $cust_id; ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary" name="update_btn">Update Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include('Includes2/footer.php'); ?>
    <?php mysqli_close($connect); ?>
</body>

</html>
