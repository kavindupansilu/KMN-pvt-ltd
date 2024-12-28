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
        $elapsed_time = $row['S_Time'];
        $elapsed_time = $row['E_Time'];
        $total_cost = $row['Total_Cost'];
        // Add other fields if necessary
    } else {
        echo "Error in the query: " . mysqli_error($connect);
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $service_date = $_POST['service_date'];
    $type_of_service = $_POST['type_of_service'];
    $parts_used = $_POST['parts_used'];
    $elapsed_time = $_POST['elapsed_time'];
    $total_cost = $_POST['total_cost'];
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
                                <label for="service_date">Service Date:</label>
                                <input type="text" class="form-control" id="service_date" name="service_date" value="<?php echo $service_date; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="type_of_service">Type of Service:</label>
                                <input type="text" class="form-control" id="type_of_service" name="type_of_service" value="<?php echo $type_of_service; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="parts_used">Parts Used:</label>
                                <input type="text" class="form-control" id="parts_used" name="parts_used" value="<?php echo $parts_used; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="elapsed_time">Elapsed Time:</label>
                                <input type="text" class="form-control" id="elapsed_time" name="elapsed_time" value="<?php echo $elapsed_time; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="total_cost">Total Cost:</label>
                                <input type="text" class="form-control" id="total_cost" name="total_cost" value="<?php echo $total_cost; ?>" required>
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
