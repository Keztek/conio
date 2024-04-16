<?php

require('database.php');

$tableName = $_POST['tableName'] ?? '';
$objectName = $_POST['objectName'] ?? '';
$objectData = $_POST['objectData'] ?? '{}'; // Assuming JSON string

if (empty($tableName) || empty($objectName)) {
    echo json_encode(['error' => 'Table name and object name are required.']);
    exit;
}

$data = getDatabaseObjects($tableName);
if (array_key_exists($objectName, $data)) {
    echo json_encode(['error' => 'Object already exists.']);
    exit;
}

$data[$objectName] = json_decode($objectData, true);
saveDatabaseObjects($tableName, $data);
echo json_encode(['success' => 'Object created successfully.']);

?>