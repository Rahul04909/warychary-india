<?php
include_once '../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    try {
        // Create SMTP Settings Table
        $sql = "CREATE TABLE IF NOT EXISTS smtp_settings (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            host VARCHAR(255) NOT NULL,
            username VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            port INT(5) NOT NULL,
            encryption ENUM('tls', 'ssl', 'none') DEFAULT 'tls',
            from_email VARCHAR(255) NOT NULL,
            from_name VARCHAR(255) NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        $db->exec($sql);
        echo "Table 'smtp_settings' created successfully.<br>";

        // Insert default row if empty
        $check = $db->query("SELECT count(*) FROM smtp_settings")->fetchColumn();
        if ($check == 0) {
            $sql_insert = "INSERT INTO smtp_settings (host, username, password, port, encryption, from_email, from_name) 
                           VALUES ('smtp.example.com', 'user@example.com', 'password', 587, 'tls', 'no-reply@warychary.in', 'WaryChary Admin')";
            $db->exec($sql_insert);
            echo "Default SMTP settings inserted.<br>";
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Database connection failed.";
}
?>
