<?php
session_start();
include('Includes2/header.php');
include('Includes2/navbar.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title> Admin - Dashboard</title>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <!-- Custom styles for this template-->
    <link href="css/style.min.css" rel="stylesheet">
    <style>
        .d1{margin-left: 500px;}
    </style>
</head>

<body id="page-top">

    <div id="wrapper">

        <div id="content-wrapper" class="d-flex flex-column">

            <div id="content">

                

                <div class="container-fluid">
                    <!-- Page content... -->
                    <div>
                    </div>
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
                        <a href="logout.php" class="btn btn-danger">Log Out</a>

                    </div>

                    <!-- Your other HTML content goes here -->

                </div>

               

            </div>
        </div>
    </div>

    <?php
    
    include('Includes2/footer.php');
    include('Includes2/scripts.php');
    ?>
</body>

</html>
