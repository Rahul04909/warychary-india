<?php
$host = 'localhost';
$username = 'jhdindus_warycharycare';
$password = 'Rd14072003@./';
$db_name = 'jhdindus_warycharycare';

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name`");
    
    // Connect to the database
    $pdo->exec("USE `$db_name`");

    // Create Senior Partners Table
    $sql = "CREATE TABLE IF NOT EXISTS senior_partners (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        image VARCHAR(255),
        gender ENUM('Male', 'Female', 'Other') NOT NULL,
        state VARCHAR(100),
        city VARCHAR(100),
        pincode VARCHAR(20),
        address TEXT,
        password VARCHAR(255) NOT NULL,
        referral_code VARCHAR(6) NOT NULL UNIQUE,
        commission DECIMAL(5,2) DEFAULT 2.00,
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    $pdo->exec($sql);
    echo "Table 'senior_partners' created successfully in database '$db_name'.<br>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
