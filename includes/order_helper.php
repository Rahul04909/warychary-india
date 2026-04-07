<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../database/db_config.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../generate-invoice.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Centrally manages the finalization of an order.
 * Ensures idempotency (processed only once) and handles MLM commissions.
 */
function completeOrder($db, $razorpay_order_id, $razorpay_payment_id) {
    $logFile = __DIR__ . '/../payment_debug.log';
    $log = function($msg) use ($logFile) {
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "[CompleteOrder] " . $msg . PHP_EOL, FILE_APPEND);
    };

    $log("Starting order completion for RZP Order: $razorpay_order_id | Payment: $razorpay_payment_id");

    try {
        // 1. Resolve Order
        $stmt = $db->prepare("SELECT * FROM orders WHERE order_id = :roid LIMIT 1");
        $stmt->execute([':roid' => $razorpay_order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$order) {
            $log("Error: Order not found for Razorpay Order ID: $razorpay_order_id");
            return ['success' => false, 'message' => 'Order not found.'];
        }

        $internal_order_id = $order['id'];
        $user_id = $order['user_id'];
        $amount = $order['total_amount'];

        // 2. IDEMPOTENCY CHECK - Is order already marked as paid?
        if ($order['payment_status'] === 'paid') {
            $log("Order $internal_order_id is already marked as paid. Skipping logic.");
            return ['success' => true, 'already_paid' => true];
        }

        // 3. Update Order Status
        $db->beginTransaction();

        $update_sql = "UPDATE orders SET payment_status = 'paid', payment_id = :pid, updated_at = NOW() WHERE id = :oid";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->execute([':pid' => $razorpay_payment_id, ':oid' => $internal_order_id]);
        
        $log("Order $internal_order_id status updated to 'paid'.");

        // 4. MLM Commission Logic
        $partner_id = null;
        
        // Fetch partner_id from users table (lifetime binding)
        if ($user_id) {
            $u_stmt = $db->prepare("SELECT partner_id FROM users WHERE id = :uid");
            $u_stmt->execute([':uid' => $user_id]);
            $user_row = $u_stmt->fetch(PDO::FETCH_ASSOC);
            $partner_id = $user_row['partner_id'];
        }

        // Fallback to Order table partner if user not bound
        if (!$partner_id && !empty($order['partner_id'])) {
            $partner_id = $order['partner_id'];
            if ($user_id) {
                // Heal the data: Bind the user to the partner used for this order
                $bind = $db->prepare("UPDATE users SET partner_id = :pid WHERE id = :uid");
                $bind->execute([':pid' => $partner_id, ':uid' => $user_id]);
                $log("User $user_id bound to partner $partner_id (Auto-heal).");
            }
        }

        if ($partner_id) {
            // A. Partner Commission (15%)
            $comm_partner = $amount * 0.15;
            
            // Double check earnings table for idempotency
            $dup_check = $db->prepare("SELECT id FROM partner_earnings WHERE order_id = :oid AND partner_id = :pid");
            $dup_check->execute([':oid' => $internal_order_id, ':pid' => $partner_id]);
            
            if (!$dup_check->fetch()) {
                $ins_p = $db->prepare("INSERT INTO partner_earnings (partner_id, partner_type, order_id, amount, percentage, description, created_at) VALUES (:pid, 'marketing', :oid, :amnt, 15.00, :desc, NOW())");
                $ins_p->execute([
                    ':pid' => $partner_id,
                    ':oid' => $internal_order_id,
                    ':amnt' => $comm_partner,
                    ':desc' => "Commission for Order #$internal_order_id"
                ]);

                // Update partner current wallet
                $db->prepare("UPDATE partners SET earning = earning + :amnt, total_earnings = total_earnings + :amnt WHERE id = :pid")
                   ->execute([':amnt' => $comm_partner, ':pid' => $partner_id]);
                
                $log("15% Commission (₹$comm_partner) awarded to Partner $partner_id.");
            }

            // B. Senior Partner Logic (2% Override)
            $p_stmt = $db->prepare("SELECT senior_partner_id FROM partners WHERE id = :pid");
            $p_stmt->execute([':pid' => $partner_id]);
            $partner_row = $p_stmt->fetch(PDO::FETCH_ASSOC);
            $senior_partner_id = $partner_row['senior_partner_id'] ?? null;

            if ($senior_partner_id) {
                $comm_senior = $amount * 0.02;
                
                $dup_check_s = $db->prepare("SELECT id FROM senior_partner_earnings WHERE order_id = :oid AND senior_partner_id = :sid");
                $dup_check_s->execute([':oid' => $internal_order_id, ':sid' => $senior_partner_id]);

                if (!$dup_check_s->fetch()) {
                    $ins_s = $db->prepare("INSERT INTO senior_partner_earnings (senior_partner_id, source_partner_id, order_id, amount, percentage, description, status, created_at) VALUES (:sid, :pid, :oid, :amnt, 2.00, :desc, 'pending', NOW())");
                    $ins_s->execute([
                        ':sid' => $senior_partner_id,
                        ':pid' => $partner_id,
                        ':oid' => $internal_order_id,
                        ':amnt' => $comm_senior,
                        ':desc' => "Override Commission from Partner #$partner_id on Order #$internal_order_id"
                    ]);

                    $db->prepare("UPDATE senior_partners SET earning = earning + :amnt, total_earnings = total_earnings + :amnt WHERE id = :sid")
                       ->execute([':amnt' => $comm_senior, ':sid' => $senior_partner_id]);
                    
                    $log("2% Override Commission (₹$comm_senior) awarded to Senior Partner $senior_partner_id.");
                }
            }
        }

        // 5. Transaction Log entry
        $log_stmt = $db->prepare("INSERT INTO razorpay_transactions (order_id, payment_id, amount, status, created_at) VALUES (:oid, :pid, :amnt, 'captured', NOW())");
        $log_stmt->execute([
            ':oid' => $razorpay_order_id, 
            ':pid' => $razorpay_payment_id, 
            ':amnt' => $amount
        ]);

        $db->commit();
        $log("Database transaction committed successfully for Order $internal_order_id.");

        // 6. Send Confirmation Email with Invoice
        try {
            $smtp_stmt = $db->prepare("SELECT * FROM smtp_settings LIMIT 1");
            $smtp_stmt->execute();
            $smtp = $smtp_stmt->fetch(PDO::FETCH_ASSOC);

            if ($smtp) {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = $smtp['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $smtp['username'];
                $mail->Password   = $smtp['password'];
                $mail->SMTPSecure = $smtp['encryption'];
                $mail->Port       = $smtp['port'];

                $mail->setFrom($smtp['from_email'], $smtp['from_name']);
                $mail->addAddress($order['customer_email'], $order['customer_name']);
                
                // Generate Invoice PDF
                $pdf_content = generateInvoicePDF($internal_order_id, 'S');
                if ($pdf_content) {
                    $mail->addStringAttachment($pdf_content, "Invoice_$internal_order_id.pdf");
                }

                $mail->isHTML(true);
                $mail->Subject = 'Order Confirmed - WaryChary';
                $mail->Body    = "<h3>Order Confirmation</h3>
                                 Dear " . htmlspecialchars($order['customer_name']) . ",<br><br>
                                 Thank you for your order! Your payment was successful and your order is being processed.<br>
                                 <b>Order ID:</b> #$internal_order_id<br>
                                 <b>Amount:</b> ₹" . number_format($amount, 2) . "<br><br>
                                 Please find your invoice attached.<br><br>
                                 Regards,<br>WaryChary Team";
                $mail->send();
                $log("Confirmation email sent to " . $order['customer_email']);
            }
        } catch (Exception $emailEx) {
            $log("Email Error: " . $emailEx->getMessage());
        }

        return ['success' => true, 'order_id' => $internal_order_id];

    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $log("CRITICAL ERROR: " . $e->getMessage());
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
