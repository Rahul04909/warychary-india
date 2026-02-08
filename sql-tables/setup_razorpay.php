<?php
include_once __DIR__ . '/../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

try {
    // Create razorpay_settings table
    $sql_settings = "CREATE TABLE IF NOT EXISTS razorpay_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        key_id VARCHAR(255) NOT NULL,
        key_secret VARCHAR(255) NOT NULL,
        webhook_secret VARCHAR(255) DEFAULT NULL,
        currency VARCHAR(10) DEFAULT 'INR',
        mode ENUM('test', 'live') DEFAULT 'test',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    $db->exec($sql_settings);
    echo "Table 'razorpay_settings' created successfully.<br>";

    // Create razorpay_transactions table
    $sql_transactions = "CREATE TABLE IF NOT EXISTS razorpay_transactions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id VARCHAR(255) NOT NULL,
        payment_id VARCHAR(255) NOT NULL,
        amount DECIMAL(10, 2) NOT NULL,
        status VARCHAR(50) NOT NULL,
        email VARCHAR(255) DEFAULT NULL,
        contact VARCHAR(50) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $db->exec($sql_transactions);
    echo "Table 'razorpay_transactions' created successfully.<br>";

} catch (PDOException $e) {
    echo "Error creating tables: " . $e->getMessage();
}
?>
