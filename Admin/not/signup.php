<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="register.css">
    <link rel="icon" href="icon.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
    <title>Register</title>
</head>

<body>
    <div class="container mt-3">
        <div class="A">
            <h2>Admin registration</h2>
            <p>Please enter valid details below</p>
        </div>
    
        <?php
        if(isset($session['success'])&& $_SESSION(['success'])!=""){
            echo '<h2 class="bg-info">'.$_SESSION['success'].'</h2>';
            unset($_SESSION['success']);
        }

        if(isset($session['status'])&& $_SESSION(['statu'])!=""){
            echo '<h2 class="bg-info">'.$_SESSION['status'].'</h2>';
            unset($_SESSION['status']);
        }
        
        ?>



        <form action="signup.inc.php" method="post">
    
            <div class="form-floating mb-3 mt-3">
                <input type="text" class="form-control" id="usernaeme" placeholder="Enter First Name" name="username">
                <label for="username">User Name</label>
            </div>
    
            <div class="form-floating mb-3 mt-3">
                <input type="email" class="form-control" id="email" placeholder="Enter Last Name" name="email">
                <label for="Lname">E-mail</label>
            </div>

            <div class="form-floating mb-3 mt-3">
                <input type="tel" class="form-control" id="phone" placeholder="Phone Number" name="phone">
                <label for="phone">Phone</label>
            </div>
    
            <div class="form-floating mb-3 mt-3">
                <input type="password" class="form-control" id="password" placeholder="Enter password" name="password">
                <label for="email">Password</label>
            </div>
    
            <div class="form-floating mt-3 mb-3">
                <input type="password" class="form-control" id="cpassword" placeholder="Enter password" name="cpassword">
                <label for="cpassword">Confirm Password</label>
            </div>
    
            <a href="tables.php">GO Back</a>
            <button type="submit" class="btn btn-primary" name="signupbtn">Register</button>
        </form>
    </div>


</body>
</html>
