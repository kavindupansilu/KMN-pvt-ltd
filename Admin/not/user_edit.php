<?php
session_start();
include('Includes2/header.php');
//include('Includes2/navbar.php');

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Initialize $edit_id
$edit_id = '';

// Check if the form is submitted and set $username, $phone, $email, $password
if (isset($_POST['edit_btn'])) {
    $edit_id = isset($_POST['edit_id']) ? $_POST['edit_id'] : '';

    if ($edit_id) {
        $query = "SELECT * FROM user WHERE User_Id = ?";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "i", $edit_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $password = $row['Password'];
            $username = $row['Username'];
            $phone = $row['Phone'];
            $email = $row['Email'];
        } else {
            echo "Error in the query: " . mysqli_error($connect);
        }
    }
}

// Handle the form submission for editing
if (isset($_POST['update_btn'])) {
    $edit_id = $_POST['edit_id'];
    $updated_username = $_POST['Updated_Username'];
    $updated_phone = $_POST['Updated_Phone'];
    $updated_email = $_POST['Updated_Email'];
    $updated_password = $_POST['Updated_Password'];

    $update_query = "UPDATE user 
                     SET Username=?, Phone=?, Email=?, Password=? 
                     WHERE User_Id=?";
    $stmt = mysqli_prepare($connect, $update_query);
    mysqli_stmt_bind_param($stmt, "ssssi", $updated_username, $updated_phone, $updated_email, $updated_password, $edit_id);
    mysqli_stmt_execute($stmt);

    header("Location: user.php"); // Redirect to the user page after update
    exit(); // Add exit to prevent further execution
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
                        <h1 class="h3 mb-2 text-gray-800">Edit User Details</h1>

                        <!-- Edit User Form -->
                        <form action="user_edit.php" method="post">

                            <input type="hidden" name="edit_id" value="<?php echo $edit_id; ?>">
                            <div class="form-group">
                                <label for="Updated_Username">Username:</label>
                                <input type="text" class="form-control" id="Updated_Username" name="Updated_Username" value="<?php echo $username; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="Updated_Phone">Phone:</label>
                                <input type="text" class="form-control" id="Updated_Phone" name="Updated_Phone" value="<?php echo $phone; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="Updated_Email">Email:</label>
                                <input type="email" class="form-control" id="Updated_Email" name="Updated_Email" value="<?php echo $email; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="Updated_Password">Password:</label>
                                <input type="password" class="form-control" id="Updated_Password" name="Updated_Password" value="<?php echo $password; ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary" name="update_btn">Update User</button>
                        </form>

                       
                    </div>
                </div>
            </div>
        </div>
    </div>

   
     
</body>

</html>

<?php
include('Includes2/footer.php');
mysqli_close($connect);
?>
