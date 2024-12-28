<?php
// Include database connection code
include('dbh.inc.php');

// Fetch data from the database
$query = "SELECT * FROM service";
$result = mysqli_query($connect, $query);

// Prepare an array to hold the data
$data = array();
while ($row = mysqli_fetch_assoc($result)) {
    $data[] = $row;
}

// Close database connection
mysqli_close($connect);

// Return data in JSON format
echo json_encode(array("data" => $data));
?>
