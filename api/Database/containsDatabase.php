<?php

require('database.php');

$tableName = $_POST['tableName'] ?? '';

if (empty($tableName)) {
    echo json_encode(['error' => 'Table name is required.']);
    exit;
}

$path = getDatabaseFilePath($tableName);
if (file_exists($path)) {
    echo json_encode(['exists' => true]);
} else {
    echo json_encode(['exists' => false]);
}

?>