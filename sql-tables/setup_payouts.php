<?php
require_once __DIR__ . '/../database/db_config.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Create payout_history table
    $sql = "CREATE TABLE IF NOT EXISTS payout_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        user_type ENUM('partner', 'senior_partner') NOT NULL,
        amount DECIMAL(10,2) NOT NULL,
        payment_mode VARCHAR(50) DEFAULT 'Bank Transfer',
        transaction_id VARCHAR(100),
        status ENUM('pending', 'processed', 'failed') DEFAULT 'processed',
        admin_note TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_user (user_id, user_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql);
    echo "Table 'payout_history' created successfully.<br>";

} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
