<?php
require_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/database/db_config.php';

use Mpdf\Mpdf;

function generateInvoicePDF($order_id, $output_mode = 'S') {
    $database = new Database();
    $db = $database->getConnection();

    // Fetch Order
    $query = "SELECT * FROM orders WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $order_id);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        return false;
    }

    // Fetch Order Items
    $item_query = "SELECT * FROM order_items WHERE order_id = :oid";
    $item_stmt = $db->prepare($item_query);
    $item_stmt->bindParam(':oid', $order_id);
    $item_stmt->execute();
    $items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Invoice HTML
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
            <img src="assets/logo/logo.png" style="width: 150px;">
            <span class="invoice-title">INVOICE</span>
        </div>

        <table class="details-table">
            <tr>
                <td class="client-info">
                    <strong>Billed To:</strong><br>
                    ' . htmlspecialchars($order['customer_name']) . '<br>
                    ' . nl2br(htmlspecialchars($order['shipping_address'])) . '<br>
                    ' . htmlspecialchars($order['shipping_city']) . ', ' . htmlspecialchars($order['shipping_state']) . ' - ' . htmlspecialchars($order['shipping_pincode']) . '<br>
                    Email: ' . htmlspecialchars($order['customer_email']) . '<br>
                    Phone: ' . htmlspecialchars($order['customer_mobile']) . '
                </td>
                <td class="order-info">
                    <strong>Order #:</strong> ' . $order['id'] . '<br>
                    <strong>Date:</strong> ' . date('F j, Y', strtotime($order['created_at'])) . '<br>
                    <strong>Payment ID:</strong> ' . htmlspecialchars($order['payment_id'] ?? 'N/A') . '<br>
                    <strong>Status:</strong> ' . ucfirst($order['payment_status']) . '
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
        $html .= '
                <tr>
                    <td>
                        ' . htmlspecialchars($item['product_name']) . '
                        ' . ($item['free_gift_name'] ? '<br><small><i>+ Free Gift: ' . htmlspecialchars($item['free_gift_name']) . '</i></small>' : '') . '
                    </td>
                    <td style="text-align: center;">' . $item['quantity'] . '</td>
                    <td style="text-align: right;">₹' . number_format($item['price'], 2) . '</td>
                    <td style="text-align: right;">₹' . number_format($item['total_price'], 2) . '</td>
                </tr>';
        $subtotal += $item['total_price'];
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
            <p>Support: support@warychary.com | +91-9813716032</p>
        </div>
    </body>
    </html>';

    try {
        $mpdf = new Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
        $mpdf->WriteHTML($html);
        return $mpdf->Output('', $output_mode);
    } catch (\Mpdf\MpdfException $e) {
        return false;
    }
}

// Handle Direct Download Request
if (isset($_GET['id']) && isset($_GET['download'])) {
    $order_id = intval($_GET['id']);
    // NOTE: In production, verify user session/auth here before allowing download
    $pdfContent = generateInvoicePDF($order_id, 'D'); // D for Download
    exit;
}
?>
