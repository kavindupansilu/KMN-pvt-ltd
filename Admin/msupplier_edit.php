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
    $edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : '';

    if ($edit_id) {
        $query = "SELECT * FROM supplier WHERE Supplier_Id = ?";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $supplier_name = $row['Supplier_Name'];
            $brand = explode(",", $row['Brand']); // explode the string to get an array of selected brand
            $contact = $row['Contact'];
            $manager_id = $row['Manager_Id'];
        } else {
            echo "Error in the query: " . mysqli_error($connect);
        }
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $updated_supplier_name = $_POST['supplier_name'];
    $brand = implode(",", $_POST['Brand']);
    $updated_contact = $_POST['contact'];
    $updated_manager_id = $_POST['manager_id'];

    $update_query = "UPDATE supplier 
                     SET Supplier_Name=?, Brand=?, Contact=?, Manager_Id=? 
                     WHERE Supplier_Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "ssssi", $updated_supplier_name, $brand, $updated_contact, $updated_manager_id, $edit_id);
    mysqli_stmt_execute($stmt);

    header("Location: supplier.php");
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
                        <h1 class="h3 mb-2 text-gray-800">Edit Supplier Details</h1>

                        <!-- Edit Supplier Form -->
                        <form action="supplier_edit.php" method="post">

                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                            <div class="form-group">
                                <label for="supplier_name">Supplier Name:</label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" value="<?php echo $supplier_name; ?>" required>
                            </div>
                            <!-- Select more than one -->
                            <!-- Use Ctrl to it -->                          
                            <div class="form-group">
                                    <label for="Brand">Brand:</label>                              
                                    <select class="form-control" id="Brand" name="Brand[]" required>
                                        <option value="Honda"<?php if (in_array("Honda", $brand)) echo " selected"; ?>>Honda</option>
                                        <option value="Toyota"<?php if (in_array("Toyota", $brand)) echo " selected"; ?>>Toyota</option>
                                        <option value="Suzuki"<?php if (in_array("Suzuki", $brand)) echo " selected"; ?>>Suzuki</option>
                                        <option value="CEAT"<?php if (in_array("CEAT", $brand)) echo " selected"; ?>>CEAT</option>
                                        <option value="Mobil"<?php if (in_array("Mobil", $brand)) echo " selected"; ?>>Mobil</option>
                                    </select>
                                </div>
                            <div class="form-group">
                                <label for="contact">Contact:</label>
                                <input type="text" class="form-control" id="contact" name="contact" value="<?php echo $contact; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="manager_id">Manager Id:</label>
                                <input type="text" class="form-control" id="manager_id" name="manager_id" value="<?php echo $manager_id; ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary" name="update_btn">Update Supplier</button>
                        </form>

                       
                            <!-- Go Back Button -->
                            <a href="msupplier.php" class="btn btn-secondary go-back-btn">Go Back</a>
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
