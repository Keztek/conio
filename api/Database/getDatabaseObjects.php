<?php

require('database.php');

$tableName = $_POST['tableName'] ?? '';

if (empty($tableName)) {
    echo json_encode(['error' => 'Table name is required.']);
    exit;
}

$data = getDatabaseObjects($tableName);

echo json_encode([$data]);

?>