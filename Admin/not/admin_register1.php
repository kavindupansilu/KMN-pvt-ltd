<?php
session_start();

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['register_btn'])) {
    $user_id = $_POST['User_Id'];
    $password = password_hash($_POST['Password'], PASSWORD_DEFAULT);
    $name = $_POST['Name'];
    $phone = $_POST['Phone'];
    $email = $_POST['Email'];

    $insert_query = "INSERT INTO admin (User_Id, Password, Name, Phone, Email)
                     VALUES (?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($connect, $insert_query);
    mysqli_stmt_bind_param($stmt, "sssss", $user_id, $password, $name, $phone, $email);

    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "Admin registered successfully!";
    } else {
        $_SESSION['error'] = "Error registering Admin: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="en">
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
            <label for="User_Id" class="l1">User ID:</label>
            <input type="text" name="User_Id" required>

            <label for="Password" class="l1">Password:</label>
            <input type="password" name="Password" required>

            <label for="Name" class="l1">Name:</label>
            <input type="text" name="Name" required>

            <label for="Phone" class="l1">Phone:</label>
            <input type="text" name="Phone" required>

            <label for="Email" class="l1">Email:</label>
            <input type="email" name="Email" required>

            <button type="submit" name="register_btn" class="btn btn-primary">Register</button>
            <a href="admin_login.php" class="btn btn-warning">Go to Login</a>
        </form>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p>' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        } elseif (isset($_SESSION['success'])) {
            echo '<p>' . $_SESSION['success'] . '</p>';
            unset($_SESSION['success']);
        }
        ?>
    </div>
</body>
</html>
