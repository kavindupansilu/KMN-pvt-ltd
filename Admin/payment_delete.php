<?php
session_start();
include('Includes2/header.php');

require "config.php";
// $con = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['delete_btn'])) {
    $delete_id = $_POST['delete_id'];

    $delete_query = "DELETE FROM invoice WHERE SID ='$delete_id'";
    mysqli_query($con, $delete_query);

    header("Location: payment.php"); // Redirect to the payment page after deletion
}

?>

<?php
include('Includes2/footer.php');
mysqli_close($con);
?>
