<?php
require 'database/db_config.php';
$db = (new Database())->getConnection();

// 1. Get partner_earnings structure to copy
echo "--- PARTNER EARNINGS CREATE STATEMENT ---\n";
try {
    $stmt = $db->query("SHOW CREATE TABLE partner_earnings");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row['Create Table'] . "\n";
} catch (Exception $e) { echo "Error: " . $e->getMessage() . "\n"; }

// 2. Create senior_partner_earnings table if not exists
$sql = "CREATE TABLE IF NOT EXISTS `senior_partner_earnings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `senior_partner_id` int(11) NOT NULL,
  `source_partner_id` int(11) DEFAULT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `percentage` decimal(5,2) NOT NULL DEFAULT 2.00,
  `status` enum('pending','paid') DEFAULT 'pending',
  `description` text,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
)";

try {
    $db->exec($sql);
    echo "senior_partner_earnings table created successfully.\n";
} catch (Exception $e) {
    echo "Error creating table: " . $e->getMessage() . "\n";
}
?>
