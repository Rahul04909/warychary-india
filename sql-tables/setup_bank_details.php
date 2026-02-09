<?php
require_once '../database/db_config.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Create bank_details table
    $sql = "CREATE TABLE IF NOT EXISTS bank_details (
        id INT AUTO_INCREMENT PRIMARY_KEY,
        user_id INT NOT NULL,
        user_type ENUM('partner', 'senior_partner') NOT NULL,
        account_holder_name VARCHAR(255) NOT NULL,
        account_number VARCHAR(50) NOT NULL,
        ifsc_code VARCHAR(20) NOT NULL,
        bank_name VARCHAR(255) NOT NULL,
        branch_address TEXT,
        bank_code VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY unique_user_bank (user_id, user_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $db->exec($sql);
    echo "Table 'bank_details' created successfully.<br>";

} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
