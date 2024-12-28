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
           // $ID = $row['Vehicle_Id'];
            $type = $row['Type'];
            $color = $row['Color'];
            $cid = $row['Cust_Id'];
        } else {
            echo "Error in the query: " . mysqli_error($connect);
        }
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
   // $updated_ID = $_POST['Vehicle_Id'];
    $updated_type = $_POST['Type'];
    $updated_color = $_POST['Color'];
    $updated_cid = $_POST['Cust_Id'];

    $update_query = "UPDATE vehicle 
                     SET Type=?, Color=?, Cust_Id=? 
                     WHERE Vehicle_Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "sssi", $updated_type, $updated_color, $updated_cid, $edit_id);
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

                        <!-- Edit Vehicle Form -->
                        <form action="vehicle_edit.php" method="post">

                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">

                            <!-- <div class="form-group">
                                <label for="Updated_Vehicle_id">Vehicle ID:</label>
                                <input type="text" class="form-control" id="Updated_Vehicle_id" name="Updated_Vehicle_id" value="<?php echo $ID; ?>" required>
                            </div> -->
                             <!-- <div class="form-group">
                                    <label for="Type">Type:</label>
                                            <select class="form-control" id="Type" name="Type[]" required>
                                            <option value="Bike"<?php if (in_array("Bike", $type)) echo "selected"; ?>>Bike</option>
                                            <option value="Car"<?php if (in_array("Car", $type)) echo "selected"; ?>>Car</option>
                                            <option value="Jeep"<?php if (in_array("Jeep", $type)) echo "selected"; ?>>Jeep</option>
                                            <option value="Truck"<?php if (in_array("Truck", $type)) echo "selected"; ?>>Truck</option>
                                            <option value="Bus"<?php if (in_array("Bus", $type)) echo "selected"; ?>>Bus</option>
                                            <option value="Van"<?php if (in_array("Van", $type)) echo "selected"; ?>>Van</option>
                                            <option value="TukTuk"<?php if (in_array("TukTuk", $type)) echo "selected"; ?>>TukTuk</option>
                                            <option value="Lorry"<?php if (in_array("Lorry", $type)) echo "selected"; ?>>Lorry</option>
                                        </select>
                                    </div>  -->

                             <div class="form-group">
                                <label for="Updated_Type">Type:</label>
                                <input type="text" class="form-control" id="Type" name="Type" value="<?php echo $type; ?>" required>
                            </div>  
                            <div class="form-group">
                                <label for="Color">Color:</label>
                                <input type="text" class="form-control" id="Color" name="Color" value="<?php echo $color; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="Cust_Id">Customer Id:</label>
                                <input type="text" class="form-control" id="Cust_Id" name="Cust_Id" value="<?php echo $cid; ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary" name="update_btn">Update Vehicle</button>
                        </form> 
                        <!-- Go Back Button -->
                        <a href="vehicle.php" class="btn btn-secondary go-back-btn">Go Back</a>                       
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div style="width:100%;height:0;padding-bottom:100%;position:relative;"><iframe src="https://giphy.com/embed/ifSql72OuOHpCQSf1R" width="100%" height="80%" style="position:absolute" frameBorder="0" allowFullScreen></iframe>


    <?php
    include('Includes2/footer.php');
    mysqli_close($connect);
    ?>
</body>

</html>
