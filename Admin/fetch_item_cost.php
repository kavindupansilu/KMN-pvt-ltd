<?php
// Include your database connection or configuration file
require_once 'config.php';

// Check if the item ID is provided via POST request
if (isset($_POST['itemId'])) {
    $itemId = $_POST['itemId'];

    // Prepare and execute the query to fetch item cost based on the item ID
    $query = "SELECT Item_Cost FROM item WHERE Item_Id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $itemId);
    $stmt->execute();
    $stmt->bind_result($itemCost);
    $stmt->fetch();
    $stmt->close();

    // Return the fetched item cost
    echo $itemCost;
} else {
    // If item ID is not provided, return an error message
    echo 'Error: Item ID not provided.';
}
?>
