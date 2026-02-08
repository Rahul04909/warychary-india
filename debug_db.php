<?php
require 'database/db_config.php';
$db = (new Database())->getConnection();

echo "--- ORDERS TABLE ---\n";
try {
    $stmt = $db->query("DESCRIBE orders");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) { echo $e->getMessage() . "\n"; }

echo "\n--- PARTNER EARNINGS TABLE ---\n";
try {
    $stmt = $db->query("DESCRIBE partner_earnings");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) { echo $e->getMessage() . "\n"; }

echo "\n--- SENIOR PARTNER EARNINGS TABLE ---\n";
try {
    $stmt = $db->query("DESCRIBE senior_partner_earnings");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) { echo "Table senior_partner_earnings likely does not exist.\n"; }

echo "\n--- PARTNERS SAMPLE ---\n";
try {
    $stmt = $db->query("SELECT id, name, referral_code, senior_partner_id FROM partners LIMIT 5");
    print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
} catch (Exception $e) { echo $e->getMessage() . "\n"; }
?>
