<?php
include_once __DIR__ . '/../../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

echo "Checking orders table structure...\n";

try {
    // Check if columns exist
    $columns = [
        'courier_name' => "VARCHAR(255) NULL",
        'tracking_id' => "VARCHAR(255) NULL",
        'dispatched_from' => "VARCHAR(255) NULL",
        'dispatched_date' => "DATETIME NULL"
    ];

    foreach ($columns as $column => $definition) {
        $check = $db->query("SHOW COLUMNS FROM orders LIKE '$column'");
        if ($check->rowCount() == 0) {
            echo "Adding column: $column\n";
            $sql = "ALTER TABLE orders ADD COLUMN $column $definition";
            $db->exec($sql);
            echo "Column $column added successfully.\n";
        } else {
            echo "Column $column already exists.\n";
        }
    }
    
    echo "Database update completed.\n";

} catch (PDOException $e) {
    echo "Error updating database: " . $e->getMessage() . "\n";
}
?>
