<?php
include_once __DIR__ . '/../../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

try {
    $check = $db->query("SHOW COLUMNS FROM orders LIKE 'status'");
    if ($check->rowCount() > 0) {
        echo "Column 'status' EXISTS.\n";
    } else {
        echo "Column 'status' DOES NOT EXIST.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
