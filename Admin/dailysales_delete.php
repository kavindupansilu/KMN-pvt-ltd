<?php
session_start();
include('Includes2/header.php');
//include('Includes2/navbar.php');

require "dbh.inc.php";
// $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['delete_btn'])) {
    $delete_id = $_POST['delete_id'];

    $delete_query = "DELETE FROM sales  WHERE Sales_Id ='$delete_id'";
    mysqli_query($connect, $delete_query);

    header("Location: dailysales.php"); // Redirect to the customer page after deletion
}

?>

<!-- HTML content if needed -->

<?php
include('Includes2/footer.php');
mysqli_close($connect);
?>
