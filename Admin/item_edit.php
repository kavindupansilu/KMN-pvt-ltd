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
        $query = "SELECT * FROM item WHERE Item_Id = ?";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $item_name = $row['Item_Name'];
            $brand = explode(",", $row['Brand']); // explode the string to get an array of selected Brand
            $quantity = $row['Quantity'];
            $instock = $row['InStock'];
            $item_cost = $row['Item_Cost'];
        } else {
            echo "Error in the query: " . mysqli_error($connect);
        }
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $updated_item_name = $_POST['item_name'];    
    $brand = implode(",", $_POST['brand']);
    $updated_quantity = $_POST['quantity'];
    $updated_instock = $_POST['instock'];
    $updated_item_cost = $_POST['item_cost'];

    // Validation checks
    if (!ctype_digit($updated_quantity) || !ctype_digit($updated_instock) || $updated_quantity <= 0 || $updated_instock <= 0) {
        $_SESSION['error'] = "Quantity and InStock must be positive integers.";
        header("Location: item_edit.php");
        exit();
    }

    if ($updated_instock > $updated_quantity) {
        $_SESSION['error'] = "InStock cannot be greater than Quantity.";
        header("Location: item_edit.php");
        exit();
    }

    if (!is_numeric($updated_item_cost) || $updated_item_cost < 0.01) {
        $_SESSION['error'] = "Item Cost must be a positive number with up to two decimal places.";
        header("Location: item_edit.php");
        exit();
    }

    // Update the item details
    $update_query = "UPDATE item 
                     SET Item_Name=?, Brand=?, Quantity=?, InStock=?, Item_Cost=?
                     WHERE Item_Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "ssiidi", $updated_item_name, $brand, $updated_quantity, $updated_instock, $updated_item_cost, $edit_id);
    mysqli_stmt_execute($stmt);

    header("Location: item.php");
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
                        <form action="item_edit.php" method="post">

                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                            <div class="form-group">
                                <label for="item_name">Item Name:</label>
                                <input type="text" class="form-control" id="item_name" name="item_name" value="<?php echo $item_name; ?>" required>
                            </div>
                            <div class="form-group">
                                    <label for="brand">Brand:</label>
                                            <select class="form-control" id="brand" name="brand[]" required>
                                            <option value="">Select Item Brand</option>
                                            <option value="Honda"<?php if (in_array("Honda", $brand)) echo " selected"; ?>>Honda</option>
                                            <option value="Toyota"<?php if (in_array("Toyota", $brand)) echo " selected"; ?>>Toyota</option>
                                            <option value="Suzuki"<?php if (in_array("Suzuki", $brand)) echo " selected"; ?>>Suzuki</option>
                                            <option value="CEAT"<?php if (in_array("CEAT", $brand)) echo " selected"; ?>>CEAT</option>
                                            <option value="Mobil"<?php if (in_array("Mobil", $brand)) echo " selected"; ?>>Mobil</option>
                                        </select>
                                    </div>
                            <div class="form-group">
                                <label for="quantity">Quantity:</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo $quantity; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="instock">InStock:</label>
                                <input type="number" class="form-control" id="instock" name="instock" value="<?php echo $instock; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="item_cost">Item Cost:</label>
                                <input type="number" step="0.01" class="form-control" id="item_cost" name="item_cost" value="<?php echo $item_cost; ?>" required>
                            </div>
                          
                            <button type="submit" class="btn btn-primary" name="update_btn">Update Item</button>
                        </form>
                            <!-- Go Back Button -->
                            <a href="item.php" class="btn btn-secondary go-back-btn">Go Back</a>
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
