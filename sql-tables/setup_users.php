<?php
include_once '../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    try {
        // Create Users Table
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            partner_id INT(11) DEFAULT NULL,
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
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (partner_id) REFERENCES partners(id) ON DELETE SET NULL
        )";
        
        $db->exec($sql);
        echo "Table 'users' created successfully.<br>";

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Database connection failed.";
}
?>
