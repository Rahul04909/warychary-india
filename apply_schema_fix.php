<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/database/db_config.php';
$database = new Database();
$db = $database->getConnection();

echo "Applying Database Schema Fixes...\n";

function addColumn($db, $table, $column) {
    try {
        // Check if exists
        $check = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        if ($check->rowCount() == 0) {
            echo "Adding '$column' to '$table'...";
            $sql = "ALTER TABLE `$table` ADD `$column` DECIMAL(10,2) NOT NULL DEFAULT '0.00'";
            $db->exec($sql);
            echo " [DONE]\n";
        } else {
            echo "Column '$column' already exists in '$table'. [SKIPPED]\n";
        }
    } catch (PDOException $e) {
        echo "Error adding '$column' to '$table': " . $e->getMessage() . "\n";
    }
}

// Fix Partners Table
addColumn($db, 'partners', 'earning');
addColumn($db, 'partners', 'total_earnings');

// Fix Senior Partners Table
addColumn($db, 'senior_partners', 'earning');
addColumn($db, 'senior_partners', 'total_earnings');

echo "Database Fix Completed.\n";
?>
