<?php require "config.php"; // Start the session (if not already started)
session_start(); 
include('Includes2/header.php'); 
include('Includes2/navbar.php');

// Fetch updated items from the database
$query_items = "SELECT Item_Id, Item_Name FROM item";
$result_items = mysqli_query($con, $query_items);

?>

<?php
// get booking details after click ADD in booking.php

if (isset($_POST['add_to_index_btn'])) {
    $item = $_POST['item'];
    $service_id = $_POST['service_id'];
    $service_type = $_POST['service_type'];
    $service_cost = $_POST['service_cost'];

    // Display the captured booking details
    echo 'Item : ' . $item . '<br>';
    echo 'Service_Id: ' . $service_id . '<br>';
    echo 'Type_of_Service: ' . $service_type . '<br>';
    echo 'Service_Charge: ' . $service_cost . '<br>';
}
?>

<html>
<head>
    <title>Create Printable PDF invoice using PHP MySQL</title>
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    
    <link rel='stylesheet' href='https://code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css'>
    <script src="https://code.jquery.com/ui/1.13.0-rc.3/jquery-ui.min.js" integrity="sha256-R6eRO29lbCyPGfninb/kjIXeRjMOqY3VWPVk6gMhREk=" crossorigin="anonymous"></script>
    
</head>
<body>
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
    <div class='container pt-5'>
        <h1 class='text-center text-primary'>INVOICE</h1><hr>
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
				
                $date = $_POST["Payment_Date"];
				$invoice_date = date('Y-m-d', strtotime($date));
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
						
    						$iname = isset($_POST['iname']) && is_array($_POST['iname']) ? implode(",", $_POST['iname'][$i]) : '';
                            $price = mysqli_real_escape_string($con, $_POST["price"][$i]);
                            $qty = mysqli_real_escape_string($con, $_POST["qty"][$i]);
                            $itotal = mysqli_real_escape_string($con, $_POST["itotal"][$i]);
                            $serviceid = mysqli_real_escape_string($con, $_POST["serviceid"][$i]);
						    $typeservice = implode(",", $_POST['typeservice'])[$i];
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
        ?>
        <form method='post' action='payment.php' autocomplete='off'>
            <div class='row'>
                <div class='col-md-4'>
                    <h5 class='text-success'>Payment Details</h5>

                    <!-- Display the Payment ID -->
                    <div class='form-group'>
                        <label>Payment ID</label>
                        <input type="text" name="Payment_Id" value="<?php echo $nextPaymentId; ?>" readonly class='form-control'>
                    </div>
                    <div class='form-group'>
                        <label>Payment Date</label>
                        <input type='date' name='Payment_Date' id='date' required class='form-control' min="<?php echo date('Y-m-d'); ?>">
                    </div>
					<div class="form-group">
                        <label for="User_Id">User ID:</label>
                        <input type="text" class="form-control" id="User_Id" name="User_Id" readonly value="<?php echo isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : ''; ?>">
                    </div>
                </div>
                <div class='col-md-8'>
                    <h5 class='text-success'>Customer Details</h5>
                    <div class="form-group">
                        <label>Cust_Id:</label>
                        <input type="text" class="form-control" id='Cust_Id' name='Cust_Id' pattern='[7-9][0-9]{8}[Vv]|[2-9][0-9]{11}' required>
                        <span class="error-message" id="Cust_Id-error"></span>
                    </div>
                    <div class='form-group'>
                        <label>Name</label>
                        <input type='text' name='Name' required class='form-control'>
                    </div>
                    <div class='form-group'>
                        <label>Address</label>
                        <input type='text' name='Address' required class='form-control'>
                    </div>
					
				<div class='form-group'>
                        <label>Vehicle ID</label>
                        <input type="text" name="Vehicle_Id" required class='form-control'>
                    </div>
                </div>
            </div>
            <div class='row'>
					<div class='col-md-12'>
						<h5 class='text-success'>Service Details</h5>
						<table class='table table-bordered'>
							<thead>
								<tr>
									<th>Item</th>
									<th>Price</th>
									<th>Qty</th>
									<th>Item Cost</th>
									<th>Service Id</th>
									<th>Type of Service</th>
									<th>Service Charge</th>				
									<th>Action</th>
								</tr>
							</thead>
							<tbody id='product_tbody'>
								<tr>
                                <!-- <select class='form-control item-select' name='iname[]' data-item-id="<?php echo $row_item['Item_Id']; ?>">
     Options
</select> -->

									<td><select class='form-control' name='iname[]' data-item-id="<?php echo $row_item['Item_Id']; ?>" >
												<option value="None">None</option>
											<?php
											// Iterate over fetched items and create options
											while ($row_item = mysqli_fetch_assoc($result_items)) {
												echo "<option value='" . $row_item['Item_Id'] . "'>" . $row_item['Item_Name'] . "</option>";
											}
											?>
										</select></td>
                                    <td><input type="text" id="price" name="price" class='form-control price' readonly></td>
									<!-- <td><input type='text'  name='price[]' class='form-control price'></td> -->
									<td><input type='text'  name='qty[]' class='form-control qty'></td>
									<td><input type='text'  name='itotal[]' class='form-control itotal'></td>
									<td><input type='text'  name='serviceid[]' class='form-control'></td>
									<!-- <td><input type='text'  name='typeservice[]' class='form-control'></td> -->
									<td><select class="form-control" id="Type_of_Service" name="typeservice[]">
                                                <!-- Add options dynamically from your database or define static options -->
                                            
											<option value="None">None</option>
                                            <option value="Oil Change">Oil Change</option>
                                            <option value="Tire Rotation">Tire Rotation</option>
                                            <option value="Fluid Checks and Replacements">Fluid Checks and Replacements</option>
                                            <option value="Air Filter Replacement">Air Filter Replacement</option>
                                            <option value="Cabin Air Filter Replacement">Cabin Air Filter Replacement</option>
                                            <option value="Engine Diagnostics">Engine Diagnostics</option>
                                            <option value="Computerized Diagnostics">Computerized Diagnostics</option>
                                            <option value="Brake System Repair">Brake System Repair</option>
                                            <option value="Suspension and Steering Repair">Suspension and Steering Repair</option>
                                            <option value="Engine Repair">Engine Repair</option>
                                            <option value="Transmission Repair">Transmission Repair</option>
                                            <option value="Electrical System Repair: ">Electrical System Repair: </option>
                                            <option value="Wheel Alignment">Wheel Alignment</option>
                                            <option value="Air Conditioning Service">Air Conditioning Service</option>
                                            <option value="Heating System Service">Heating System Service</option>
                                            <option value="Emission System Service">Emission System Service</option>
                                            <option value="Performance Upgrades">Performance Upgrades</option>
                                            <option value="Interior and Exterior Detailing">Interior and Exterior Detailing</option>
                                            <option value="Paint and Bodywork">Paint and Bodywork</option>
                                            
                                            <!-- Add more options as needed -->
                                            </select></td>
									<td><input type='text'  name='stotal[]' class='form-control stotal'></td>
									<td><input type='button' value='DELETE' class='btn btn-danger btn-sm btn-row-remove'> </td>
								</tr>
							</tbody>
							<tfoot>
								<tr>
									<td><input type='button' value='+ Add Row' class='btn btn-primary btn-sm' id='btn-add-row'></td>
									<td colspan='2' class='text-right'>Total Item Cost</td>
									<td><input type='text' name='total_icost' id='total_icost' class='form-control' required></td>
									<td colspan='2' class='text-right'>Total Service Cost</td>
									<td><input type='text' name='total_scost' id='total_scost' class='form-control' required></td>
								</tr>
							</tfoot>
						</table>
						<!--  total cost -->
						<div class='form-group'>
							<label>Total Cost</label>
							<input type='text' name='Total_Cost' id='total_cost' class='form-control' required>
						</div>
						<!-- Paid -->
						<div class='form-group'>
							<label>Paid</label>
							<input type='text' name='Paid' id='paid' class='form-control' required>
						</div>

						<!-- Balance -->
						<div class='form-group'>
							<label>Balance</label>
							<input type='text' name='Balance' id='balance' class='form-control' required>
						</div>

						<input type='submit' name='submit' value='Save Invoice' class='btn btn-success float-right'>
					</div>
				</div>
			</form>
		</div>

		<script>
    $(document).ready(function() {
            $('#item_name').change(function() {
                var item_name = $(this).val();
                $.ajax({
                    url: 'item.php',
                    type: 'POST',
                    data: {
                        item_name: item_name
                    },
                    success: function(response) {
                        $('#item_cost').val(response);
                    }
                });
            });
        });
				// Define a function to fetch items and create options
				function fetchItemsAndCreateOptions() {
        <?php
        // Fetch items from the database
        $query_items = "SELECT Item_Id, Item_Name FROM item";
        $result_items = mysqli_query($con, $query_items);

        // Generate options
        $options = "<option value='None'>None</option>"; // Include "None" as the first option
        while ($row_item = mysqli_fetch_assoc($result_items)) {
            $options .= "<option value='" . $row_item['Item_Id'] . "'>" . $row_item['Item_Name'] . "</option>";
        }
        ?>

        // Return options
        return "<?php echo $options; ?>";
    }

    // add new row
    $("#btn-add-row").click(function(){
        var options = fetchItemsAndCreateOptions(); // Fetch options
        var row = "<tr> <td><select class='form-control' name='iname[]'>" + options + "</select></td> <td><input type='text' name='price[]' class='form-control price'></td> <td><input type='text' name='qty[]' class='form-control qty'></td> <td><input type='text'  name='itotal[]' class='form-control itotal'></td><td><input type='text' name='serviceid[]' class='form-control serviceid'></td><td><select class='form-control' id='Type_of_Service' name='typeservice[]'>				<option value='None'>None</option><option value='Oil Change'>Oil Change</option><option value='Tire Rotation'>Tire Rotation</option><option value='Fluid Checks and Replacements'>Fluid Checks and Replacements</option><option value='Air Filter Replacement'>Air Filter Replacement</option><option value='Cabin Air Filter Replacement'>Cabin Air Filter Replacement</option><option value='Engine Diagnostics'>Engine Diagnostics</option><option value='Computerized Diagnostics'>Computerized Diagnostics</option> <option value='Brake System Repair'>Brake System Repair</option><option value='Suspension and Steering Repair'>Suspension and Steering Repair</option><option value='Engine Repair'>Engine Repair</option><option value='Transmission Repair'>Transmission Repair</option><option value='Electrical System Repair'>Electrical System Repair </option><option value='Wheel Alignment'>Wheel Alignment</option><option value='Air Conditioning Service'>Air Conditioning Service</option><option value='Heating System Service'>Heating System Service</option><option value='Emission System Service'>Emission System Service</option><option value='Performance Upgrades'>Performance Upgrades</option><option value='Interior and Exterior Detailing'>Interior and Exterior Detailing</option><option value='Paint and Bodywork'>Paint and Bodywork</option></select></td></select></td><td><input type='text' name='stotal[]' class='form-control stotal'></td><td><input type='button' value='DELETE' class='btn btn-danger btn-sm btn-row-remove'> </td> </tr>";
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

			// });
		</script>
	</body>
</html>