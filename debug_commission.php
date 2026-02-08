<?php
require 'database/db_config.php';
$db = (new Database())->getConnection();

// Check partner_earnings schema
echo "--- PARTNER_EARNINGS COLUMNS ---\n";
try {
    $stmt = $db->query("DESCRIBE partner_earnings");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")\n";
    }
} catch (Exception $e) { echo "partner_earnings table error: " . $e->getMessage() . "\n"; }

// Check orders schema
echo "\n--- ORDERS COLUMNS ---\n";
try {
    $stmt = $db->query("DESCRIBE orders");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
        if (in_array($col['Field'], ['partner_id', 'senior_partner_id'])) {
            echo $col['Field'] . " (" . $col['Type'] . ")\n";
        }
    }
} catch (Exception $e) { echo "orders table error: " . $e->getMessage() . "\n"; }

// Check if senior_partner_earnings exists
echo "\n--- SENIOR_PARTNER_EARNINGS ---\n";
try {
    $db->query("SELECT 1 FROM senior_partner_earnings LIMIT 1");
    echo "Table exists.\n";
} catch (Exception $e) { echo "Table does not exist.\n"; }

// Check random partner with senior partner
echo "\n--- PARTNER DATA ---\n";
$stmt = $db->query("SELECT id, name, senior_partner_id FROM partners WHERE senior_partner_id IS NOT NULL LIMIT 1");
$p = $stmt->fetch(PDO::FETCH_ASSOC);
if ($p) {
    echo "Found Partner ID: " . $p['id'] . " with Senior Partner ID: " . $p['senior_partner_id'] . "\n";
} else {
    echo "No partners found with senior_partner_id set.\n";
}
?>
