<?php

header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: *');

require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
require('api/Account/error_codes.php');
require('db.php');
$privateKey = file_get_contents('private/private.pem');
$header = [
    'typ' => 'JWT',
    'alg' => 'HS256',
    'kid' => 'q321q321'
];

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if (isset($_POST['username'])) {
    $username = $_POST['username'];
}
if (isset($_POST['password'])) {
    $password = $_POST['password'];
}

if (isset($username) && isset($password)) {
    $sql = "CREATE TABLE IF NOT EXISTS webaccounts (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(25) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        reg_date TIMESTAMP NOT NULL,
        uuid VARCHAR(255) NOT NULL
        )";

    if ($con->query($sql) === TRUE) { }

    $sql = "SELECT * FROM webaccounts WHERE username = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if ($user['is_deleted'] == 0) {
            if (password_verify($password, $user['password'])) {
                $payload = [
                    "iss" => "https://conio.keztek.net",
                    "aud" => "https://conio.keztek.net",
                    "iat" => time(),
                    "nbf" => time(),
                    "exp" => time() + (60 * 60 * 24 * 30 * 3),
                    "uuid" => $user['uuid']
                ];
                $jwt = JWT::encode($payload, $privateKey, 'RS256', $header['kid']);
                header('Content-Type: application/json');
                echo json_encode(array('Token' => $jwt));
                setcookie("authtoken", $jwt, time() + (60 * 60 * 24 * 30 * 3));
                header("Location: https://conio.keztek.net");
            } else {
                header('Content-Type: application/json');
                echo json_encode(array('Error' => ErrorCodes::InvalidPassword));
                exit;
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(array('Error' => ErrorCodes::UserDeleted));
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(array('Error' => ErrorCodes::UserNotFound));
    }
}

$con->close();

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="login.php" method="post">
        <input type="text" name="username" id="">
        <input type="password" name="password" id="">
        <input type="submit" value="Login">
    </form>
</body>
</html>