<!DOCTYPE html>
<html lang="en">
<head>
  <title>Home Page</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Link to the favicon -->
  <link rel="icon" href="icon.png">
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- Bootstrap JS -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <!-- Custom CSS -->
  <link rel="stylesheet" href="mainhome.css">
</head>
<body>

<!-- Navigation bar -->
<nav class="navbar navbar-inverse">
  <div class="container-fluid">
    <div class="navbar-header">
      <!-- Brand logo -->
      <a class="navbar-brand">KMN (PVT) LTD</a>
    </div>
    <ul class="nav navbar-nav">
      <!-- Dropdown menu for ADMIN -->
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">ADMIN <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <!-- Links for admin registration and login -->
          <li><a href="admin_register.php">ADMIN REGISTER</a></li>
          <li><a href="admin_login.php">ADMIN LOGIN</a></li>
        </ul>
      </li>
      <!-- Dropdown menu for MECHANIC -->
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">MECHANIC <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <!-- Links for mechanic registration and login -->
          <li><a href="mechanic_register.php">MECHANIC REGISTER</a></li>
          <li><a href="mechanic_login.php">MECHANIC LOGIN</a></li>
        </ul>
      </li>
      <!-- Dropdown menu for MANAGER -->
      <li class="dropdown">
        <a class="dropdown-toggle" data-toggle="dropdown" href="#">MANAGER <span class="caret"></span></a>
        <ul class="dropdown-menu">
          <!-- Links for manager registration and login -->
          <li><a href="manager_register.php">MANAGER REGISTER</a></li>
          <li><a href="manager_login.php">MANAGER LOGIN</a></li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
  
<div class="container">
  <h3></h3>
</div>

</body>
</html>
