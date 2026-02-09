<?php
require_once 'database/db_config.php';
$database = new Database();
$db = $database->getConnection();
$stmt = $db->query("SELECT id, name, email FROM senior_partners LIMIT 5");
$partners = $stmt->fetchAll(PDO::FETCH_ASSOC);
print_r($partners);
?>
