<?php
include_once __DIR__ . '/../../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

$order_id_public = 'order_SDhcNoiuqE120G'; // From screenshot

try {
    $stmt = $db->prepare("SELECT * FROM orders WHERE order_id = :oid");
    $stmt->execute([':oid' => $order_id_public]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        echo "Order Found:\n";
        print_r($order);
    } else {
        echo "Order NOT Found for ID: $order_id_public\n";
        // Let's list a few recent orders to see what they look like
        $stmt = $db->query("SELECT id, order_id, payment_status, dispatched_date FROM orders ORDER BY created_at DESC LIMIT 10");
        echo "\nRecent Orders:\n";
        echo "\nRecent Orders:\n";
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
        var_dump($orders);
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
