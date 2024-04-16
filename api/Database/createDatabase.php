<?php

require('database.php');

$tableName = $_POST['tableName'] ?? '';

if (empty($tableName)) {
    echo json_encode(['error' => 'Table name is required.']);
    exit;
}

$path = getDatabaseFilePath($tableName);
if (!file_exists($path)) {
    file_put_contents($path, "{}");
    refreshDatabases();
    echo json_encode(['success' => 'Database created successfully.']);
} else {
    echo json_encode(['error' => 'Database already exists.']);
}

?>