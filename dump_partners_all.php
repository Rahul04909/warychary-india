<?php
require 'database/db_config.php';
$db = (new Database())->getConnection();

$stmt = $db->query("SELECT * FROM partners LIMIT 10");
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($partners);
?>
