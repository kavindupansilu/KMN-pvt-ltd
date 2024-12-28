<?php
// Include your database connection file (config.php or dbh.inc.php)
require 'config.php';

// Check if the selected service type is sent via POST
if(isset($_POST['serviceType'])) {
    $serviceType = $_POST['serviceType'];

    // Query the database to fetch the service charge based on the selected service type
    $query = "SELECT Service_Charge FROM service_type WHERE Type_of_Service = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "s", $serviceType);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $serviceCharge);
    
    // Fetch the result
    mysqli_stmt_fetch($stmt);
    
    // Return the service charge
    echo $serviceCharge;

    // Close the statement
    mysqli_stmt_close($stmt);
} else {
    // Handle if service type is not provided
    echo "Error: Service type not provided.";
}
?>
