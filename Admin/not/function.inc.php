<?php
function emptyInputSignup($username, $email, $phone, $password, $cpassword) {
    return empty($username) || empty($email) || empty($phone) || empty($password) || empty($cpassword);
}

function invalidUsername($username) {
    return !preg_match("/^[a-zA-Z0-9]*$/", $username);
}

function invalidEmail($email) {
    return !filter_var($email, FILTER_VALIDATE_EMAIL);
}

function invalidPhone($phone) {
    return !filter_var($phone, FILTER_VALIDATE_INT);
}

function passwordMatch($password, $cpassword) {
    return $password !== $cpassword;
}

function usernameExists($connect, $username, $email, $phone) {
    $sql = "SELECT * FROM `user` WHERE Username = ? OR Email = ? OR Phone = ?";
    $stmt = mysqli_stmt_init($connect);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../signup.php?error=stmtfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $phone);
    mysqli_stmt_execute($stmt);
    $resultData = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    return ($row = mysqli_fetch_assoc($resultData)) ? $row : false;
}

function createUser($connect, $username, $email, $phone, $password) {
    $sql = "INSERT INTO `user` (Username, Email, Phone, Password) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($connect);

    if (!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: ../signup.php?error=stmtfailed");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $phone, $hashedPassword);

    if (!mysqli_stmt_execute($stmt)) {
        header("Location: ../signup.php?error=stmtexecutionfailed");
        exit();
    }

    mysqli_stmt_close($stmt);

    header("Location: ../Admin/tables.php");
    exit();
}
?>
