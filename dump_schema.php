<?php
include_once __DIR__ . '/database/db_config.php';

$database = new Database();
$db = $database->getConnection();

try {
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    $output = "";
    foreach ($tables as $table) {
        $output .= "Table: $table\n";
        $columns = $db->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            $output .= "  {$col['Field']} ({$col['Type']})\n";
        }
        $output .= "\n";
    }
    file_put_contents('db_schema_dump.txt', $output);
    echo "Schema dumped to db_schema_dump.txt";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
