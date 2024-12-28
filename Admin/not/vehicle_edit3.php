<?php
session_start();
include('Includes2/header.php');

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

$edit_id = '';

if (isset($_POST['edit_btn'])) {
    $edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : '';

    if ($edit_id) {
        $query = "SELECT * FROM vehicle WHERE Vehicle_Id = ?";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $type = $row['Type'];
            $color = $row['Color'];
            $cust_id = $row['Cust_Id'];
        } else {
            echo "Error in the query: " . mysqli_error($connect);
        }
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $type = $_POST['type'];
    $color = $_POST['color'];
    $cust_id = $_POST['cust_id'];

    $update_query = "UPDATE vehicle
                     SET Type=?, Color=?, Cust_Id=? 
                     WHERE Vehicle_Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "sssi", $type, $color, $cust_id, $edit_id);
    mysqli_stmt_execute($stmt);

    header("Location: vehicle.php");
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
                        <h1 class="h3 mb-2 text-gray-800">Edit Vehicle Details</h1>

                        <!-- Edit Supplier Form -->
                        <form action="vehicle_edit.php" method="post">

                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                            <div class="form-group">
                                <label for="type">Type:</label>
                                <input type="text" class="form-control" id="type" name="type" value="<?php echo $type; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="color">Color:</label>
                                <input type="text" class="form-control" id="color" name="color" value="<?php echo $color; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="cust_id">Customer Id:</label>
                                <input type="text" class="form-control" id="cust_id" name="cust_id" value="<?php echo $cust_id; ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary" name="update_btn">Update Vehicle</button>
                        </form>

                       
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
