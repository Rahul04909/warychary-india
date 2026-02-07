<?php
include_once '../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "Connected to database.<br>";
    
    // Add referral_code column
    try {
        $sql = "ALTER TABLE senior_partners ADD COLUMN mobile VARCHAR(20) NOT NULL AFTER email";
        $db->exec($sql);
        echo "Column 'mobile' added successfully to 'senior_partners'.<br>";
        
    } catch (PDOException $e) {
        // Ignore if column already exists
        if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
             echo "Column 'mobile' already exists.<br>";
        } else {
             echo "Error adding 'mobile': " . $e->getMessage() . "<br>";
        }
    }

} else {
    echo "Database connection failed.";
}
?>
