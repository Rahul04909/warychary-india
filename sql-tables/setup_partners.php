<?php
include_once '../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    try {
        // Create Partners Table
        $sql = "CREATE TABLE IF NOT EXISTS partners (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            senior_partner_id INT(11) NOT NULL,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            mobile VARCHAR(20) NOT NULL,
            gender ENUM('Male', 'Female', 'Other') NOT NULL,
            image VARCHAR(255) DEFAULT NULL,
            state VARCHAR(100) NOT NULL,
            city VARCHAR(100) NOT NULL,
            pincode VARCHAR(20) NOT NULL,
            address TEXT NOT NULL,
            password VARCHAR(255) NOT NULL,
            status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (senior_partner_id) REFERENCES senior_partners(id) ON DELETE CASCADE
        )";
        
        $db->exec($sql);
        echo "Table 'partners' created successfully.<br>";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Database connection failed.";
}
?>
