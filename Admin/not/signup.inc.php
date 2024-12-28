<?php
include_once('dbh.inc.php');
include('function.inc.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from the form
    $password = $_POST['password'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];

    // Check for empty input
    if (emptyInputSignup($name, $email, $phone, $password)) {
        header("Location: ../admin/admin_register.php?error=emptyinput");
        exit();
    }


    // Check for invalid username
    if (invalidUsername($name)) {
        header("Location: ../admin/admin_register.php?error=invalidusername");
        exit();
    }

    // Check for invalid email
    if (invalidEmail($email)) {
        header("Location: ../admin/admin_register.php?error=invalidemail");
        exit();
    }

    // Check for invalid phone
    if (invalidPhone($phone)) {
        header("Location: ../admin/admin_register.php?error=invalidphone");
        exit();
    }


    // Check if username, email, or phone already exists
    $userExists = usernameExists($connect, $name, $email, $phone);
    if ($userExists) {
        header("Location: ../admin/admin_register.php?error=userexists");
        exit();
    }

    // Create user if all checks pass
    createUser($connect, $name, $email, $phone, $password);
} else {
    // Redirect if accessed without submitting the form
    header("Location: ../admin/admin_register.php");
    exit();
}
?>
