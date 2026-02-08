<?php
include_once __DIR__ . '/database/db_config.php';
$database = new Database();
$db = $database->getConnection();

$output = "Users Table Columns:\n";
try {
    $stmt = $db->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        $output .= $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Exception $e) {
    $output .= "Error: " . $e->getMessage();
}
file_put_contents(__DIR__ . '/debug_columns.txt', $output);
echo "Columns written to debug_columns.txt";
?>
