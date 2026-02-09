<?php
include_once __DIR__ . '/../../database/db_config.php';

$database = new Database();
$db = $database->getConnection();

echo "Adding status column to orders table...\n";

try {
    $check = $db->query("SHOW COLUMNS FROM orders LIKE 'status'");
    if ($check->rowCount() == 0) {
        // Add status column, default to 'pending'
        $sql = "ALTER TABLE orders ADD COLUMN status VARCHAR(50) DEFAULT 'pending'";
        $db->exec($sql);
        echo "Column 'status' added successfully.\n";
        
        // Update existing orders: 
        // If dispatched_date IS NOT NULL -> 'completed'
        // Else if payment_status IN ('captured', 'paid', 'success') -> 'processing' (or keep pending/captured)
        // Let's just set completed ones.
        $db->exec("UPDATE orders SET status = 'completed' WHERE dispatched_date IS NOT NULL");
        echo "Updated status for dispatched orders.\n";
        
    } else {
        echo "Column 'status' already exists.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
