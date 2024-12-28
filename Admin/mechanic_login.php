<?php
// Start the session to persist user login
session_start();

// Establish database connection
require "dbh.inc.php";
// $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

// Check if connection is successful
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check if login button is clicked
if (isset($_POST['login_btn'])) {
    // Get user ID and password from the form
    $user_id = $_POST['User_Id'];
    $password = $_POST['Password'];

    // Prepare SQL query to fetch user from database
    $query = "SELECT * FROM mechanic WHERE User_Id = ? AND Password = ?";
    $stmt = mysqli_prepare($connect, $query);

    // Check if statement preparation is successful
    if ($stmt) {
        // Bind parameters and execute the statement
        mysqli_stmt_bind_param($stmt, "ss", $user_id, $password);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        // Check if there is any matching user
        if (mysqli_stmt_num_rows($stmt) > 0) {
            // If user exists, set session variable and redirect to dashboard
            $_SESSION['User_Id'] = $user_id;
            header("Location: meitem.php");
            exit();
        } else {
            // If no matching user, display error message
            $login_error_message = "Incorrect User ID or password!";
        }

        // Close the prepared statement
        mysqli_stmt_close($stmt);
    } else {
        // If there's an error in the query, display error message
        echo "Error in the query: " . mysqli_error($connect);
    }
}

// Close the database connection
mysqli_close($connect);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Mechanic Login</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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
                        <h3 class="text-center">Mechanic Login</h3>
                    </div>
                    <div class="card-body">
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

                            <!-- Display login error message -->
                            <p class="red-text">
                                <?php echo isset($login_error_message) ? '<div class="text-danger mt-2">' . $login_error_message . '</div>' : ''; ?>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
