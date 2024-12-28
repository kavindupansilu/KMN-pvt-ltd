<?php require "config.php"; 
// Start the session (if not already started)
session_start();
include('Includes2/header.php'); 
include('Includes2/navbar.php');

?>
 <?php
            // Define $nextPaymentId outside the if(isset($_POST["submit"])) block
            $nextPaymentId = 1; // Default value
            $sqlMaxPaymentId = "SELECT MAX(Payment_Id) AS max_payment_id FROM invoice";
            $result = $con->query($sqlMaxPaymentId);
            if ($result && $row = $result->fetch_assoc()) {
                $nextPaymentId = $row['max_payment_id'] + 1;
            }

            if(isset($_POST["submit"])){
                $invoiceid = sprintf("%03d", $nextPaymentId); // Format to 3 digits
                $invoice_date = date("Y-m-d", strtotime($_POST["Payment_Date"]));
                $custid = $_POST["Cust_Id"];
				$vehicleid = $_POST["Vehicle_Id"];
                $cname = mysqli_real_escape_string($con, $_POST["Name"]);
                $caddress = mysqli_real_escape_string($con, $_POST["Address"]);
                $total_cost = mysqli_real_escape_string($con, $_POST["Total_Cost"]);
                $paid = mysqli_real_escape_string($con, $_POST["Paid"]);
                $balance = mysqli_real_escape_string($con, $_POST["Balance"]);
				$user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

                $sql = "INSERT INTO invoice (Payment_Id, Payment_Date, Cust_Id, Vehicle_Id, Name, Address, Total_Cost, Paid, Balance, User_Id) 
                        VALUES ('{$invoiceid}', '{$invoice_date}', '{$custid}', '{$vehicleid}', '{$cname}', '{$caddress}', '{$total_cost}', '{$paid}', '{$balance}', '{$user_id}')";

                if($con->query($sql)) {
                    $sid = $con->insert_id;
                    $sql2 = "INSERT INTO invoice_products (SID, Item_Name, PRICE, QTY, Item_Cost, Service_Id, Type_of_Service, Service_Charge) VALUES ";
                    $rows = [];

                    // Check if the relevant fields are set in $_POST
                    if(isset($_POST["iname"]) && isset($_POST["price"]) && isset($_POST["qty"]) && isset($_POST["itotal"]) && isset($_POST["serviceid"]) && isset($_POST["typeservice"]) && isset($_POST["stotal"])) {
                        $rows = [];
                        for($i = 0; $i < count($_POST["iname"]); $i++) {
                            $iname = mysqli_real_escape_string($con, $_POST["iname"][$i]);
                            $price = mysqli_real_escape_string($con, $_POST["price"][$i]);
                            $qty = mysqli_real_escape_string($con, $_POST["qty"][$i]);
                            $itotal = mysqli_real_escape_string($con, $_POST["itotal"][$i]);
                            $serviceid = mysqli_real_escape_string($con, $_POST["serviceid"][$i]);
                            $typeservice = mysqli_real_escape_string($con, $_POST["typeservice"][$i]);
                            $stotal = mysqli_real_escape_string($con, $_POST["stotal"][$i]);
                            $rows[] = "('{$sid}', '{$iname}', '{$price}', '{$qty}', '{$itotal}', '{$serviceid}', '{$typeservice}', '{$stotal}')";
                        }
                        $sql2 .= implode(",", $rows);
                        if($con->query($sql2)) {
    
                            echo "<div class='alert alert-success'>Invoice Added. <a href='print.php?id={$sid}' target='_BLANK'>Click</a> here to Print Invoice</div>";
                        } else {
                            echo "<div class='alert alert-danger'>Invoice Added Failed.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger'>Incomplete or missing product details.</div>";
                    }
                }        
            }

			// Fetch data from invoice table
			$query = "SELECT * FROM invoice";
			$result = mysqli_query($con, $query);
        ?>

<html>
<head>

	<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    <link rel='stylesheet' href='https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css'>
    <script src="https://code.jquery.com/ui/1.13.0-rc.3/jquery-ui.min.js" integrity="sha256-R6eRO29lbCyPGfninb/kjIXeRjMOqY3VWPVk6gMhREk=" crossorigin="anonymous"></script>
    
	<style>
        #payment thead th {
            background-color: #9999ff;
            color: black;
        }
    </style>

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
				<h1 class="h3 mb-2 text-gray-800">Invoice List</h1>
					<!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Payment Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                if ($result) {
                                    if (mysqli_num_rows($result) > 0) {
                                        echo '<table id="payment" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
										echo '<th>SID</th>';
                                        echo '<th>PAYMENT ID</th>';
                                        echo '<th>PAYMENT DATE</th>';
                                        echo '<th>CUSTOMER ID</th>';
                                        echo '<th>NAME</th>';
                                        echo '<th>VEHICLE ID</th>';
										echo '<th>SERVICE</th>';
                                        echo '<th>TOTAL COST</th>';
										echo '<th>USER ID</th>';
										echo '<th>DELETE</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';
										while ($row_payment = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td>' . $row_payment['SID'] . '</td>';
											echo '<td>' . $row_payment['Payment_Id'] . '</td>';
                                            echo '<td>' . $row_payment['Payment_Date'] . '</td>';
                                            echo '<td>' . $row_payment['Cust_Id'] . '</td>';
                                            echo '<td>' . $row_payment['Name'] . '</td>';
                                            echo '<td>' . $row_payment['Vehicle_Id'] . '</td>';

                                            //get Service id 
                                            // Fetch service ID from invoice_products table
                                            $sql_service_id = "SELECT Service_Id FROM invoice_products WHERE SID = " . $row_payment['SID'];
                                            $result_service_id = mysqli_query($con, $sql_service_id);
                                            if ($result_service_id && $row_service_id = mysqli_fetch_assoc($result_service_id)) {
                                                echo '<td>' . $row_service_id['Service_Id'] . '</td>';
                                            } else {
                                                echo '<td>N/A</td>'; // If no service ID is found
                                            }

                                            echo '<td>' . $row_payment['Total_Cost'] . '</td>';
                                            echo '<td>' . $row_payment['User_Id'] . '</td>';
                                        
											echo '<td>
											<form action="payment_delete.php" method="post">
												<input type="hidden" name="delete_id" value="' . $row_payment['SID'] . '">
												<button type="submit" name="delete_btn" class="btn btn-danger">DELETE</button>
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
mysqli_close($con);
?>