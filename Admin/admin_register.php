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

            // Insert data into the 'admin' table
            $insert_admin_query = "INSERT INTO admin (Password, Name, Phone, Email) 
            VALUES ('$password', '$name', '$phone', '$email')";


        if (mysqli_query($connect, $insert_admin_query)) {
            // Get the last inserted ID
            $last_id = mysqli_insert_id($connect);

            // Generate User_Id
            //$code = rand(1, 99);
            $user_id = "Admin_" . $last_id. "_ID" ;

            // Update User_Id in the database
            $update_user_id_query = "UPDATE admin SET User_Id ='$user_id' WHERE id = $last_id";
            mysqli_query($connect, $update_user_id_query);

            $registration_success_message = "Admin registered successfully with User_Id: $user_id";
        } else {
            echo "Error: " . $insert_admin_query . "<br>" . mysqli_error($connect);
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
    <title>Admin Registration</title>
    <link rel="icon" href="icon.png">

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

<script>
    function showErrorMessage(fieldId, message) {
            document.getElementById(fieldId + '-error').innerText = message;
        }

        function validateForm() {
            var phone = document.getElementById('phone').value;
            var email = document.getElementById('email').value;
             // Validate phone format
            var phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(phone)) {
                showErrorMessage('Phone', 'Phone must have 10 digits.');
                return false; // Prevent form submission
            } else {
                showErrorMessage('Phone', ''); // Clear previous error message
            }

            // Validate email format
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email !== '' && !emailRegex.test(email)) {
                showErrorMessage('email', 'Invalid email format.');
                return false; // Prevent form submission
            } else {
                showErrorMessage('email', ''); // Clear previous error message
            }

            return true; // Allow form submission
        }
    </script>

</head>

<body>
<div class="container">
        <h3>Admin Registration</h3>
    

    <form action="" method="post">
        <label for="Password" class="l1">Password:</label>
        <input type="password" name="Password" required><br>

        <label for="Name" class="l1">Name:</label>
        <!--Only leeters allow  to the name -->
        <input type="text" name="Name" pattern="[A-Za-z]+" title="Only letters are allowed" required><br> 

        <!--Only 0(7[0124578]|[0124578])[0-9]{7} allow  to the phone -->
        <label for="Phone" class="l1">Phone:</label>
        <input type="text" class="form-control" id="Phone" name="Phone" pattern='^0(7[0124578]|[0124578])[0-9]{7}$' required><br>
        <span class="error-message" id="phone-error"></span>

        <label for="Email" class="l1">Email:</label>
        <input type="email" name="Email" required><br>

        <button type="submit" name="register_btn" class="btn btn-primary">Register</button>
            <a href="admin_login.php" class="btn btn-warning">Go to Login</a>
    </form>
        <!-- Display registration success message in a white box with red text -->
        <div class="white-box">
            <p class="red-text"><?php echo $registration_success_message; ?></p>
        </div>    
</body>

</html>
