<?php
session_start();
include('Includes2/header.php');

require "dbh.inc.php";
// $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

$edit_id = '';
$sdate = '';
$psales = '';
$srevenue = '';

if (isset($_POST['edit_btn'])) {
    $edit_id = $_POST['edit_id'];

    $query = "SELECT * FROM sales WHERE Sales_Id  = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "i", $edit_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $sdate = $row['Sales_Date'];
        $psales = $row['Parts_Sales'];
    } else {
        echo "Error in the query: " . mysqli_error($connect);
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $sdate = $_POST['Sales_Date'];
    $psales = isset($_POST['Parts_Sales']) && is_array($_POST['Parts_Sales']) ? implode(",", $_POST['Parts_Sales']) : '';
    $srevenue = 0;  // Initialize to 0


    $update_query = "UPDATE sales 
                     SET Sales_Date=?, Parts_Sales=?
                     WHERE Sales_Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "ssi", $sdate, $psales, $edit_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: dailysales.php");
        exit();
    } else {
        echo "Error updating daily sales: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
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
                        <h1 class="h3 mb-2 text-gray-800">Edit Daily Sales Details</h1>

                        <!-- Edit Daily Sales Form -->
                        <form action="dailysales_edit.php" method="post">
                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                            <div class="form-group">
                                <label for="Sales_Date">Sales Date:</label>
                                <input type="text" class="form-control" id="Sales_Date" name="Sales_Date" value="<?php echo $sdate; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="Parts_Sales">Parts Sales:</label><br>

                                <?php
                                // Fetch item names from the Item table
                                $itemQuery = "SELECT Item_Name FROM item";
                                $itemResult = mysqli_query($connect, $itemQuery);

                                if ($itemResult && mysqli_num_rows($itemResult) > 0) {
                                    while ($item = mysqli_fetch_assoc($itemResult)) {
                                        $isChecked = in_array($item['Item_Name'], explode(",", $psales)) ? 'checked' : '';
                                        echo '<input type="checkbox" id="Parts_Sales_' . $item['Item_Name'] . '" name="Parts_Sales[]" value="' . $item['Item_Name'] . '" ' . $isChecked . '>';
                                        echo '<label for="Parts_Sales_' . $item['Item_Name'] . '"> ' . $item['Item_Name'] . '</label><br>';
                                    }
                                }
                                ?>
                            </div>

                            <button type="submit" class="btn btn-primary" name="update_btn">Update Daily Sales</button>
                        </form>
                        <!-- Go Back Button -->
                        <a href="dailysales.php" class="btn btn-secondary go-back-btn">Go Back</a>

                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
include('Includes2/footer.php');
mysqli_close($connect);
?>
