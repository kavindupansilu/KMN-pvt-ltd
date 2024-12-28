<?php
session_start();

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['login_btn'])) {
    $user_id = $_POST['User_Id'];
    $password = $_POST['Password'];

    $query = "SELECT * FROM admin WHERE User_Id = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && $row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['Password'])) {
            $_SESSION['user'] = $row;
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Incorrect password for admin.";
        }
    } else {
        $_SESSION['error'] = "Admin not found.";
    }
}

mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Admin Login</title>
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
        <h3>Admin Login</h3>
        <!-- <form action="customer.php" method="post"> -->
        <form action="customer_form.php" method="post">
            <label for="User_Id" class="l1">User ID:</label>
            <input type="text" name="User_Id" required>

            <label for="Password" class="l1">Password:</label>
            <input type="password" name="Password" required>

            <button type="submit" name="login_btn" class="btn btn-primary">Login</button>
        </form>

        <?php
        if (isset($_SESSION['error'])) {
            echo '<p>' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
    </div>
</body>
</html>
