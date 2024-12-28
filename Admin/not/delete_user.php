<?php
session_start();
include('Includes2/header.php');
//include('Includes2/navbar.php');

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['delete_btn'])) {
    $delete_id = $_POST['delete_id'];
    $query = "DELETE FROM user WHERE User_Id='$delete_id'";
    $query_run = mysqli_query($connect, $query);

    if ($query_run) {
        $_SESSION['success'] = "User deleted successfully!";
        header('Location: user.php');
    } else {
        $_SESSION['status'] = "Error deleting user!";
        header('Location: user.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
</head>

<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Delete User</h1>

                    <!-- Delete User Form -->
                    <form action="" method="post">
                        <input type="hidden" name="delete_id" value="<?php echo $delete_id; ?>">
                        <p>Are you sure you want to delete this user?</p>
                        <button type="submit" class="btn btn-danger" name="delete_btn">Delete User</button>
                    </form>
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
