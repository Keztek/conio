<?php

header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: *');

require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require('db.php');

$publicKey = file_get_contents('private/public.pem');
$header = [
    'typ' => 'JWT',
    'alg' => 'HS256',
    'kid' => 'q321q321'
];

//$_COOKIE['mode'] = 'dark';
if ($_COOKIE['mode'] == 'dark') {
    $nextMode = "light";
    $mode = '<i class="fa-solid fa-moon"></i>';
}
if ($_COOKIE['mode'] == 'light') {
    $nextMode = "system";
    $mode = '<i class="fa-solid fa-sun"></i>';
}
if ($_COOKIE['mode'] == 'system') {
    $nextMode = "dark";
    $mode = '<i class="fa-solid fa-gear"></i>';
}

$base_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
$request_url = $_SERVER['REQUEST_URI'];

$issuer = "https://conio.keztek.net";
$audience = "https://conio.keztek.net";

if (isset($_COOKIE['authtoken'])) {
    $token = $_COOKIE['authtoken'];

    if (empty($token)) {
        echo json_encode(['Error' => 'Token not provided']);
        exit;
    }

    $allowedAlgorithms = ['RS256'];

    try {
        $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

        if ($decoded->iss !== $issuer) {
            throw new Exception('Invalid issuer');
        }
        if ($decoded->aud !== $audience) {
            throw new Exception('Invalid audience');
        }

        $uuidClaim = $decoded->uuid ?? null;

        $sql = "SELECT * FROM webaccounts WHERE uuid = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $uuidClaim);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
        }

        //echo json_encode($decodedArray);
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['Error' => 'Invalid token']);
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConIO, up and coming tools and services for online game developers!</title>
    <link href='https://fonts.googleapis.com/css?family=Raleway:400|Roboto+Slab:700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/styles.css"/>
    <link rel="stylesheet" href="css/styles.css"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link id="themeStylesheet" rel="stylesheet" href="css/<?php echo $_COOKIE['mode']; ?>mode.css"/>
    <script src="js/cookie.js"></script>
    <script src="https://kit.fontawesome.com/84d944f889.js" crossorigin="anonymous"></script>
</head>
<body>
    <button class="modeBtn" onclick="setMode('<?php echo $nextMode; ?>')"><?php echo $mode; ?></button>
    <div class="nonfooter">
        <header>
            <div class="page">
                <hgroup>
                    <h1>
                        <a href="/">ConIO</a>
                    </h1>
                    <h2>The fastest way to build online games without breaking a sweat.</h2>
                </hgroup>
                <nav>
                    <ul>
                        <li class="">
                            <a href="/documentation/">Documentation</a>
                            <span class="selecteditem"></span>
                        </li>
                        <li class="">
                            <a href="/download/">SDK</a>
                        </li>
                    </ul>
                </nav>
                <div id="accountinfo">
                    <?php
                    if (!isset($_COOKIE['authtoken'])) {
                        echo '<a href="/register.php">Sign Up</a>';
                        echo ' | ';
                        echo '<a href="/login.php">Sign In</a>';
                    } else {
                        echo '<a href="#" class="dropdownlink" data-dropdown="accountdropdown">'.$user['username'].'</a>';
                        echo '<span class="dropdownindicator"></span>';
                        echo '<nav class="dropdown" id="accountdropdown"><a href="/my/go/account">My Account</a><a href="/logout">Signout</a><div class="sourcearrow"></div></nav>';
                    }
                    ?>
                </div>
            </div>
        </header>
        <div class="page">
            
        </div>
        <div style="clear:both"></div>
    </div>
    <footer></footer>
    <div class="bodyend"></div>
</body>
</html>