<?php

$databasePath = "../../Database/data";
$databases = [];

function refreshDatabases() {
    global $databasePath, $databases;
    $databases = [];
    $files = glob($databasePath . "/*.json");

    foreach ($files as $file) {
        $tableName = basename($file, '.json');
        $content = file_get_contents($file);
        $databases[$tableName] = json_decode($content, true);
    }
}

function getDatabaseFilePath($tableName) {
    global $databasePath;
    return $databasePath . "/" . $tableName . ".json";
}

function getDatabaseObjects($tableName) {
    global $databases;
    if (!array_key_exists($tableName, $databases)) {
        $path = getDatabaseFilePath($tableName);
        if (file_exists($path)) {
            $content = file_get_contents($path);
            $databases[$tableName] = json_decode($content, true);
        } else {
            $databases[$tableName] = [];
        }
    }
    return $databases[$tableName];
}

function saveDatabaseObjects($tableName, $objects) {
    global $databases;
    $path = getDatabaseFilePath($tableName);
    $content = json_encode($objects, JSON_PRETTY_PRINT);
    file_put_contents($path, $content);
    $databases[$tableName] = $objects;
}

refreshDatabases();

?>