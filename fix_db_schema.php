<?php
// Fix Database Schema for Earnings
include_once __DIR__ . '/database/db_config.php';
$database = new Database();
$db = $database->getConnection();

echo "Checking Database Schema...\n";

function addColumnIfNotExists($db, $table, $column, $definition) {
    try {
        $check = $db->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
        if ($check->rowCount() == 0) {
            echo "Adding '$column' to '$table'...\n";
            $db->exec("ALTER TABLE `$table` ADD `$column` $definition");
            echo "Success!\n";
        } else {
            echo "Column '$column' already exists in '$table'.\n";
        }
    } catch (PDOException $e) {
        echo "Error checking/adding column '$column' in '$table': " . $e->getMessage() . "\n";
    }
}

// 1. Check 'partners' table
addColumnIfNotExists($db, 'partners', 'earning', "DECIMAL(10,2) DEFAULT 0.00");
addColumnIfNotExists($db, 'partners', 'total_earnings', "DECIMAL(10,2) DEFAULT 0.00");

// 2. Check 'senior_partners' table
addColumnIfNotExists($db, 'senior_partners', 'earning', "DECIMAL(10,2) DEFAULT 0.00");
addColumnIfNotExists($db, 'senior_partners', 'total_earnings', "DECIMAL(10,2) DEFAULT 0.00");

echo "Schema Check Completed.\n";
?>
