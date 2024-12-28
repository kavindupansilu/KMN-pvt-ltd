<?php
session_start();
include('Includes2/header.php');
include('Includes2/navbar.php');

$conn = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


// $statement->execute();
// $all_result = $statement->fetchAll();
// $total_rows = $statement->rowCount();

?>
<!DOCTYPE html>
<html lang="en">

    <head>
       <title></title>
       <meta charset="utf-8">
       <meta name="viewport" content="width=device-width,initial-scale=1">
       <meta name="robots" content="noindex, nofollow">
         <link rel="stylesheet" href="css/bootstrap.min.css">
         <script src="js/jquery.min.js"></script>
         <script src="js/bootstrap.min.js"></script>
        <script src="js/jquery.dataTables.min.js"></script>
        <script src="js/dataTables.bootstrap.min.js"></script>
        <link rel="stylesheet" href="css/datepicker.css"/>
         <script src="js/bootstrap-datepicker1.js"></script>
             <link rel="stylesheet" href="css/datepicker.css">
             <script src="js/bootstap-datepicker.js"></script>
        <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
         <style>
         /* Remove the navbar's default margin-bottom and rounded <borders */
         .navbar{
             margin-bottom: 4px;
             border-radius: 0;
         }
         /* Add a gray background color and some padding to the footer */
         footer{
             background: color #f2f2f2;
             padding: 25px;
         }
         .carousel-inner img {
             width: 100% /* Set width to 100% */
             margin:auto;
             min-height: 200px;
         }
         .navbar-brand
         {
             padding:5px 40px;
         }
         .navbar-brand:hover
         {
             background-color:#ffffff;
         }
         /*Hide the carousel text when the screen is Less than 600 pixel wide */
         @media (max-width: 600px){
             .carousel-caption{
                 display: none;
         }
         }
        
            .box
         {
             width: 100%;
             max-width: 1390px;
             border-radius: 5px;
             border:1px solid #ccc;
             padding: 15px;
             margin: 0 auto;
             margin-top: 50px;
             box-sizing:border-box;
         }
         
         
        </style>
    </head>
    <body>


             <div class="container-fluid">

             <h3 align="center">Invoice List</h3><br/>
             <div align="right">

             </div>
             <br/>
             <table id="data-table" class="table-bordered table-striped">
                                 <thead>
              <tr>
                <th>Invoice no.</th>
                <th>Invoice Date</th>
                <th>Customer Name</th>
                <th>Invoice Total</th>
                <th>PDF</th>
                <th>Edit</th>
                <th>Delete</th>
                </tr>
                </thead>
                <?php
                
                    foreach($all_result as $row)
                    {
                        echo '<tr>
                        <td>'.$row["Booking_Id"].'</td>
                        <td>'.$row["Date"].'</td>
                        <td>'.$row["Time"].'</td>
                        <td>'.$row["Type_of_Service"].'</td>
                        <td>'.$row["Cust_Id"].'</td>
                        <td><a href="print_invoice.php?pdf=1&id='.$row["Booking_Id"].'">PDF</a></td>
                        <td><a href="invoice.php?update=1&id='.$row["Booking_Id"].'"><span class="glyphicon glyphicon-edit"></span></a></td>
                        </tr>';
                    }    

                
                ?>
                </table>
             </div>

             <br>

             <footer class="container-fluid text-center">
                 <p>footer text</p>
             </footer>
             </body>
             </html>
             <script type="text/javascript">
             $(document).ready(function(){

             }