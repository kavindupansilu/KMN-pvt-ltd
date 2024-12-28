<?php
session_start();

$registration_success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle registration form submission
    $user_id = $_POST['User_Id'];
    $password = $_POST['Password'];
    $name = $_POST['Name'];
    $phone = $_POST['Phone'];
    $email = $_POST['Email'];

    // Validate and process registration
    if ($user_id && $password && $name && $phone && $email) {
        $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

        if (!$connect) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Escape values to prevent SQL injection
        $user_id = mysqli_real_escape_string($connect, $user_id);
        $password = mysqli_real_escape_string($connect, $password);
        $name = mysqli_real_escape_string($connect, $name);
        $phone = mysqli_real_escape_string($connect, $phone);
        $email = mysqli_real_escape_string($connect, $email);

        // Hash the password (for security)
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data into the 'admin' table
        $insert_admin_query = "INSERT INTO admin (User_Id, Password, Name, Phone, Email) 
                               VALUES ('$user_id', '$hashed_password', '$name', '$phone', '$email')";

        if (mysqli_query($connect, $insert_admin_query)) {
            $registration_success_message = "Admin registered successfully!";
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
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="reg.css">
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
</head>

<body>
<div class="container">
        <h3>Admin Registration</h3>
    

    <form action="" method="post">
        <label for="User_Id" class="l1">User_Id:</label>
        <input type="text" name="User_Id" required><br>

        <label for="Password" class="l1">Password:</label>
        <input type="password" name="Password" required><br>

        <label for="Name" class="l1">Name:</label>
        <input type="text" name="Name" required><br>

        <label for="Phone" class="l1">Phone:</label>
        <input type="text" name="Phone" required><br>

        <label for="Email" class="l1">Email:</label>
        <input type="email" name="Email" required><br>

        <button type="submit" name="register_btn" class="btn btn-primary">Register</button>
            <a href="admin_login.php" class="btn btn-warning">Go to Login</a>
    </form>
<?php echo $registration_success_message; ?>
    
</body>

</html>
