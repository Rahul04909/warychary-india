<?php
include_once '../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "Connected to database.<br>";
    
    // Add referral_code column
    try {
        $sql = "ALTER TABLE partners ADD COLUMN referral_code VARCHAR(50) DEFAULT NULL AFTER status";
        $db->exec($sql);
        echo "Column 'referral_code' added successfully.<br>";
        
        // Add unique index
        $sql_index = "ALTER TABLE partners ADD UNIQUE INDEX (referral_code)";
        $db->exec($sql_index);
        echo "Unique index added to 'referral_code'.<br>";
        
    } catch (PDOException $e) {
        // Ignore if column already exists
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
             echo "Column 'referral_code' already exists.<br>";
        } else {
             echo "Error adding 'referral_code': " . $e->getMessage() . "<br>";
        }
    }

    // Add commission column
    try {
        $sql = "ALTER TABLE partners ADD COLUMN commission DECIMAL(10,2) DEFAULT 15.00 AFTER referral_code";
        $db->exec($sql);
        echo "Column 'commission' added successfully.<br>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
             echo "Column 'commission' already exists.<br>";
        } else {
             echo "Error adding 'commission': " . $e->getMessage() . "<br>";
        }
    }

} else {
    echo "Database connection failed.";
}
?>
