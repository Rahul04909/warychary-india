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

// Fetch Order with User Details
// We prioritize order table data for address snapshot, but fall back to user table if needed or for email
$query = "SELECT o.*, u.name as user_name, u.email as user_email, u.mobile as user_mobile 
          FROM orders o 
          LEFT JOIN users u ON o.user_id = u.id 
          WHERE o.id = :id";
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

// Company Details (Hardcoded as per request)
$company_email = "support@warychary.com";
// Placeholder for Mobile and GST as they were not found in codebase
$company_mobile = "+91 98765 43210"; 
$company_gst = "GSTIN: 07AABCU9603R1Z2"; // Example Placeholder, user asked to put "GST" below logo.

// Customer Details Logic
// Use order-specific fields if available (snapshots), otherwise user fields
$c_name = $order['user_name'] ?? 'Customer';
$c_email = $order['user_email'] ?? '';
$c_mobile = $order['phone'] ?? $order['mobile'] ?? $order['user_mobile'] ?? '';
$c_address = $order['address'] ?? '';
$c_city = $order['city'] ?? '';
$c_state = $order['state'] ?? '';
$c_pincode = $order['pincode'] ?? '';

// Receipt HTML strictly for 3x5 inch (76.2mm x 127mm)
$html = '
<!DOCTYPE html>
<html>
<head>
    <style>
        @page {
            margin: 2mm;
        }
        body { 
            font-family: sans-serif; 
            font-size: 10px; 
            color: #000; 
            line-height: 1.2;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bold { font-weight: bold; }
        
        .header { 
            border-bottom: 2px solid #000; 
            padding-bottom: 5px; 
            margin-bottom: 5px; 
            text-align: center;
        }
        .logo { 
            width: 120px; 
            max-height: 50px; 
            object-fit: contain;
            margin-bottom: 5px;
        }
        .company-details { font-size: 8px; margin-top: 2px; }
        .receipt-title { font-size: 12px; font-weight: bold; margin-top: 5px; text-decoration: underline; }
        
        .section { margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px dashed #666; }
        .section:last-child { border-bottom: none; }
        
        .label { font-size: 8px; color: #333; text-transform: uppercase; font-weight: bold; }
        .value { font-size: 10px; }
        .address-block { font-size: 10px; margin-top: 2px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th { text-align: left; font-size: 9px; border-bottom: 1px solid #000; font-weight: bold; }
        td { font-size: 9px; padding: 2px 0; border-bottom: 1px solid #ddd; }
        
        .footer { 
            margin-top: 10px; 
            text-align: center; 
            font-size: 8px; 
            border-top: 1px solid #000; 
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="../../assets/logo/logo.png" class="logo"><br>
        <div class="company-details">
            ' . $company_gst . '<br>
            Email: ' . $company_email . ' | Mobile: ' . $company_mobile . '
        </div>
        <div class="receipt-title">COURIER RECEIPT</div>
    </div>

    <div class="section">
        <div class="label">DELIVERY TO:</div>
        <div class="value bold" style="font-size: 11px;">' . htmlspecialchars($c_name) . '</div>
        <div class="value">Phone: ' . htmlspecialchars($c_mobile) . '</div>
        <div class="value">Email: ' . htmlspecialchars($c_email) . '</div>
        <div class="address-block">
            ' . nl2br(htmlspecialchars($c_address)) . '<br>
            ' . htmlspecialchars($c_city) . ', ' . htmlspecialchars($c_state) . ' - ' . htmlspecialchars($c_pincode) . '
        </div>
    </div>

    <div class="section">
        <table style="width: 100%">
            <tr>
                <td style="border:none">
                    <div class="label">Order ID</div>
                    <div class="value bold">#' . htmlspecialchars($order['order_id']) . '</div>
                </td>
                <td style="border:none; text-align: right;">
                    <div class="label">Date</div>
                    <div class="value">' . date('d-M-Y', strtotime($order['created_at'])) . '</div>
                </td>
            </tr>
            <tr>
                <td style="border:none; padding-top: 5px;">
                    <div class="label">Payment</div>
                    <div class="value">' . ucfirst($order['payment_status']) . '</div>
                </td>
                 <td style="border:none; padding-top: 5px; text-align: right;">
                    <div class="label">Courier</div>
                    <div class="value">' . htmlspecialchars($order['courier_name'] ?? 'Pending') . '</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <table>
            <thead>
                <tr>
                    <th width="70%">Item</th>
                    <th width="15%" class="text-center">Qty</th>
                    <th width="15%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>';

foreach ($items as $item) {
    $name = $item['product_name'] ?? $item['name'] ?? 'Item';
    $qty = $item['quantity'];
    if (strlen($name) > 30) $name = substr($name, 0, 28) . '..';
    
    $html .= '
                <tr>
                    <td>' . htmlspecialchars($name) . '</td>
                    <td class="text-center">' . $qty . '</td>
                     <td class="text-right">'.number_format($item['total_price'],0).'</td>
                </tr>';
}

$html .= '
            </tbody>
        </table>
    </div>
    
    <div class="section text-center">
         <div class="label">Your Order ID</div>
         <div class="value" style="font-size: 14px; letter-spacing: 1px; font-weight: bold;">' . htmlspecialchars($order['order_id']) . '</div>
    </div>

    <div class="footer">
        Thank you for choosing WaryChary!
    </div>
</body>
</html>';

try {
    // 3x5 inches = 76.2mm x 127mm
    $mpdf = new Mpdf([
        'mode' => 'utf-8', 
        'format' => [76.2, 127], 
        'margin_left' => 2,
        'margin_right' => 2,
        'margin_top' => 2,
        'margin_bottom' => 2,
        'default_font' => 'sans-serif'
    ]);
    
    $mpdf->WriteHTML($html);
    $mpdf->Output('Receipt_' . $order['order_id'] . '.pdf', 'I'); 
} catch (\Mpdf\MpdfException $e) {
    echo "Error generating PDF: " . $e->getMessage();
}
?>
