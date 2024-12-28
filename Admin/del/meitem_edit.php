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
        $query = "SELECT * FROM item WHERE Item_Id = ?";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $item_name = $row['Item_Name'];
            $brand = $row['Brand'];
            $quantity = $row['Quantity'];
            $instock = $row['InStock'];
            $item_cost = $row['Item_Cost'];
            $mechanic_id = $row['Mechanic_Id'];
        } else {
            echo "Error in the query: " . mysqli_error($connect);
        }
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $updated_item_name = $_POST['item_name'];
    $updated_brand = $_POST['brand'];
    $updated_quantity = $_POST['quantity'];
    $updated_instock = $_POST['instock'];
    $updated_item_cost = $_POST['item_cost'];
    $updated_mechanic_id = $_POST['mechanic_id'];


    $update_query = "UPDATE item 
                     SET Item_Name=?, Brand=?, Quantity=?, InStock=?, Item_Cost=?, Mechanic_Id=?
                     WHERE Item_Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "ssiidsi", $updated_item_name, $updated_brand, $updated_quantity, $updated_instock, $updated_item_cost, $updated_mechanic_id, $edit_id);
    mysqli_stmt_execute($stmt);

    header("Location: meitem.php");
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
                        <h1 class="h3 mb-2 text-gray-800">Edit Item Details</h1>

                        <!-- Edit Item Form -->
                        <form action="meitem_edit.php" method="post">

                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                            <div class="form-group">
                                <label for="item_name">Item Name:</label>
                                <input type="text" class="form-control" id="item_name" name="item_name" value="<?php echo $item_name; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="brand">Brand:</label>
                                <input type="text" class="form-control" id="brand" name="brand" value="<?php echo $brand; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <input type="text" class="form-control" id="quantity" name="quantity" value="<?php echo $quantity; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="instock">InStock:</label>
                                <input type="text" class="form-control" id="instock" name="instock" value="<?php echo $instock; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="item_cost">Item Cost:</label>
                                <input type="text" class="form-control" id="item_cost" name="item_cost" value="<?php echo $item_cost; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="mechanic_id">Mechanic Id:</label>
                                <input type="text" class="form-control" id="mechanic_id" name="mechanic_id" value="<?php echo $mechanic_id; ?>" required>
                            </div>
                          
                            <button type="submit" class="btn btn-primary" name="update_btn">Update Item</button>
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
