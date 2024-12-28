<?php
// Include the database connection file
require_once "dbh.inc.php";

// Check if the item name is provided in the GET request
if (isset($_GET['item'])) {
    // Sanitize the input to prevent SQL injection
    $item_name = mysqli_real_escape_string($connect, $_GET['item']);

    // Query to retrieve the item cost from the database based on the item name
    $query = "SELECT Item_Cost FROM item WHERE Item_Name = '$item_name'";

    // Execute the query
    $result = mysqli_query($connect, $query);

    if ($result) {
        // Check if the query returned any rows
        if (mysqli_num_rows($result) > 0) {
            // Fetch the item cost from the result
            $row = mysqli_fetch_assoc($result);
            $item_cost = $row['Item_Cost'];

            // Return the item cost as the response
            echo $item_cost;
        } else {
            // If no rows were returned, indicate that the item cost is not available
            echo "Item cost not available";
        }
    } else {
        // If an error occurred while executing the query, return an error message
        echo "Error: " . mysqli_error($connect);
    }

    // Close the database connection
    mysqli_close($connect);
} else {
    // If the item name is not provided in the GET request, return an error message
    echo "Error: Item name not provided";
}
?>
