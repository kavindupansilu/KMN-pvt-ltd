<?php
session_start();
include('Includes2/header.php');

require "dbh.inc.php";
// $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

//$edit_id = '';

if (isset($_POST['edit_btn'])) {
    $edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : '';

    if ($edit_id) {
        $query = "SELECT * FROM vehicle WHERE Vehicle_Id = ?";
        $stmt = mysqli_prepare($connect, $query);
        // Bind the edit_id variable to the prepared statement,
        // specifying "s" to indicate that it's a string data type.
        // This ensures proper formatting and escaping of the value
        // to prevent SQL injection attacks and ensure compatibility
        // with string data types in the SQL query.
        mysqli_stmt_bind_param($stmt, "s", $edit_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);        
            $type = explode(",", $row['Type']);// explode the string to get an array of selected type
            $color = $row['Color'];
        } else {
            echo "Error: Vehicle not found";
            exit; // Exit script if vehicle not found
        }
    }
}
        // Edit only selected Vehicle Id
if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $type = implode(",", $_POST['Type']);
    $updated_color = $_POST['Color'];

    $update_query = "UPDATE vehicle 
                     SET Type=?, Color=?
                     WHERE Vehicle_Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "sss", $type, $updated_color, $edit_id);
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

                            <div class="form-group">
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
                                    </div>
                            <div class="form-group">
                                <label for="Color">Color:</label>
                                <input type="text" class="form-control" id="Color" name="Color" value="<?php echo $color; ?>" required>
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

    <?php
    include('Includes2/footer.php');
    mysqli_close($connect);
    ?>
</body>

</html>
