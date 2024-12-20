<?php

$jsonData = file_get_contents('game.json');
$json = json_decode($jsonData, true);

$gameTitle = $json['GameTitle'];
$gamePort = $json['Port'];
$gameQueryPort = $json['QueryPort'];

?>