<?php

require('database.php');

$tableName = $_POST['tableName'] ?? '';
$objectName = $_POST['objectName'] ?? '';

if (empty($tableName) || empty($objectName)) {
    echo json_encode(['error' => 'Both table name and object name are required.']);
    exit;
}

$data = getDatabaseObjects($tableName);

if (!array_key_exists($objectName, $data)) {
    echo json_encode(['error' => 'Object does not exist.']);
    exit;
}

echo json_encode([$objectName => ['data' => $data[$objectName]]]);

?>