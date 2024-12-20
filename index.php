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

require('gameInfo.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConIO, up and coming tools and services for online game developers!</title>
    <link rel="shortcut icon" href="Icon.svg" type="image/x-icon">
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
        <div class="adminheader">
            <div class="page">
                <div class="help"></div>
                <h1>
                    <span class="headerprefix">
                        <?php

                        echo "<a href=\"#\">$gameTitle";

                        ?>
                    </span>
                    <span id="db">Information</span>
                    <a href="#documentation" class="documentationlink">
                        <span id="documentationlinkicon">?</span>
                        Help & Documentation
                    </a>
                </h1>
            </div>
        </div>
        <div class="page">
            <nav class="leftrail">
                <nav>
                    <ul class="menu active">
                        <li>
                            <a href="<?php echo $base_url; ?>/Account/">Account</a>
                        </li>
                        <li>
                            <a href="<?php echo $base_url; ?>/Database/">Database</a>
                            <ul class="menu active" id="db-list"></ul>
                        </li>
                        <li>
                            <a href="<?php echo $base_url; ?>/ErrorLog/">Error log</a>
                        </li>
                        <li>
                            <a href="<?php echo $base_url; ?>/Multiplayer/">Multiplayer</a>
                        </li>
                        <li>
                            <a href="<?php echo $base_url; ?>/Store/">Store</a>
                        </li>
                    </ul>
                </nav>
            </nav>
            <div class="mainrail withleftrail">
                <div class="innermainrail">
                    <section>
                        <div class="offsetbox">
                            <table class="box">
                                <tbody>
                                    <tr>
                                        <th style="width:100px;vertical-align:middle;">
                                            <h3 style="margin:0px">Address:</h3>
                                        </th>
                                        <td style="vertical-align:middle;">
                                            <h3 style="font-weight:normal:margin:0px;">127.0.0.1:<?php echo $gamePort; ?></h3>
                                        </td>
                                        <th style="width:100px;vertical-align:middle;">
                                            <h3 style="margin:0px">Query:</h3>
                                        </th>
                                        <td style="vertical-align:middle;">
                                            <h3 style="font-weight:normal:margin:0px;">127.0.0.1:<?php echo $gameQueryPort; ?></h3>
                                        </td>
                                        <td style="width:100px;text-align:right;">
                                            <a href="#" class="button positive">Edit Settings</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                    <section>
                        <h3>Multiplayer</h3>
                        <div class="offsetbox">
                            <table class="box" style="margin-bottom:0px">
                                <tbody>
                                    <tr>
                                        <td class="white center" style="vertical-align:middle">
                                            <h2 style="margin:0px">Active Rooms: 0</h2>
                                        </td>
                                        <td class="white center" style="vertical-align:middle">
                                            <h2 style="margin:0px">Current Online: 0</h2>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>
        </div>
        <div style="clear:both"></div>
    </div>
    <footer>
    	<div class="footertop">
    		<div class="page">
                <?php
                
                $sql = "SELECT * FROM about ORDER BY id ASC";
                $result = $con->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo $row['body'];
                    }
                }

                ?>
    			<div style="clear:both"></div>
    		</div>
    	</div>
    	<div class="footerend">
    		<div class="page">
    			<a href="mailto:hello@playerio.com">conio@keztek.net</a>
    			<div class="rightfooter">
    				<a href="/status">Service Status</a>
    				<a href="/privacypolicy">Privacy Policy</a>
    				<a href="/termsofservice">Terms of Service</a>
    				© Keztek Ltd.
    			</div>
    		</div>
    	</div>
    	<script>
    	  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    	  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    	  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    	  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');
    	  ga('create', 'UA-92600566-2', 'auto');
    	  ga('send', 'pageview');
    	</script>
    </footer>
    <div class="bodyend"></div>
</body>
</html>