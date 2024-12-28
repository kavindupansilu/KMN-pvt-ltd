<?php
session_start();
include('Includes2/header.php');
include('Includes2/navbar.php');
require "dbh.inc.php";
// $connect = mysqli_connect("localhost", "root", "", "kmn (pvt) ltd");

if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

// Add Customer
if (isset($_POST['add_btn'])) {
    $cust_id = $_POST['Cust_Id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $referral = $_POST['referral'];

    // Fetch User_Id from session
    $user_id = $_SESSION['User_Id'];

    // Validate if phone already exists in the 'customers' table
    $checkPhoneQuery = "SELECT COUNT(*) FROM customers WHERE phone = ?";
    $stmt_check_phone = mysqli_prepare($connect, $checkPhoneQuery);
    mysqli_stmt_bind_param($stmt_check_phone, "s", $phone);
    mysqli_stmt_execute($stmt_check_phone);
    mysqli_stmt_bind_result($stmt_check_phone, $phoneCount);
    mysqli_stmt_fetch($stmt_check_phone);
    mysqli_stmt_close($stmt_check_phone);

    if ($phoneCount > 0) {
        header("Location: customer_form.php?error=phone_exists");
        exit();
    }

    // Validate if Cust_Id already exists in the 'customers' table
    $checkCustIdQuery = "SELECT COUNT(*) FROM customers WHERE Cust_Id = ?";
    $stmt_check_cust_id = mysqli_prepare($connect, $checkCustIdQuery);
    mysqli_stmt_bind_param($stmt_check_cust_id, "s", $cust_id);
    mysqli_stmt_execute($stmt_check_cust_id);
    mysqli_stmt_bind_result($stmt_check_cust_id, $custIdCount);
    mysqli_stmt_fetch($stmt_check_cust_id);
    mysqli_stmt_close($stmt_check_cust_id);

    if ($custIdCount > 0) {
        header("Location: customer_form.php?error=cust_id_exists");
        exit();
    }

    // Validate if email already exists in the 'customers' table
    $checkEmailQuery = "SELECT COUNT(*) FROM customers WHERE email = ?";
    $stmt_check_email = mysqli_prepare($connect, $checkEmailQuery);
    mysqli_stmt_bind_param($stmt_check_email, "s", $email);
    mysqli_stmt_execute($stmt_check_email);
    mysqli_stmt_bind_result($stmt_check_email, $emailCount);
    mysqli_stmt_fetch($stmt_check_email);
    mysqli_stmt_close($stmt_check_email);

    if ($emailCount > 0) {
        header("Location: customer_form.php?error=email_exists");
        exit();
    }

    // Insert data into the 'customers' table
    $insert_customer_query = "INSERT INTO customers (Cust_Id, first_name, last_name, address, phone, email, referral, User_Id) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt_add_customer = mysqli_prepare($connect, $insert_customer_query);

    // Bind parameters
    mysqli_stmt_bind_param($stmt_add_customer, "sssssssi", $cust_id, $first_name, $last_name, $address, $phone, $email, $referral, $user_id);

    // Execute the statement and handle errors
    if (mysqli_stmt_execute($stmt_add_customer)) {
        $_SESSION['success'] = "Customer added successfully!";
    } else {
        $_SESSION['error'] = "Error adding customer: " . mysqli_stmt_error($stmt_add_customer);
    }

    // Close the statement
    mysqli_stmt_close($stmt_add_customer);
}

// Close the database connection
mysqli_close($connect);
?>
