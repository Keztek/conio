<?php

require('error_codes.php');
require('db.php');

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

$username = $_POST['username'];
$currentPassword = $_POST['current_password'];
$newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

$sql = "CREATE TABLE IF NOT EXISTS accounts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(25) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    reg_date TIMESTAMP NOT NULL,
    uuid VARCHAR(255) NOT NULL
    )";

if ($con->query($sql) === TRUE) { }

$sql = "SELECT * FROM accounts WHERE username='$username'";
$result = $con->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($currentPassword, $user['password'])) {
        $updateSql = "UPDATE accounts SET password='$newPassword' WHERE username='$username'";
        if ($con->query($updateSql) === TRUE) {
            $response = array('Success' => ErrorCodes::PasswordUpdateSuccessful);
            echo json_encode($response);
        } else {
            $response = array('Error' => ErrorCodes::ErrorUpdatingPassword);
            echo json_encode($response);
        }
    } else {
        $response = array('Error' => ErrorCodes::IncorrectPassword);
        echo json_encode($response);
    }
}

$con->close();

?>