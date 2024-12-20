<?php

error_reporting(0);

function sendMessageToServer($host, $port, $message) {
    $socket = fsockopen($host, $port, $errno, $errstr, 5);

    if (!$socket) {
        if ($errno === 10061) { // No connection could be made because the target machine actively refused it
            echo '<div class="server-status offline"><i class="fa-solid fa-circle"></i> Servers offline</div>';
        } else {
            echo "Failed to connect to server: $errstr ($errno)";
        }
    } else {
        fwrite($socket, $message);

        $response = fgets($socket);
        echo '<div class="server-status online"><i class="fa-solid fa-circle"></i> Servers online</div>';

        fclose($socket);
    }
}

$serverHost = '127.0.0.1';
$serverPort = 5526;
$message = "Hello from PHP!";

sendMessageToServer($serverHost, $serverPort, $message);

//function isPortInUse($port) {
//    $command = "netstat -na | find \"$port\"";
//    return exec($command, $output, $returnVar);
//
//    return $returnVar === 0;
//}
//
//$port = 5526;
//
//if (isPortInUse($port)) {
//    echo "Port $port is in use.";
//} else {
//    echo "Port $port is not in use.";
//}

$jsonFile = "data.json";
$data = readJson($jsonFile);

function readJson($filename) {
    $data = file_get_contents($filename);
    return json_decode($data, true);
}

function writeJson($filename, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents($filename, $json);
}

writeJson($jsonFile, $data);

?>