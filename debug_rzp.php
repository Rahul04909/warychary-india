<?php
include_once __DIR__ . '/database/db_config.php';
$database = new Database();
$db = $database->getConnection();

echo "<h1>Razorpay Settings Dump</h1>";
$stmt = $db->query("SELECT * FROM razorpay_settings");
$settings = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($settings) {
    echo "<pre>";
    print_r($settings);
    echo "</pre>";
} else {
    echo "No settings found in table.";
}
?>
