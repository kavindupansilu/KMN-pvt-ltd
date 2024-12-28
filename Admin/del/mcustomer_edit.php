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
        $query = "SELECT * FROM customer WHERE Cust_Id = ?";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $first_name = $row['first_name'];
            $last_name = $row['last_name'];
            $address = $row['address'];
            $phone = $row['phone'];
            $email = $row['email'];
            $referral = $row['referral'];
            $manager_id = $row['Manager_Id'];
        } else {
            echo "Error in the query: " . mysqli_error($connect);
        }
    }
}

if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $updated_first_name = $_POST['first_name'];
    $updated_last_name = $_POST['last_name'];
    $updated_address = $_POST['address'];
    $updated_phone = $_POST['phone'];
    $updated_email = $_POST['email'];
    $updated_referral = $_POST['referral'];
    $updated_manager_id = $_POST['Manager_Id'];

    $update_query = "UPDATE customer
                     SET first_name=?, last_name=?, address=?, phone=?, email=?, referral=?, Manager_Id=?
                     WHERE Cust_id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "sssssssi", $updated_first_name, $updated_last_name, $updated_address, $updated_phone, $updated_email, $updated_referral, $updated_manager_id,  $edit_id);
    mysqli_stmt_execute($stmt);

    header("Location: viewCustomer.php");
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
                        <h1 class="h3 mb-2 text-gray-800">Edit Customer Details</h1>

                        <!-- Edit Customer Form -->
                        <form action="mcustomer_edit.php" method="post">

                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                            <div class="form-group">
                                <label for="first_name">First Name:</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $first_name; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="last_name">Last Name:</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $last_name; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Address:</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone:</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $phone; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" >
                            </div>
                            <div class="form-group">
                                <label for="referral">Referral:</label>
                                <input type="text" class="form-control" id="referral" name="referral" value="<?php echo $referral; ?>" >
                            </div>
                            <div class="form-group">
                                        <label for="Manager_Id">Manager Id:</label>
                                        <input type="text" class="form-control" id="Manager_Id" name="Manager_Id" >
                                    </div>

                            <button type="submit" class="btn btn-primary" name="update_btn">Update Customer</button>
                        </form>

                        <!-- Go Back Button -->
                        <a href="customer.php" class="btn btn-secondary go-back-btn">Go Back</a>
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
