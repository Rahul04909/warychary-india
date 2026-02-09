<?php
session_start();
include_once '../../database/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: ../login.php");
        exit;
    }

    $database = new Database();
    $db = $database->getConnection();

    $order_id = $_POST['order_id'];
    $courier_name = $_POST['courier_name'];
    $tracking_id = $_POST['tracking_id'];
    $dispatched_from = $_POST['dispatched_from'];

    // --- SMTP & Email Logic ---

    // 1. Fetch Order & User Details
    $query = "SELECT o.*, u.name as user_name, u.email as user_email 
              FROM orders o
              LEFT JOIN users u ON o.user_id = u.id 
              WHERE o.id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $order_id);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Fetch Order Items
    $item_query = "SELECT * FROM order_items WHERE order_id = :oid";
    $item_stmt = $db->prepare($item_query);
    $item_stmt->bindParam(':oid', $order['id']);
    $item_stmt->execute();
    $items = $item_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Update Order Status
    try {
        $sql = "UPDATE orders 
                SET courier_name = :courier_name, 
                    tracking_id = :tracking_id, 
                    dispatched_from = :dispatched_from, 
                    dispatched_date = NOW(),
                    status = 'completed' 
                WHERE id = :id";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':courier_name', $courier_name);
        $stmt->bindParam(':tracking_id', $tracking_id);
        $stmt->bindParam(':dispatched_from', $dispatched_from);
        $stmt->bindParam(':id', $order_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Order dispatched successfully.";

            // 4. Send Email
            if (file_exists('../../vendor/autoload.php')) {
                require_once '../../vendor/autoload.php';
            }

            // Fetch SMTP Settings
            $smtpSettings = [];
            try {
                $smtpStmt = $db->prepare("SELECT * FROM smtp_settings LIMIT 1");
                $smtpStmt->execute();
                $smtpSettings = $smtpStmt->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                error_log("SMTP Params Error: " . $e->getMessage());
            }

            if ($order && !empty($order['user_email']) && !empty($smtpSettings)) {
                $mail = new PHPMailer\PHPMailer\PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = $smtpSettings['host'];
                    $mail->SMTPAuth   = true;
                    $mail->Username   = $smtpSettings['username'];
                    $mail->Password   = $smtpSettings['password'];
                    $mail->SMTPSecure = $smtpSettings['encryption'];
                    $mail->Port       = $smtpSettings['port'];

                    $mail->setFrom($smtpSettings['from_email'], $smtpSettings['from_name']);
                    $mail->addAddress($order['user_email'], $order['user_name']);

                    $mail->isHTML(true);
                    $mail->Subject = 'Your Order #' . $order['order_id'] . ' has been Dispatched! 🚚';

                    // Build Item List HTML
                    $itemsHtml = '';
                    foreach ($items as $item) {
                        $pName = $item['product_name'] ?? $item['name'] ?? 'Item';
                        $itemsHtml .= "<li>{$pName} (x{$item['quantity']})</li>";
                    }

                    $emailBody = "
                    <!DOCTYPE html>
                    <html>
                    <head>
                        <style>
                            body { font-family: 'Arial', sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; margin: 0; padding: 0; }
                            .email-container { max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                            .header { background: #1a202c; color: #ffffff; padding: 20px; text-align: center; }
                            .header h1 { margin: 0; font-size: 24px; }
                            .content { padding: 30px; }
                            .order-details { background: #f7fafc; padding: 15px; border-radius: 6px; margin: 20px 0; border: 1px solid #e2e8f0; }
                            .tracking-box { background: #ebf8ff; border: 1px dashed #4299e1; padding: 15px; border-radius: 6px; text-align: center; margin: 20px 0; }
                            .tracking-number { font-size: 18px; font-weight: bold; color: #2b6cb0; display: block; margin-top: 5px; }
                            .footer { background: #edf2f7; padding: 15px; text-align: center; font-size: 12px; color: #718096; }
                            ul { padding-left: 20px; }
                        </style>
                    </head>
                    <body>
                        <div class='email-container'>
                            <div class='header'>
                                <h1>Order Dispatched!</h1>
                            </div>
                            <div class='content'>
                                <p>Hi <strong>{$order['user_name']}</strong>,</p>
                                <p>Great news! Your order has been packed and dispatched via <strong>{$courier_name}</strong>.</p>
                                
                                <div class='tracking-box'>
                                    <span>Tracking Number:</span>
                                    <span class='tracking-number'>{$tracking_id}</span>
                                </div>

                                <div class='order-details'>
                                    <strong>Order ID:</strong> #{$order['id']} ({$order['order_id']})<br>
                                    <strong>Dispatched From:</strong> {$dispatched_from}<br>
                                    <strong>Date:</strong> " . date('d M Y') . "
                                </div>

                                <p><strong>Items in this package:</strong></p>
                                <ul>{$itemsHtml}</ul>

                                <p>You verify your package using the tracking number provided above on the courier's website.</p>
                                <p>Thank you for shopping with us!</p>
                            </div>
                            <div class='footer'>
                                &copy; " . date('Y') . " WaryChary India. All rights reserved.
                            </div>
                        </div>
                    </body>
                    </html>";

                    $mail->Body = $emailBody;
                    $mail->AltBody = "Your order #{$order['order_id']} has been dispatched via {$courier_name}. Tracking ID: {$tracking_id}.";

                    $mail->send();
                    $_SESSION['success_message'] .= " Email sent.";
                } catch (Exception $e) {
                    error_log("Mailer Error: {$mail->ErrorInfo}");
                    $_SESSION['warning_message'] = "Order dispatched, but email failed to send.";
                }
            } else {
                 if(empty($smtpSettings)) error_log("SMTP Settings not found.");
                 if(empty($order['user_email'])) error_log("User email not found for Order ID: $order_id");
            }

        } else {
            $_SESSION['error_message'] = "Failed to dispatch order.";
        }
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }

    header("Location: pending-orders.php");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
