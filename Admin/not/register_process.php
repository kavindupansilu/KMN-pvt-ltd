<?php
// Assuming you have a database connection
include('db_connection.php');

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];

    $query = "INSERT INTO managers (username, password, email) VALUES ('$username', '$password', '$email')";

    if (mysqli_query($connect, $query)) {
        echo "Registration successful!";
    } else {
        echo "Error: " . mysqli_error($connect);
    }

    mysqli_close($connect);
}
?>
