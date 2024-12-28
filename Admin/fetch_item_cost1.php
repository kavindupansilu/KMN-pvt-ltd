<?php
require "config.php"; // Include your database connection

if(isset($_POST['itemId'])) {
    $itemId = $_POST['itemId'];

    // Query to fetch item price based on itemId
    $query = "SELECT Item_Price FROM item WHERE Item_Id = $itemId";
    $result = mysqli_query($con, $query);

    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        echo $row['Item_Price'];
    } else {
        echo "0"; // Default price if item not found (you can handle this based on your requirements)
    }
}
?>
