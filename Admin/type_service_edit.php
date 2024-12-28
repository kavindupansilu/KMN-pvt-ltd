<?php
session_start();
include('Includes2/header.php');
require "dbh.inc.php";
//$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

$edit_id = '';

if (isset($_POST['edit_btn'])) {
    $edit_id = $_POST['edit_id'];

    $query = "SELECT * FROM service_type WHERE T_Service_Id = '$edit_id'";
    $query_run = mysqli_query($connect, $query);

    if ($query_run) {
        $row = mysqli_fetch_assoc($query_run);
    $type_of_service = $row['Type_of_Service'];
    $service_charge = $row['Service_Charge'];
} else {
    echo "Error in the query: " . mysqli_error($connect);
}
}

 if (isset($_POST['update_btn'])) {
     $edit_id = $_POST['edit_id'];
     $type_of_service = $_POST['Type_of_Service'];
     $service_charge = $_POST['Service_Charge'];

    $update_query = "UPDATE service_type 
                      SET Type_of_Service=?, Service_Charge=?
                      WHERE T_Service_Id=?";
     $stmt = mysqli_prepare($connect, $update_query);
     mysqli_stmt_bind_param($stmt, "sdi", $type_of_service, $service_charge, $edit_id);
     mysqli_stmt_execute($stmt);

     header("Location: type_service.php");
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

</head>

<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid center-form">
                    <div class="form-container">
                        <h1 class="h3 mb-2 text-gray-800">Edit Service Details</h1>

                        <!-- Edit Service Form -->
                        <form action="type_service_edit.php" method="post">

                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">                                
                                    <div class="form-group">
                                            <label for="Type_of_Service">Type of Service:</label>
                                            <input type="text" class="form-control" id="Type_of_Service" name="Type_of_Service" value="<?php echo $type_of_service; ?>" required>

                                    </div>

                                        <div class="form-group">
                                            <label for="Service_Charge">Service Charge:</label>
                                            <input type="text" class="form-control" id="Service_Charge" name="Service_Charge" required>

                                        </div>

                                        <button type="submit" class="btn btn-primary" name="update_btn">Update Service</button>
                        </form>
                        <!-- Go Back Button -->
                        <a href="type_service.php" class="btn btn-secondary go-back-btn">Go Back</a>
                   
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
include('Includes2/footer.php');
mysqli_close($connect);
?>
