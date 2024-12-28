<?php
// Start session to manage user session data
session_start();

// Initialize login error message variable
$login_error_message = "";

// Connect to the MySQL database
require "dbh.inc.php";
// $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

// Check connection and display error if failed
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if the login form is submitted
if (isset($_POST['login_btn'])) {
    // Get user ID and password from the form
    $user_id = $_POST['User_Id'];
    $password = $_POST['Password'];

    // Prepare SQL query to select user from database
    $query = "SELECT * FROM manager WHERE User_Id = ? AND Password = ?";
    $stmt = mysqli_prepare($connect, $query);

    // Check if statement preparation is successful
    if ($stmt) {
        // Bind parameters and execute the statement
        mysqli_stmt_bind_param($stmt, "ss", $user_id, $password);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        // Check if user exists
        if (mysqli_stmt_num_rows($stmt) > 0) {
            // User exists, set session and redirect to customer page
            $_SESSION['User_Id'] = $user_id;
            header("Location: mcustomer.php");
            exit();
        } else {
            // User doesn't exist, set error message
            $login_error_message = "Incorrect User ID or password!";
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    } else {
        // Display error if there's an issue with the query
        echo "Error in the query: " . mysqli_error($connect);
    }
}

// Close database connection
mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Manager Login</title>
    <!-- Meta tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom Styles -->
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <!-- Additional Styles -->
    <style>
        .white-box {
            background-color: white;
            padding: 10px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        .red-text {
            color: red;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Manager Login</h3>
                    </div>
                    <div class="card-body">
                        <!-- Login Form -->
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="User_Id">User_Id:</label>
                                <input type="text" name="User_Id" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="Password">Password:</label>
                                <input type="password" name="Password" class="form-control" required>
                            </div>
                            <button type="submit" name="login_btn" class="btn btn-primary btn-block">Login</button>
                        </form>
                        <!-- Display login error message -->
                        <div class="white-box">
                            <p class="red-text"><?php echo $login_error_message; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript files -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
