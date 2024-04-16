<?php

require('database.php');

$tableName = $_POST['tableName'];
$objectName = $_POST['objectName'];

$path = $databasePath."/".$tableName.".json";
$table = getDatabaseObjects($tableName);
return array_key_exists($objectName, $table);

?>