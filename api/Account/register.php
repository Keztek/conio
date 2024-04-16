<?php

header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: *');

require_once '../../vendor/autoload.php';
use Firebase\JWT\JWT;
require('error_codes.php');
require('db.php');
$privateKey = file_get_contents('../../private/private.pem');
$header = [
    'typ' => 'JWT',
    'alg' => 'HS256',
    'kid' => 'q321q321'
];

if ($con->connect_error) {
    die("Connection failed: ".$con->connect_error);
}

if (isset($_SERVER['HTTP_ORIGIN'])) {
    //header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    //header('Access-Control-Allow-Credentials: true');
    //header('Access-Control-Max-Age: 86400');
}

if (isset($_POST['username'])) {
    $username = $_POST['username'];
}
if (isset($_POST['email'])) {
    $email = $_POST['email'];
}
if (isset($_POST['password'])) {
    $password = $_POST['password'];
}

if (isset($username) && isset($email) && isset($password)) {
    $sql = "CREATE TABLE IF NOT EXISTS accounts (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(25) NOT NULL,
        email VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        uuid VARCHAR(255) NOT NULL
        )";

    if ($con->query($sql) === TRUE) { }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $checkUsernameSql = "SELECT * FROM accounts WHERE username='$username'";
    $existingUser = $con->query($checkUsernameSql);

    if ($existingUser->num_rows > 0) {
        $response = array('Error' => ErrorCodes::UsernameAlreadyRegistered);
        echo json_encode($response);
    } else {
        $uuid = generateUuid4();

        $stmt = $con->prepare("INSERT INTO accounts (username, email, password, uuid) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $hashedPassword, $uuid);
        if ($stmt->execute()) {
            $payload = array(
                "iss" => "http://playerio.keztek.net",
                "aud" => "http://playerio.keztek.net",
                "iat" => time(),
                "nbf" => time(),
                "exp" => time() + (60 * 60 * 24 * 30 * 3),
                "uuid" => $user['uuid']
                );
            $jwt = JWT::encode($payload, $privateKey, 'RS256', $header['kid']);
            header('Content-Type: application/json');
            echo json_encode(array('Token' => $jwt));
            //$token = base64_encode(json_encode(array('username' => $username, 'uuid' => $uuid)));
            //$response = array('token' => $token);
            //echo json_encode($response);
        } else {
            $response = array('Error' => ErrorCodes::ErrorRegistering);
            header('Content-Type: application/json');
            echo json_encode($response);
        }
    }
}

$con->close();

function generateUuid4() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

?>