<?php
session_start();

$registration_success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle registration form submission
    $password = $_POST['Password'];
    $name = $_POST['Name'];
    $phone = $_POST['Phone'];
    $email = $_POST['Email'];

    // Validate and process registration
    if ($password && $name && $phone && $email) {
        require "dbh.inc.php";
        // $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

        if (!$connect) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Escape values to prevent SQL injection
        $password = mysqli_real_escape_string($connect, $password);
        $name = mysqli_real_escape_string($connect, $name);
        $phone = mysqli_real_escape_string($connect, $phone);
        $email = mysqli_real_escape_string($connect, $email);

            // Insert data into the 'mechanic' table
            $insert_mechanic_query = "INSERT INTO mechanic (Password, Name, Phone, Email) 
            VALUES ('$password', '$name', '$phone', '$email')";


        if (mysqli_query($connect, $insert_mechanic_query)) {
            // Get the last inserted ID
            $last_id = mysqli_insert_id($connect);

            // Generate User_Id
            $user_id = "Mechanic_" . $last_id. "_ID" ;

            // Update User_Id in the database
            $update_muser_id_query = "UPDATE mechanic SET User_Id ='$user_id' WHERE id = $last_id";
            mysqli_query($connect, $update_muser_id_query);

            $registration_success_message = "Mechanic registered successfully with User_Id: $user_id";
        } else {
            echo "Error: " . $insert_mechanic_query . "<br>" . mysqli_error($connect);
        }

        // Close the database connection
        mysqli_close($connect);
    } else {
        // Display an error message if any field is empty
        echo "Error: All fields are required!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<head>
    <title>Mechanic_ Registration</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="reg.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

    <!-- Add style for white box -->
    <style>
        .white-box {
            background-color: white;
            padding: 10px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        .red-text {
            color: green;
        }
    </style>

</head>

<body>
<div class="container">
        <h3>Mechanic_ Registration</h3>
    

    <form action="" method="post">
        <label for="Password" class="l1">Password:</label>
        <input type="password" name="Password" required><br>

        <label for="Name" class="l1">Name:</label>
        <input type="text" name="Name" required><br>

        <label for="Phone" class="l1">Phone:</label>
        <input type="text" name="Phone" required><br>

        <label for="Email" class="l1">Email:</label>
        <input type="email" name="Email" required><br>

        <button type="submit" name="register_btn" class="btn btn-primary">Register</button>
            <a href="mechanic_login.php" class="btn btn-warning">Go to Login</a>
    </form>
 <!-- Display registration success message in a white box with red text -->
        <div class="white-box">
            <p class="red-text"><?php echo $registration_success_message; ?></p>
        </div>    
</body>

</html>
