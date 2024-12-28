<?php
session_start();
include('Includes2/header.php');
include('Includes2/managernavbar.php');

require "database.php";
// $conn = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
// Retrieve total instock from session
$totalInstock = isset($_SESSION['total_instock']) ? $_SESSION['total_instock'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['generate_report_btn'])) {
   
    // Save report details to the database
    $reportType = $_POST['Report_Type'];
    $content = $_POST['Content'];
    $dateGenerated = date('Y-m-d'); // You can adjust the date format as needed
    $parts = isset($_POST['Parts_Sales']) && is_array($_POST['Parts_Sales']) ? implode(",", $_POST['Parts_Sales']) : '';
    //$parts = $_POST['Parts_Sales'];
   // $inStock = $_POST['InStock'];
    $managerId = $_POST['Manager_Id'];
    $user_id = isset($_SESSION['User_Id']) ? $_SESSION['User_Id'] : null;

    $serviceRevenue = 0; // Initialize with a default value

// Query to retrieve service revenue from sales table for the selected date
$serviceRevenueQuery = "SELECT Service_Revenue FROM sales WHERE Sales_Date = ?";
$stmtServiceRevenue = mysqli_prepare($conn, $serviceRevenueQuery);
mysqli_stmt_bind_param($stmtServiceRevenue, "s", $dateGenerated);
mysqli_stmt_execute($stmtServiceRevenue);
$resultServiceRevenue = mysqli_stmt_get_result($stmtServiceRevenue);

if ($rowServiceRevenue = mysqli_fetch_assoc($resultServiceRevenue)) {
    // Service revenue found for the selected date
    $serviceRevenue = $rowServiceRevenue['Service_Revenue'];
    // Now, set the fetched Service Revenue to the input field in the form
    echo '<script>document.getElementById("Service_Revenue").value = "' . $serviceRevenue . '";</script>';
}

mysqli_stmt_close($stmtServiceRevenue);


    // Use prepared statements to prevent SQL injection
    // $insertReportQuery = "INSERT INTO Report (Report_Type, Content, Date_Generated, Service_Revenue, Parts_Sales, InStock, Manager_Id, User_Id)
    //                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    // $stmtInsertReport = mysqli_prepare($conn, $insertReportQuery);
    // mysqli_stmt_bind_param($stmtInsertReport, "sssdssss", $reportType, $content, $dateGenerated, $serviceRevenue, $parts, $inStock, $managerId, $user_id);

    $insertReportQuery = "INSERT INTO Report (Report_Type, Content, Date_Generated, Service_Revenue, Parts_Sales, Manager_Id, User_Id)
                          VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmtInsertReport = mysqli_prepare($conn, $insertReportQuery);
    mysqli_stmt_bind_param($stmtInsertReport, "sssdsss", $reportType, $content, $dateGenerated, $serviceRevenue, $parts, $managerId, $user_id);


    if (mysqli_stmt_execute($stmtInsertReport)) {
        $_SESSION['success'] = "Report generated and saved successfully!";
    } else {
        $_SESSION['error'] = "Error generating report: " . mysqli_stmt_error($stmtInsertReport);
    }

    mysqli_stmt_close($stmtInsertReport);
}
// // Display Report
 $select = "SELECT * FROM report";

 $query = mysqli_query($conn, $select);


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="css/style.min.css" rel="stylesheet">
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <style>
        #services thead th {
            background-color: #9999ff;
            color: black;
        }
    </style>

<script>
    function calculateInStock() {
        var checkboxes = document.getElementsByName('Parts_Sales[]');
        var totalInStock = 0;
        for (var i = 0; i < checkboxes.length; i++) {
            if (checkboxes[i].checked) {
                var itemName = checkboxes[i].value;
                var instock = <?php echo json_encode($item['InStock']); ?>;
                totalInStock += parseInt(instock);
            }
        }
        document.getElementById('InStock').value = totalInStock;
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
                    <h1 class="h3 mb-2 text-gray-800">Report Details</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Report Information</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <?php
                                $select = "SELECT r.*, GROUP_CONCAT(i.InStock) AS Instock
                                FROM report r
                                JOIN item i ON FIND_IN_SET(i.Item_Name, r.Parts_Sales)
                                GROUP BY r.Report_Id";
                                    $query = mysqli_query($conn, $select);
                                    
                                    if ($query) {
                                        if (mysqli_num_rows($query) > 0) {
                                            echo '<table id="services" class="table table-bordered" width="100%" cellspacing="0">';
                                            echo '<thead>';
                                            echo '<tr>';
                                            echo '<th>REPORT ID</th>';
                                            echo '<th>REPORT TYPE</th>';
                                            echo '<th>CONTENT</th>';
                                            echo '<th>DATE GENERATED</th>';
                                            echo '<th>SERVICE REVENUE</th>';
                                            echo '<th>PARTS SALES</th>';
                                            echo '<th>INSTOCK</th>';
                                            echo '<th>MANAGER ID</th>';
                                            echo '<th>USER ID</th>';
                                            echo '<th>DELETE</th>';
                                            echo '</tr>';
                                            echo '</thead>';
                                            echo '<tbody>';
                                    
                                            while ($row_report = mysqli_fetch_assoc($query)) {
                                                echo '<tr>';
                                                echo '<td>' . $row_report['Report_Id'] . '</td>';
                                                echo '<td>' . $row_report['Report_Type'] . '</td>';
                                                echo '<td>' . $row_report['Content'] . '</td>';
                                                echo '<td>' . $row_report['Date_Generated'] . '</td>';
                                                echo '<td>' . $row_report['Service_Revenue'] . '</td>';
                                                // Explode the comma-separated string into an array
                                                $partsSalesArray = explode(",", $row_report['Parts_Sales']);
                                    
                                                // Display each part separately
                                                echo '<td>' . implode('<br>', $partsSalesArray) . '</td>';
                                    
                                                // Display each instock value separately
                                                $instockArray = explode(",", $row_report['Instock']);
                                                echo '<td>';
                                                foreach ($instockArray as $instock) {
                                                    echo $instock . '<br>';
                                                }
                                                echo '</td>';
                                                
                                                echo '<td>' . $row_report['Manager_Id'] . '</td>';
                                                echo '<td>' . $row_report['User_Id'] . '</td>';
                                                echo '<td>
                                                        <form action="mreport_delete.php" method="post">
                                                            <input type="hidden" name="delete_id" value="' . $row_report['Report_Id'] . '">
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
                                        echo 'Error in the query: ' . mysqli_error($conn);
                                    }
                    
                                ?>
                                
                       
                            </div>
                        </div>
                    </div>
                </div>

                
            <!-- Add PDF button -->
                <form action="pdf.php" method="post" target="_blank">
                    <input type="hidden" name="pdf_id" value="' . $row_report['Report_Id'] . '">
                    <button type="submit" name="pdf_btn" class="btn btn-primary">PDF </button>
                 </form>                        

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
mysqli_close($conn);
?>
