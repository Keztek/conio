<?php

header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Origin: *');

require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require('../db.php');

$publicKey = file_get_contents('../private/public.pem');
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
    <link rel="shortcut icon" href="https://conio.keztek.net/Icon.svg" type="image/x-icon">
    <link href='https://fonts.googleapis.com/css?family=Raleway:400|Roboto+Slab:700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="<?php echo $base_url; ?>/css/styles.css"/>
    <link rel="stylesheet" href="css/style.css"/>
    <link id="themeStylesheet" rel="stylesheet" href="https://conio.keztek.net/css/<?php echo $_COOKIE['mode']; ?>mode.css"/>
    <script src="https://conio.keztek.net/js/cookie.js"></script>
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
                        <a href="#">GameName</a>
                    </span>
                    <span id="db">Database</span>
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
                            <a href="<?php echo $base_url; ?>/Database/" class="active">Database</a>
                            <ul class="menu active" id="db-list">
                            </ul>
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
                    <div class="boxtabs"></div>
                    <div class="offsetbox">
                        <table class="bigdb-query-table"></table>
                        <div id="bigdbsearchresults"></div>
                        <table>
                            <tbody id="offsetbox">
                            </tbody>
                        </table>
                    </div>
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
    <script src="js/database.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const database = urlParams.get('db');

            if (database) {
                let dbLbl = document.getElementById('db');
                dbLbl.innerHTML = '<a href="/Database/">Database</a><span id="db-val"> / '+database+'</span>';
                getDatabaseObjects(database);
            } else {
                let dbs = document.getElementById('offsetbox');
                dbs.innerHTML += '<h3>Tables</h3>';
                dbs.innerHTML += '<tr><th>Name</th><th nowrap="nowrap">Database Objects</th><th></th></tr>';
                dbs.innerHTML += '<tr class="colrow"></tr>';
                dbs.innerHTML += '<tr class="actionrow" id="actionrow"><td colspan="2" style="text-align:left"><a class="button negative" href="">Delete Tables...</a><a class="button negative" href="">Truncate Tables...</a><a class="button positive" href="">Copy Tables...</a><a class="button positive" href="">Export Data...</a></td><td><a class="button positive" href="">CreateTable</a></td></tr>';
                let actionrow = document.getElementById('actionrow');

                fetch('/../../api/Database/getDatabases.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    let keys = Object.keys(data[0]);
                    keys.sort();
                    let values = Object.values(data[0]);
                    for (let i = 0; i < keys.length; i++) {
                        let dboCount = 0;
                        let bytes = 0;
                        fetch('/../../api/Database/getDatabaseObjects.php', {
                            method: 'POST',
                            body: new URLSearchParams({ 'tableName': values[i] }),
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                        })
                        .then(response => response.json())
                        .then(data2 => {
                            dboCount = Object.keys(data2[0]).length;
                            bytes = new Blob([JSON.stringify(data2)]).size;
                            let bytesStr = bytes + ' bytes';
                            if (bytes < 1024) {
                                bytesStr = bytes + ' bytes';
                            } else if (bytes < 1024 * 1024) {
                                bytesStr = (bytes / 1024).toFixed(2) + ' KB';
                            } else {
                                bytesStr = (bytes / (1024 * 1024)).toFixed(2) + ' MB';
                            }
                            dbs.innerHTML += '<tr class="colrow"><td class="white" style="width:100%"><a class="big" href="?db=' + values[i] + '">' + values[i] + '</a><p>!FIX.Description.</p></td><td class="white" nowrap="nowrap">' + dboCount + ' objects, ' + bytesStr + '<div class="small gray">(calculated less than a minute ago)</div></td><td class="white" align="right" nowrap="nowrap"><a class="button" href="">Edit Table ></a></td></tr>';
                            dbs.innerHTML = dbs.innerHTML.replace('<tr class="actionrow" id="actionrow"><td colspan="2" style="text-align:left"><a class="button negative" href="">Delete Tables...</a><a class="button negative" href="">Truncate Tables...</a><a class="button positive" href="">Copy Tables...</a><a class="button positive" href="">Export Data...</a></td><td><a class="button positive" href="">CreateTable</a></td></tr>', '');
                            dbs.innerHTML += '<tr class="actionrow" id="actionrow"><td colspan="2" style="text-align:left"><a class="button negative" href="">Delete Tables...</a><a class="button negative" href="">Truncate Tables...</a><a class="button positive" href="">Copy Tables...</a><a class="button positive" href="">Export Data...</a></td><td><a class="button positive" href="">CreateTable</a></td></tr>';
                        })
                        .catch(error2 => console.error("Error:", error2));
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    </script>
</body>
</html>