<?php
session_start();

$login_error_message = "";

$connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        // Handle registration form submission
        $cust_id = $_POST['Cust_Id'];// Customer NIC
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $address = $_POST['address'];
        $phone = $_POST['phone'];

        $insert_query = "INSERT INTO customer (Cust_Id, first_name, last_name, address, phone) 
        VALUES (?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($connect, $insert_query);
        mysqli_stmt_bind_param($stmt, "sssss", $cust_id, $first_name, $last_name, $address, $phone);

       // Execute the statement
    mysqli_stmt_execute($stmt);
       
        // Check if the query was successful
    if(mysqli_stmt_affected_rows($stmt) > 0) {
        
        // Add vehicles
        if(isset($_POST["Vehicle_Id"]) && isset($_POST["idType"]) && isset($_POST["idColor"]) && isset($_POST["Cust_Id"])) {
            $query_vehicle = "INSERT INTO vehicle (Vehicle_Id, Type, Color, Cust_Id)
                                    VALUES (?, ?, ?, ?)";
            $stmt_vehicle = mysqli_prepare($connect, $query_vehicle);

            for($i = 0; $i < count($_POST["Vehicle_Id"]); $i++) {
                $type = mysqli_real_escape_string($connect, $_POST["idType"][$i]);
                $color = mysqli_real_escape_string($connect, $_POST["idColor"][$i]);
                $vcust_id = mysqli_real_escape_string($connect, $_POST["Cust_Id"]);
                $vehicle_id = mysqli_real_escape_string($connect, $_POST["Vehicle_Id"][$i]);
                mysqli_stmt_bind_param($stmt_vehicle, "ssss", $vehicle_id, $type, $color, $vcust_id);
                mysqli_stmt_execute($stmt_vehicle);
            }
        }
        // Customer added successfully
        $customer_success_message = "Customer added successfully.";
    } else {
        // Error occurred
        echo "Error adding Customer: " . mysqli_error($connect);
        
    }
        mysqli_stmt_close($stmt);


    } elseif (isset($_POST['login'])) {
        // Handle login form submission
        $cust_id = $_POST['Cust_Id']; // Customer NIC
        $phone = $_POST['phone'];
        $num = $_POST['Vehicle_Id'];

        // Query to check if user exists with the provided NIC and phone
        $query = "SELECT * FROM customer WHERE Cust_Id = '$cust_id' AND phone = '$phone'";
        $result = mysqli_query($connect, $query);
        $vquery = "SELECT * FROM vehicle WHERE Vehicle_Id = '$num'";
        $result = mysqli_query($connect, $vquery);

        if (mysqli_num_rows($result) > 0) {
            // User exists, allow access to online booking
            $_SESSION['Cust_Id'] = $cust_id; // Store NIC in session
            $_SESSION['phone'] = $phone; // Store phone number in session
            $_SESSION['Vehicle_Id'] = $num; // Store vehicle number in session
            header("Location: obooking.php"); // Redirect to online booking page
            exit();
        } else {
            $login_error_message = "Invalid NIC / phone number / vehicle number.";
        }
        
    }
}

mysqli_close($connect);
?>


<!DOCTYPE html>
<html lang="en">
<head>
     <title>Login</title>
    <link rel="icon" href="icon.png">
     <!-- <link rel="stylesheet" href="login.css"> -->
     <link rel="stylesheet" href="Cmenu.css">
     <link rel="stylesheet" href="Cfooter.css">

     <link
     rel="stylesheet"
     href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
     integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
     crossorigin="anonymous"
     referrerpolicy="no-referrer"
   />

   <style>
    *{
     margin: 0;
     padding: 0;
     box-sizing: border-box;
     font-family: sans-serif;
} 

.wrapper {
     position: relative;
     width: 400px;
     height: 650px;
     
     
}

.form-wrapper {
     position: absolute;
     top: 0;
     left: 0;
     display: flex;
     justify-content: center;
     align-items: center;
     width: 100%;
     height: 100%;
     background: #fff;
     box-shadow: 0 0 10px rgba(0,0,0,0.5);
}


.wrapper .form-wrapper.sign-up {
     transform: rotate(7deg);
}
.wrapper.animate-signUp .form-wrapper.sign-in {
     transform: rotate(7deg);
     animation: animateRotate .7s ease-in-out forwards;
     animation-delay: .3s;
}
.wrapper.animate-signIn .form-wrapper.sign-in {
     animation: animateSignIn 1.5s ease-in-out forwards;
}
@keyframes animateSignIn {
     0% {
          transform: translateX(0);
     }
     50% {
          transform: translateX(-500px);
     }
     100% {
          transform: translateX(0) rotate(7deg);
     }
}.wrapper.animate-signUp .form-wrapper.sign-up {
     animation: animateSignUp 1.5s ease-in-out forwards;
}
@keyframes animateSignUp {
     0% {
          transform: translateX(0);
          z-index: 1;
     }
     50% {
          transform: translateX(500px);
     }
     100% {
          transform: translateX(0) rotate(7deg);
     }
}
.wrapper.animate-signIn .form-wrapper.sign-up {
     animation: animateRotate .7s ease-in-out forwards;
     animation-delay: .3s;
}
@keyframes animateRotate {
     0% {
          transform: rotate(7deg);
     }
     100% {
          transform: rotate(0);
          z-index: 1;
     }
}


h2 {
     font-size: 30px;
     color: #0478f4;
     text-align: center;
}
.input-group {
     position: relative;
     width: 300px;
     margin: 20px 0;
}
.input-group label {
     position: absolute;
     top: 50%;
     left: 5px;
     transform: translateY(-50%);
     font-size: 16px;
     color: #333;
     padding: 0 5px;
     pointer-events: none;
     transition: .5s;
}
.input-group input {
     width: 100%;
     height: 40px;
     font-size: 16px;
     color: #333;
     padding: 0 10px;
     background: transparent;
     border: 1px solid #333;
     outline: none;
     border-radius: 5px;
}
.input-group input:focus~label,
.input-group input:valid~label {
     top: 0;
     font-size: 12px;
     background: #fff;
}
.forgot-pass {
     margin: -15px 0 15px;
}
.forgot-pass a {
     color: #333;
     font-size: 14px;
     text-decoration: none;
}
.forgot-pass a:hover {
     text-decoration: underline;
}
.btn {
     position: relative;
     top: 0;
     left: 0;
     width: 100%;
     height: 40px;
     border: none;
     outline: none;
     background: linear-gradient(to right,#36a6d5,#184deb);
     box-shadow: 0 2px 10px rgba(0,0,0,0.3);
     font-size: 16px;
     color: #fff;
     font-weight: 600;
     cursor: pointer;
     border-radius: 5px;
}
.sign-link {
     font-size: 14px;
     text-align: center;
     margin: 25px 0;
     color: #333;
}
.sign-link a {
     color:#184deb ;
     text-decoration: none;
     font-weight: 600;
}
.sign-link a:hover {
     text-decoration: underline;
} 

.white-box {
            background-color: white;
            padding: 10px;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

.red-text {
            color: red;
        }

    body {
      font-family: Arial, sans-serif;
      background-color: #f4f4f4;
      margin: 0;
      padding: 0;
    }

    .container {
      max-width: 600px;
      margin: 50px auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    label {
      font-weight: bold;
    }

  </style>
</head>
<body>
<!-- header -->
     <header class="header">

          <a href="#" class="log">KMN (pvt) Ltd</a>
  
        <nav class="navbar">
          <a href="index.html" style="--i:1;">Home</a>
          <a href="service.html" style="--i:4;">Services</a>
          <a href="contact.html" style="--i:5;">Contact</a>
          <a href="about.html" style="--i:2;">About</a>
        </nav>
  
          <div class="social-media">
              
          </div>
        
     </header>
     <br><br><br><br><br><br><br>
     <center>
        <!-- body -->
        <div class="wrapper">
          <div class="form-wrapper sign-up">
          <form action="" method="post">
                    <h2>SignUp</h2>
                    <div class="input-group">
                        <input type="text" class="form-control" id='Cust_Id' name='Cust_Id' pattern='[7-9][0-9]{8}[Vv]|[2-9][0-9]{11}' title="[7-9][0-9]{8}[Vv]OR[2-9][0-9]{11}" required>
                        <label for="Cust_Id">NIC:</label>
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control" id="first_name" name="first_name" pattern="[A-Za-z]+" title="Only letters are allowed" required>
                        <label for="first_name">First Name:</label> 
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control" id="last_name" name="last_name" pattern="[A-Za-z]+" title="Only letters are allowed" required>
                        <label for="last_name">Last Name:</label>
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control" id="address" name="address"required>
                        <label for="address">Address:</label>
                    </div>
                    <div class="input-group">
                        <input type="text" class="form-control" id="phone" name="phone" pattern='^0(7[0124578])[0-9]{7}$' title="07(0124578)(0-9)" required>
                        <label for="phone">Phone:</label>
                    </div>
        <div class="input-group">    
            <label for="idType" >Vehicle:</label>
                <select id="idType" class="form-control" name="idType[]">    
            
                <option value="">Select Vehicle Type</option>
                <option value="Bike">Bike</option>
                <option value="Car">Car</option>
                <option value="Jeep">Jeep</option>
                <option value="Truck">Truck</option>
                <option value="Bus">Bus</option>
                <option value="Van">Van</option>
                <option value="TukTuk">TukTuk</option>
                <option value="Lorry">Lorry</option>
                </select>
      </div>
      <div class="input-group" id="idNumberField" style="display: none;" >
                <input type="text" class="form-control"  id="Vehicle_Id" name="Vehicle_Id[]"pattern='[A-Z]{3}[0-9]{4}|[A-Z]{2}[0-9]{4}' title="[A-Z]{3}[0-9]{4}OR[A-Z]{2}[0-9]{4}" >
                <label for="Vehicle_Id">Vehicle Number:</label>
        </div>

      <div class="input-group" id="idColorField" style="display: none;" >
        <input type="text" class="form-control" id="idColor" name="idColor[]">
        <label for="idColor">Vehicle Color:</label>
      </div>

<button type="submit" class="btn" name="register">Sign Up</button>
<div class="sign-link">
     <p>Already have an Account? <a href="#" class="signIn-link">Sign In</a></p>
</div>
</form>
</div>
  <div class="form-wrapper sign-in">
               <form action="" method="post">
                    <h2>Log In</h2>
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="error"><?php echo $_SESSION['error']; ?></div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>
                    <div class="input-group">
                         <input type="text" class="form-control" name="Cust_Id" required>
                         <label for="Cust_Id">NIC</label>
                    </div>
                    <div class="input-group">
                         <input type="password" class="form-control" name="phone" required>
                         <label for="phone">Phone</label>
                    </div>
                    <div class="input-group">
                         <input type="text" class="form-control" name="Vehicle_Id" required>
                         <label for="Vehicle_Id">Vehicle Number</label>
                    </div>
                    <button type="submit" class="btn" name="login">Log In</button>
                    <!-- Display error message in a white box with red text -->
                    <div class="white-box">
                        <p class="red-text"><?php echo $login_error_message; ?></p>
                    </div>
                    <div class="sign-link">
                         <p>Don't have an Account? <a href="#" class="signUp-link">Sign Up</a></p>
                    </div>
               </form> <br><br><br>
                       
          </div>
        </div>
     </center>
     <script src="login.js"></script>
     <br><br><br><br>

     <script>
  document.getElementById('idType').addEventListener('change', function() {
    var selectedService = this.value;
    if (selectedService) {
       document.getElementById('idNumberField').style.display = 'block';
      document.getElementById('idColorField').style.display = 'block';

    // } else {
    //   document.getElementById('idTypeContainer').style.display = 'none';
     }
  });
</script>
<!-- footer -->
<footer>
        <div class="footer-1">
            <p>Get connected with us on social network!</p>
            <ul>
                <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                <li><a href="#"><i class="fab fa-whatsapp"></i></a></li>
                <li><a href="#"><i class="fab fa-google-plus-g"></i></a></li>
                <li><a href="#"><i class="fab fa-instagram"></i></a></li>
            </ul>
        </div>
        <div class="footer-2">
            <div class="item">
                <h2>branches</h2><br>
                <ul>
                    <li><a href="#">Negombo</a></li>
                    <li><a href="#">Gampaha</a></li>
                    <li><a href="#">Kandy</a></li>
                    <li><a href="#"></a>Anuradhapura</li>
                </ul>
            </div>
            <div class="item">
                <h2>useful link</h2><br>
                <ul>
                    <li><a href="loging.html">Appointment</a></li>
                    <li><a href="service.html">Services</a></li>
                    <li><a href="contact.html">Contact</a></li>
                    <li><a href="index.html">Home</a></li>
                </ul>
            </div>
            <div class="item">
                <h2>products</h2><br>
                <ul>
                    <li><a href="service.html"></a>Services</li>
                    <li><a href="#"></a>Vehicle Parts</li>
                    <li><a href="#"></a>Customer Care</li>
                </ul>
            </div>
            <div class="item">
                <h2>contact</h2><br>
                <ul>
                    <li>
                        <a href="#">
                            <i class="fa-solid fa-house"></i>
                            <span>Colombo 07,Srilanka</span>
                        </a>
                    </li>
    
                    <li>
                        <a href="#">
                            <i class="fa-solid fa-envelope"></i>
                            <span>kmnservices@gmail.com</span>
                        </a>
                    </li>
    
                    <li>
                        <a href="#">
                            <i class="fa-solid fa-phone"></i>
                            <span>+9477 284 5678</span>
                        </a>
                    </li>
    
                    <li>
                        <a href="#">
                            <i class="fa-solid fa-print"></i>
                            <span>025 567 8367</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="logo">
            <img src="icon without circle.png" alt="#">
        </div>
    </footer>
    
    <footer>
        <div class="footer-content">
            <center>
          <p>Copyright © KMN (pvt) Ltd 2023</p>
        
            </center>
        </div>
      </footer>
</body>
</html>
