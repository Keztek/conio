<?php

require_once '../../vendor/autoload.php';
use Firebase\JWT\JWT;
require('error_codes.php');
require('db.php');
//$key = "WP'C,ezro>28:w#£7U.2#wk=";
$privateKey = file_get_contents('../../private/private.pem');
$header = [
    'typ' => 'JWT',
    'alg' => 'HS256',
    'kid' => 'q321q321'
];

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
}

$username = $_POST['username'];
$password = $_POST['password'];

$sql = "CREATE TABLE IF NOT EXISTS accounts (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(25) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    reg_date TIMESTAMP NOT NULL,
    uuid VARCHAR(255) NOT NULL
    )";

if ($con->query($sql) === TRUE) { }

$sql = "SELECT * FROM accounts WHERE username = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if ($user['is_deleted'] == 0) {
        if (password_verify($password, $user['password'])) {
            $payload = [
                "iss" => "http://playerio.keztek.net",
                "aud" => "http://playerio.keztek.net",
                "iat" => time(),
                "nbf" => time(),
                "exp" => time() + (60 * 60 * 24 * 30 * 3),
                "uuid" => $user['uuid']
            ];
            $jwt = JWT::encode($payload, $privateKey, 'RS256', $header['kid']);
            header('Content-Type: application/json');
            echo json_encode(array('Token' => $jwt));
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

$con->close();

?>