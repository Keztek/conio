<?php

require('database.php');

function getAllDatabaseNames() {
    global $databasePath;
    $files = glob($databasePath . "/*.json");
    $databaseNames = [];

    foreach ($files as $file) {
        $databaseNames[] = basename($file, '.json');
    }

    return $databaseNames;
}

$databaseNames = getAllDatabaseNames();
echo json_encode([$databaseNames]);

?>