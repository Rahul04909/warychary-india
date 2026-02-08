<?php
require 'database/db_config.php';
$db = (new Database())->getConnection();

$stmt = $db->query("SELECT id, name, referral_code, senior_partner_id FROM partners WHERE status='active' LIMIT 10");
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

file_put_contents('partners_dump.log', print_r($partners, true));
echo "Partners dumped to partners_dump.log";
?>
