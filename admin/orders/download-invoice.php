<?php
session_start();
// Admin Auth
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

require_once '../../vendor/autoload.php';
include_once '../../database/db_config.php';

use Mpdf\Mpdf;

if (!isset($_GET['id'])) {
    die("Order ID is required.");
}

$order_id = intval($_GET['id']);
$database = new Database();
$db = $database->getConnection();

// Fetch Order
// Fetch Order with User Details
// orders table uses shipping_ prefix for address. users table uses plain address.
$query = "SELECT o.*, 
          u.name as user_name, u.email as user_email, u.mobile as user_mobile,
          u.address as user_address, u.city as user_city, u.state as user_state, u.pincode as user_pincode 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE o.id = :id";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $order_id);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Customer Details Logic
// Priority: Order Shipping Details > Order Details (legacy) > User Profile Details
$c_name = $order['customer_name'] ?? $order['user_name'] ?? 'Customer';
$c_email = $order['customer_email'] ?? $order['user_email'] ?? '';
$c_mobile = $order['customer_mobile'] ?? $order['phone'] ?? $order['mobile'] ?? $order['user_mobile'] ?? '';

$c_address = $order['shipping_address'] ?? $order['address'] ?? $order['user_address'] ?? '';
$c_city = $order['shipping_city'] ?? $order['city'] ?? $order['user_city'] ?? '';
$c_state = $order['shipping_state'] ?? $order['state'] ?? $order['user_state'] ?? '';
$c_pincode = $order['shipping_pincode'] ?? $order['pincode'] ?? $order['user_pincode'] ?? '';

if (!$order) {
    die("Order not found.");
}

// Fetch Order Items
$item_query = "SELECT * FROM order_items WHERE order_id = :oid";
$item_stmt = $db->prepare($item_query);
$item_stmt->bindParam(':oid', $order_id);
$item_stmt->execute();
$items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch User (Customer) Info if not fully in order table
// (Assuming order table has shipping info snapshot, which it should)

// Invoice HTML (Reused structure)
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; font-size: 10pt; color: #333; }
        .header { width: 100%; border-bottom: 2px solid #ddd; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #333; }
        .invoice-title { float: right; font-size: 20px; color: #666; }
        .details-table { width: 100%; margin-bottom: 20px; }
        .details-table td { vertical-align: top; }
        .client-info { width: 50%; }
        .order-info { width: 50%; text-align: right; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .items-table th { background: #f9f9f9; padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
        .items-table td { padding: 10px; border-bottom: 1px solid #eee; }
        .total-row td { font-weight: bold; border-top: 2px solid #ddd; background: #f9f9f9; }
        .footer { margin-top: 50px; text-align: center; font-size: 9pt; color: #777; border-top: 1px solid #eee; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">WaryChary</div>
        <span class="invoice-title">INVOICE</span>
    </div>

    <table class="details-table">
        <tr>
            <td class="client-info">
                <strong>Billed To:</strong><br>
                ' . htmlspecialchars($c_name) . '<br>
                ' . nl2br(htmlspecialchars($c_address)) . '<br>
                ' . htmlspecialchars($c_city) . ', ' . htmlspecialchars($c_state) . ' - ' . htmlspecialchars($c_pincode) . '<br>
                Phone: ' . htmlspecialchars($c_mobile) . '<br>
                Email: ' . htmlspecialchars($c_email) . '
            </td>
            <td class="order-info">
                <strong>Order #:</strong> ' . $order['order_id'] . '<br>
                <strong>Date:</strong> ' . date('F j, Y', strtotime($order['created_at'])) . '<br>
                <strong>Payment Status:</strong> ' . ucfirst($order['payment_status']) . '<br>
                <strong>Method:</strong> ' . htmlspecialchars($order['payment_method'] ?? 'Online') . '
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th width="50%">Item</th>
                <th width="15%" style="text-align: center;">Qty</th>
                <th width="15%" style="text-align: right;">Price</th>
                <th width="20%" style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>';

$subtotal = 0;
foreach ($items as $item) {
    // Determine product name column
    $prod_name = $item['product_name'] ?? $item['name'] ?? 'Product';
    $qty = $item['quantity'] ?? 1;
    $price = $item['price'] ?? 0;
    $total = $item['total_price'] ?? ($price * $qty);
    
    $html .= '
            <tr>
                <td>' . htmlspecialchars($prod_name) . '</td>
                <td style="text-align: center;">' . $qty . '</td>
                <td style="text-align: right;">₹' . number_format($price, 2) . '</td>
                <td style="text-align: right;">₹' . number_format($total, 2) . '</td>
            </tr>';
    $subtotal += $total;
}

$html .= '
            <tr class="total-row">
                <td colspan="3" style="text-align: right;">Grand Total:</td>
                <td style="text-align: right;">₹' . number_format($order['total_amount'], 2) . '</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Thank you for shopping with WaryChary!</p>
        <p>Support: support@warychary.com</p>
    </div>
</body>
</html>';

try {
    $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
    $mpdf->WriteHTML($html);
    $mpdf->Output('Invoice_' . $order['order_id'] . '.pdf', 'D'); // Force Download
} catch (\Mpdf\MpdfException $e) {
    echo "Error generating PDF: " . $e->getMessage();
}
?>
