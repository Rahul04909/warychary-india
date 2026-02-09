<?php
$page = 'orders';
require_once '../auth_check.php';
include_once '../../database/db_config.php';

if (!isset($_GET['id'])) {
    die("Order ID is required.");
}

$order_id = intval($_GET['id']);
$database = new Database();
$db = $database->getConnection();

// Fetch Order
$query = "SELECT * FROM orders WHERE id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $order_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Order not found.");
}

// Fetch Order Items
$item_query = "SELECT * FROM order_items WHERE order_id = :oid";
$item_stmt = $db->prepare($item_query);
$item_stmt->bindParam(':oid', $order_id);
$item_stmt->execute();
$items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier Receipt - <?php echo htmlspecialchars($order['order_id']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; padding: 20px; }
        .receipt-header { border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .receipt-title { font-size: 24px; font-weight: bold; text-transform: uppercase; }
        .company-info { font-size: 14px; }
        .order-details { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .shipping-info, .tracking-info { width: 48%; border: 1px solid #ddd; padding: 15px; border-radius: 5px; }
        .section-title { font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #eee; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f9f9f9; }
        .barcode-placeholder { text-align: center; margin-top: 20px; border: 1px dashed #ccc; padding: 20px; color: #999; }
        .print-btn { display: block; width: 100%; padding: 15px; background: #333; color: #fff; border: none; font-size: 16px; cursor: pointer; border-radius: 5px; margin-top: 20px; }
        @media print {
            .print-btn { display: none; }
            body { padding: 0; }
            .shipping-info, .tracking-info { border: 1px solid #000; }
        }
    </style>
</head>
<body>
    <div class="receipt-header">
        <div class="company-info">
            <strong>WaryChary India</strong><br>
            New Delhi, India<br>
            support@warychary.com
        </div>
        <div class="receipt-title">Packing Slip / Receipt</div>
    </div>

    <div class="order-details">
        <div class="shipping-info">
            <div class="section-title">Delivery Address</div>
            <strong><?php echo htmlspecialchars($order['customer_name'] ?? 'Customer'); ?></strong><br>
            <?php echo nl2br(htmlspecialchars($order['address'] ?? '')); ?><br>
            <?php echo htmlspecialchars($order['city'] ?? '') . ', ' . htmlspecialchars($order['state'] ?? '') . ' - ' . htmlspecialchars($order['pincode'] ?? ''); ?><br>
            Phone: <?php echo htmlspecialchars($order['phone'] ?? $order['mobile'] ?? 'N/A'); ?>
        </div>
        <div class="tracking-info">
            <div class="section-title">Order Info</div>
            <strong>Order ID:</strong> <?php echo htmlspecialchars($order['order_id']); ?><br>
            <strong>Date:</strong> <?php echo date('d-M-Y', strtotime($order['created_at'])); ?><br>
            <strong>Payment:</strong> <?php echo ucfirst($order['payment_status']); ?><br>
            <br>
            <strong>Courier:</strong> <?php echo htmlspecialchars($order['courier_name'] ?? 'Pending'); ?><br>
            <strong>Tracking ID:</strong> <?php echo htmlspecialchars($order['tracking_id'] ?? 'Pending'); ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>SKU</th> <!-- Placeholder column -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name'] ?? $item['product_name'] ?? 'Product'); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo htmlspecialchars($item['sku'] ?? '-'); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="barcode-placeholder">
        <!-- Placeholder for Barcode -->
        *** <?php echo htmlspecialchars($order['tracking_id'] ?? $order['order_id']); ?> ***
    </div>

    <button class="print-btn" onclick="window.print()">Print Receipt</button>
</body>
</html>
