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
        $query = "SELECT * FROM item_supplier WHERE Id = ?";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $item_id = $row['Item_Id'];
            $supplier_id = $row['Supplier_Id'];
        } else {
            echo "Error in the query: " . mysqli_error($connect);
        }
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $updated_item_id = $_POST['Item_Id'];
    $updated_supplier_id = $_POST['Supplier_Id'];

    $update_query = "UPDATE item_supplier 
                     SET Item_Id=?, Supplier_Id=?
                     WHERE Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "iii", $updated_item_id, $updated_supplier_id, $edit_id);
    mysqli_stmt_execute($stmt);

    header("Location: item_supplier.php");
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
                        <h1 class="h3 mb-2 text-gray-800">Edit Item_Supplier Details</h1>

                        <!-- Edit item_supplier Form -->
                        <form action="item_supplier_edit.php" method="post">

                        <div class="form-group">
                                        <label for="Item_Id">Item ID:</label>
                                        <input type="text" class="form-control" id="Item_Id" name="Item_Id" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="Supplier_Id">Supplier ID:</label>
                                        <input type="text" class="form-control" id="Supplier_Id" name="Supplier_Id" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="update_btn">Update Item-Supplier Relationship</button>
                                
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
</body>

</html>
