<?php
require 'database/db_config.php';
$db = (new Database())->getConnection();

echo "--- SENIOR PARTNERS ---\n";
$stmt = $db->query("DESCRIBE senior_partners");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "--- PARTNERS ---\n";
$stmt = $db->query("DESCRIBE partners");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
