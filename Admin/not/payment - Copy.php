<?php
session_start();
include('Includes2/header.php'); 
include('Includes2/navbar.php');

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Payment
if (isset($_POST['add_payment_btn'])) {
    $date = $_POST['Date'];
    $formatted_payment_date = date('Y-m-d', strtotime($date));
    $item_cost = $_POST['Item_Cost'];
    $service_charge = $_POST['Service_Charge'];
    $total_cost = $_POST['Total_Cost'];
    $service_id = $_POST['Service_Id'];
    $cust_id = $_POST['Cust_Id'];
    $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

    // Using prepared statements to prevent SQL injection
    $insert_payment_query = "INSERT INTO payment (`Date`, Item_Cost, Service_Charge, Total_Cost, Service_Id, Cust_Id,  User_Id)
                             VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt_add_payment = mysqli_prepare($connect, $insert_payment_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt_add_payment, "sdddiss", $formatted_payment_date, $item_cost, $service_charge, $total_cost, $service_id, $cust_id,  $user_id);

    // Execute the statement and handle errors
    if (mysqli_stmt_execute($stmt_add_payment)) {
        $_SESSION['success'] = "Payment added successfully!";
    } else {
        $_SESSION['error'] = "Error adding payment: " . mysqli_stmt_error($stmt_add_payment);
    }

    // Close the statement
    mysqli_stmt_close($stmt_add_payment);
}

// Display Payments
$query_payment = "SELECT * FROM payment";
$query_run_payment = mysqli_query($connect, $query_payment);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        #payment thead th {
            background-color: #9999ff;
            color: black;
        }
    </style>
    <script>
        function showErrorMessage(fieldId, message) {
            document.getElementById(fieldId + '-error').innerText = message;
        }

        function calculateTotalCost() {
            var itemCost = parseFloat(document.getElementById('Item_Cost').value) || 0;
            var serviceCharge = parseFloat(document.getElementById('Service_Charge').value) || 0;
            var totalCost = itemCost + serviceCharge;

            document.getElementById('Total_Cost').value = totalCost.toFixed(2);
        }

        function validateForm() {
            // Add any additional form validation logic here
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
                    <h1 class="h3 mb-2 text-gray-800">Payment Details</h1>

                    <!-- Add Payment Form -->
                    <a href="" class="btn btn-primary" data-toggle="modal" data-target="#addpaymentmodel">Add Payment</a>
                    <br><br>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                if ($query_run_payment) {
                                    if (mysqli_num_rows($query_run_payment) > 0) {
                                        echo '<table id="payment" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
                                        echo '<th>PAYMENT ID</th>';
                                        echo '<th>DATE</th>';
                                        echo '<th>ITEM COST</th>';
                                        echo '<th>SERVICE CHARGE</th>';
                                        echo '<th>TOTAL COST</th>';
                                        echo '<th>SERVICE ID</th>';
                                        echo '<th>CUST ID</th>';
                                        echo '<th>USER ID</th>';
                                        echo '<th>EDIT</th>';
                                        echo '<th>DELETE</th>';
                                        echo '<th>PDF</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';

                                        while ($row_payment = mysqli_fetch_assoc($query_run_payment)) {
                                            echo '<tr>';
                                            echo '<td>' . $row_payment['Payment_Id'] . '</td>';
                                            echo '<td>' . $row_payment['Date'] . '</td>';
                                            echo '<td>' . $row_payment['Item_Cost'] . '</td>';
                                            echo '<td>' . $row_payment['Service_Charge'] . '</td>';
                                            echo '<td>' . $row_payment['Total_Cost'] . '</td>';
                                            echo '<td>' . $row_payment['Service_Id'] . '</td>';
                                            echo '<td>' . $row_payment['Cust_Id'] . '</td>';
                                            echo '<td>' . $row_payment['User_Id'] . '</td>';

                                            echo '<td>
                                                    <form action="payment_edit.php" method="post">
                                                        <input type="hidden" name="edit_id" value="' . $row_payment['Payment_Id'] . '">
                                                        <button type="submit" name="edit_btn" class="btn btn-success">EDIT</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="payment_delete.php" method="post">
                                                        <input type="hidden" name="delete_id" value="' . $row_payment['Payment_Id'] . '">
                                                        <button type="submit" name="delete_btn" class="btn btn-danger">DELETE</button>
                                                    </form>
                                                </td>';

                                            echo '<td>
                                                    <form action="payment_pdf.php" method="post" target="_blank">
                                                        <input type="hidden" name="pdf_id" value="' . $row_payment['Payment_Id'] . '">
                                                        <button type="submit" name="pdf_btn" class="btn btn-danger">PDF</button>
                                                    </form>
                                                </td>';
                                            echo '</tr>';
                                        }

                                        echo '</tbody>';
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

                <!-- Add Payment Modal -->
                <div class="modal fade" id="addpaymentmodel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Add Payment</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <!-- Add Payment Form -->
                                <form action="" method="post" onsubmit="return validateForm()">
                                <div class="form-group">
                                            <label for="Date">Date:</label>
                                            <input type="date" class="form-control" id="Date" name="Date" required min="<?php echo date('Y-m-d'); ?>">
                                        </div>

                                    <div class="form-group">
                                        <label for="Item_Cost">Item Cost:</label>
                                        <input type="text" class="form-control" id="Item_Cost" name="Item_Cost" required onchange="calculateTotalCost()">
                                    </div>

                                    <div class="form-group">
                                        <label for="Service_Charge">Service Charge:</label>
                                        <input type="text" class="form-control" id="Service_Charge" name="Service_Charge" required onchange="calculateTotalCost()">
                                    </div>

                                    <div class="form-group">
                                        <label for="Total_Cost">Total Cost:</label>
                                        <input type="text" class="form-control" id="Total_Cost" name="Total_Cost" readonly>
                                    </div>

                                    <div class="form-group">
                                        <label for="Cust_Id">Cust_Id:</label>
                                        <input type="text" class="form-control" id='Cust_Id' name='Cust_Id' pattern='[7-9][0-9]{8}[Vv]|[2-9][0-9]{11}' required>
                                        <span class="error-message" id="Cust_Id-error"></span>
                                    </div>

                                    <div class="form-group">
                                        <label for="Service_Id">Service Id:</label>
                                        <input type="text" class="form-control" id="Service_Id" name="Service_Id" required>
                                    </div>

                                    <div class="form-group">
                                        <label for="User_Id">User ID:</label>
                                        <input type="text" class="form-control" id="User_Id" name="User_Id" readonly value="<?php echo isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : ''; ?>">
                                    </div>

                                    <button type="submit" class="btn btn-primary" name="add_payment_btn">Add Payment</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scroll to Top Button-->
                <a class="scroll-to-top rounded" href="#page-top">
                    <i class="fas fa-angle-up"></i>
                </a>

            

                <!-- Bootstrap core JavaScript-->
                <script src="vendor/jquery/jquery.min.js"></script>
                <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
                <script src="vendor/datatables/jquery.dataTables.min.js"></script>
                <script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
                <script src="js/demo/datatables-demo.js"></script>
                <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
                <script src="js/sb-admin-2.min.js"></script>
            </div>
        </div>
    </div>
</body>

</html>

<?php
include('Includes2/footer.php');
mysqli_close($connect);
?>
