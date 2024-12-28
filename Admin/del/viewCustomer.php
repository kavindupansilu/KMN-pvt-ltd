<?php
session_start();
include('Includes2/header.php');
include('Includes2/managernavbar.php');

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Customer
if (isset($_POST['add_btn'])) {
    $cust_id = $_POST['Cust_Id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $referral = $_POST['referral'];

    // Fetch User_Id from session
    $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

    // Your existing validation and SQL queries...

    // Insert data into the 'customers' table
    $insert_customer_query = "INSERT INTO customer (Cust_Id, First_name, Last_name, Address, Phone, Email, Referral, User_Id) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_add_customer = mysqli_prepare($connect, $insert_customer_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt_add_customer, "ssssssss", $cust_id, $first_name, $last_name, $address, $phone, $email, $referral, $user_id);

    // Execute the statement and handle errors
    if (mysqli_stmt_execute($stmt_add_customer)) {
        $_SESSION['success'] = "Customer added successfully!";
    } else {
        $_SESSION['error'] = "Error adding customer: " . mysqli_stmt_error($stmt_add_customer);
    }

    // Close the statement
    mysqli_stmt_close($stmt_add_customer);
}

// Display Customers
$query_customer = "SELECT * FROM customer";
$query_run_customer = mysqli_query($connect, $query_customer);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        #customer thead th {
            background-color: #9999ff;
            color: black;
        }

        .error-popup {
            color: red;
        }
    </style>
    <script>
        function showErrorMessage(fieldId, message) {
            document.getElementById(fieldId + '-error').innerText = message;
        }

        function validateForm() {
            var custId = document.getElementById('Cust_Id').value;
            var phone = document.getElementById('phone').value;
            var email = document.getElementById('email').value;

            // Validate Cust_Id format
            var custIdRegex = /^[0-9]{9}[vV]|[0-9]{12}$/;
            if (!custIdRegex.test(custId)) {
                showErrorMessage('Cust_Id', 'Cust_Id must have 9 digits and "V" or 12 digits.');
                return false; // Prevent form submission
            } else {
                showErrorMessage('Cust_Id', ''); // Clear previous error message
            }

            // Validate phone format
            var phoneRegex = /^[0-9]{10}$/;
            if (!phoneRegex.test(phone)) {
                showErrorMessage('phone', 'Phone must have 10 digits.');
                return false; // Prevent form submission
            } else {
                showErrorMessage('phone', ''); // Clear previous error message
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

<body id="page-top">
    <div id="wrapper">
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <form class="form-inline">
                        <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                            <i class="fa fa-bars"></i>
                        </button>
                    </form>
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown no-arrow">
                            <a href="home.php" class="btn btn-danger">Log Out</a>
                        </li>
                    </ul>
                </nav>

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Customer Details</h1>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Customer Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                if ($query_run_customer) {
                                    if (mysqli_num_rows($query_run_customer) > 0) {
                                        echo '<table id="customer" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>CUST_ID</th>';
                                        echo '<th>FIRST NAME</th>';
                                        echo '<th>LAST NAME</th>';
                                        echo '<th>ADDRESS</th>';
                                        echo '<th>PHONE</th>';
                                        echo '<th>EMAIL</th>';
                                        echo '<th>REFERRAL</th>';
                                        echo '<th>MANAGER_ID</th>';
                                        echo '<th>USER_ID</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row = mysqli_fetch_assoc($query_run_customer)) {
                                            echo '<tr>';
                                            echo '<td>' . $row['Cust_Id'] . '</td>';
                                            echo '<td>' . $row['first_name'] . '</td>';
                                            echo '<td>' . $row['last_name'] . '</td>';
                                            echo '<td>' . $row['address'] . '</td>';
                                            echo '<td>' . $row['phone'] . '</td>';
                                            echo '<td>' . $row['email'] . '</td>';
                                            echo '<td>' . $row['referral'] . '</td>';
                                            echo '<td>' . $row['Manager_Id'] . '</td>';
                                            echo '<td>' . $row['User_Id'] . '</td>';
                                            echo '<td>
                                                    <form action="mcustomer_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row['Cust_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="mcustomer_delete.php" method="post">
                                                        <input type="hidden" name="delete_id" value="' . $row['Cust_Id'] . '">
                                                        <button type="submit" name="delete_btn" class="btn btn-danger">DELETE</button>
                                                    </form>
                                                </td>';
                                            echo '</tr>';
                                            
                                        }

                                        echo '</table>';
                                    } else {
                                        echo 'No records found';
                                    }
                                } else {
                                    echo 'Error in the query: ' . mysqli_error($connect);
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->

                

                <!-- Bootstrap core JavaScript-->
                <script src="vendor/jquery/jquery.min.js"></script>
                <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                <script src="vendor/datatables/jquery.dataTables.min.js"></script>
                <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
                <script src="js/demo/datatables-demo.js"></script>
                <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
                <script src="js/sb-admin-2.min.js"></script>
</body>

</html>

<?php
include('Includes2/footer.php');
mysqli_close($connect);
?>
