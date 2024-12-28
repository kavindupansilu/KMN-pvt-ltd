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
                $caddress = mysqli_real_escape_string($con, $_POST["address"]);
                $total_cost = mysqli_real_escape_string($con, $_POST["Total_Cost"]);
                $paid = mysqli_real_escape_string($con, $_POST["Paid"]);
                $balance = mysqli_real_escape_string($con, $_POST["Balance"]);
				$user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

                $sql = "INSERT INTO invoice (Payment_Id, Payment_Date, Cust_Id, Vehicle_Id, Name, address, Total_Cost, Paid, Balance, User_Id) 
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

<script>

// Show the invoice form when the "Add Invoice" button is clicked
$("#btn-add-invoice").click(function() {
$("#invoice-form").show();
});

// set date
$(document).ready(function(){
	$("#date").datepicker({
		dateFormat:"dd-mm-yy"
	});
	
	// add new row
	$("#btn-add-row").click(function(){
		var row="<tr> <td><input type='text' required name='iname[]' class='form-control'></td> <td><input type='text' required name='price[]' class='form-control price'></td> <td><input type='text' required name='qty[]' class='form-control qty'></td> <td><input type='text'  name='itotal[]' class='form-control itotal'></td><td><input type='text' name='serviceid[]' class='form-control serviceid'></td><td><input type='text' name='typeservice[]' class='form-control typeservice'></td><td><input type='text' name='stotal[]' class='form-control stotal'></td><td><input type='button' value='DELETE' class='btn btn-danger btn-sm btn-row-remove'> </td> </tr>";
		$("#product_tbody").append(row);
	});
	
   
	// add delete button
	$("body").on("click",".btn-row-remove",function(){
		if(confirm("Are You Sure?")){
			$(this).closest("tr").remove();
			total_cost();
		}
	});

	//get item * qty
	$("body").on("keyup",".price",function(){
		var price=Number($(this).val());
		var qty=Number($(this).closest("tr").find(".qty").val());
		$(this).closest("tr").find(".itotal").val(price*qty);
		total_icost();
	});
	
	//get item * qty
	 $("body").on("keyup",".qty",function(){
		 var qty=Number($(this).val());
		 var price=Number($(this).closest("tr").find(".price").val());
		 $(this).closest("tr").find(".itotal").val(price*qty);
		 total_icost();
	 });
					 
	 //get item total cost
	function total_icost(){
		var tot=0;
		$(".itotal").each(function(){
			tot+=Number($(this).val());
		});
		$("#total_icost").val(tot);
		total_cost();
	}

	 // bind total_scost function to keyup event of .stotal inputs
	 $("body").on("keyup",".stotal",function(){
		total_scost();
	});

	// get service total cost
	function total_scost(){
		var total=0;
		$(".stotal").each(function(){
			total+=Number($(this).val());
		});
		$("#total_scost").val(total);
		total_cost();
	}

	// get total cost
	function total_cost(){
		var itemCost = parseFloat($("#total_icost").val());
		var serviceCost = parseFloat($("#total_scost").val());
		var totalCost = itemCost + serviceCost;
		$("#total_cost").val(totalCost.toFixed(2));
	}

	function updateBalance() {
		var paid = parseFloat($("#paid").val());
		var totalCost = parseFloat($("#total_cost").val());
		var balance = paid - totalCost;
		$("#balance").val(balance.toFixed(2));
	}

	// Call updateBalance on page load
	updateBalance();

	// Call updateBalance whenever Paid field value changes
	$("#paid").on("keyup", function() {
		updateBalance();
	});		

});
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
				<h1 class="h3 mb-2 text-gray-800">Invoice List</h1>
					<!-- <button type="button" class="btn btn-primary mb-3" id="btn-add-invoice">Add Invoice</button> -->
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
                                        echo '<table id="invoice" class="table table-bordered" width="100%" cellspacing="0">';
                                        echo '<thead>';
                                        echo '<tr>';
										echo '<th>SID</th>';
                                        echo '<th>PAYMENT ID</th>';
                                        echo '<th>PAYMENT DATE</th>';
                                        echo '<th>CUSTOMER ID</th>';
                                        echo '<th>VEHICLE ID</th>';
                                        echo '<th>NAME</th>';
										echo '<th>SERVICE</th>';
                                        echo '<th>TOTAL COST</th>';
										echo '<th>USER ID</th>';
										echo '<th>DELETE</th>';
                                        echo '<th>PRINT</th>';
                                        echo '</tr>';
                                        echo '</thead>';
                                        echo '<tbody>';
										while ($row_payment = mysqli_fetch_assoc($result)) {
                                            echo '<tr>';
                                            echo '<td>' . $row_payment['SID'] . '</td>';
											echo '<td>' . $row_payment['Payment_Id'] . '</td>';
                                            echo '<td>' . $row_payment['Payment_Date'] . '</td>';
                                            echo '<td>' . $row_payment['Cust_Id'] . '</td>';
                                            echo '<td>' . $row_payment['Vehicle_Id'] . '</td>';
                                            echo '<td>' . $row_payment['Name'] . '</td>';

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
                                            // echo '<td>
                                            // <button class="btn btn-primary btn-print-invoice" data-invoice-id="' . $row_payment['SID'] . '">PRINT</button>
                                            //  </td>';

                                        
											echo '<td>
											<form action="payment_delete.php" method="post">
												<input type="hidden" name="delete_id" value="' . $row_payment['SID'] . '">
												<button type="submit" name="delete_btn" class="btn btn-danger">DELETE</button>
											</form>
											</td>';

                                             echo '<td>
                                                 <form action="print.php" method="post" target="_blank">
                                               <input type="hidden" name="payment_id" value="' . $row_payment['SID'] . '">
                                                    <button type="submit" name="btn-print-invoice" class="btn btn-primary">PRINT</button>
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
 <!-- Place your "Print" button here -->
<button id="printButton">Print Invoice</button>

<!-- jQuery and custom JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Event listener for the "Print" button click
        $('#printButton').click(function() {
            // Fetch invoice ID or any other necessary data
            var invoiceId = 'Payment_Id'; // Replace 'your_invoice_id' with the actual invoice ID

            // AJAX request to print.php
            $.ajax({
                type: "POST",
                url: "print.php",
                data: { id: invoiceId },
                success: function(response) {
                    // Open PDF in new window
                    var blob = new Blob([response], { type: 'application/pdf' });
                    var url = window.URL.createObjectURL(blob);
                    var win = window.open(url, '_blank');

                    // Trigger print dialog
                    win.onload = function() {
                        win.print();
                    };
                },
                error: function(xhr, status, error) {
                    // Handle error
                    console.error(error);
                }
            });
        });
    });
</script>

            </div>
        </div>
    </div>
</body>

</html>

<?php
include('Includes2/footer.php');
mysqli_close($con);
?>