<?php
header('Content-Type: application/json');

// 1. Output Buffering (Crucial for clean JSON)
ob_start();

include_once __DIR__ . '/database/db_config.php';
require __DIR__ . '/vendor/autoload.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

// Debug Log File
$logFile = __DIR__ . '/payment_debug.log';

function logDebug($message) {
    global $logFile;
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . $message . PHP_EOL, FILE_APPEND);
}

// Global Exception Handler for JSON Safety
set_exception_handler(function ($e) {
    if (ob_get_length()) ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Server Error: ' . $e->getMessage()]);
    exit;
});

try {
    $database = new Database();
    $db = $database->getConnection();

    // 2. Input Capture & Strict Validation
    $inputJSON = file_get_contents('php://input');
    $input = json_decode($inputJSON, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON Input");
    }

    logDebug("Input Received: " . $inputJSON);

    // Mandatory Parameters Check
    $requiredParams = ['razorpay_order_id', 'razorpay_payment_id', 'razorpay_signature'];
    foreach ($requiredParams as $param) {
        if (empty($input[$param])) {
            throw new Exception("Missing required parameter: $param");
        }
    }

    $razorpay_order_id = $input['razorpay_order_id'];
    $razorpay_payment_id = $input['razorpay_payment_id'];
    $razorpay_signature = $input['razorpay_signature'];
    $internal_order_id = $input['internal_order_id'] ?? null;

    // 3. Fetch Razorpay Credentials
    $rzp_stmt = $db->prepare("SELECT key_id, key_secret FROM razorpay_settings ORDER BY id DESC LIMIT 1");
    $rzp_stmt->execute();
    $creds = $rzp_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$creds) {
        throw new Exception("Payment gateway configuration not found.");
    }

    $api = new Api($creds['key_id'], $creds['key_secret']);

    // 4. Strict Signature Verification
    $attributes = [
        'razorpay_order_id' => $razorpay_order_id,
        'razorpay_payment_id' => $razorpay_payment_id,
        'razorpay_signature' => $razorpay_signature
    ];

    try {
        $api->utility->verifyPaymentSignature($attributes);
        logDebug("Signature Verification Successful");
    } catch (SignatureVerificationError $e) {
        throw new Exception("Signature Verification Failed: " . $e->getMessage());
    }

    // 5. Reliable Order Resolution
    $order = null;
    
    // Strategy A: By Internal ID
    if ($internal_order_id) {
        $stmt = $db->prepare("SELECT id, user_id, total_amount, payment_status, partner_id as order_partner_id, senior_partner_id as order_senior_id FROM orders WHERE id = :id");
        $stmt->execute([':id' => $internal_order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Strategy B: By Razorpay Order ID
    if (!$order) {
        $stmt = $db->prepare("SELECT id, user_id, total_amount, payment_status, partner_id as order_partner_id, senior_partner_id as order_senior_id FROM orders WHERE order_id = :roid");
        $stmt->execute([':roid' => $razorpay_order_id]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    if (!$order) {
        throw new Exception("Order not found for Razorpay Order ID: $razorpay_order_id");
    }

    $internal_order_id = $order['id'];
    $user_id = $order['user_id'];
    $amount = $order['total_amount'];

    logDebug("Order Resolved: ID $internal_order_id, User $user_id");

    // 6. Update Order Status
    if ($order['payment_status'] !== 'paid') {
        $update_sql = "UPDATE orders SET payment_status = 'paid', payment_id = :pid, updated_at = NOW() WHERE id = :oid";
        $update_stmt = $db->prepare($update_sql);
        $update_stmt->execute([':pid' => $razorpay_payment_id, ':oid' => $internal_order_id]);
        logDebug("Order Status Updated to Paid");
    }
    
    // 7. Dynamic MLM Commission Logic
    // Priority: Users Table > Orders Table
    
    $partner_id = null;

    if ($user_id) {
        // A. Check Users Table
        $u_stmt = $db->prepare("SELECT partner_id FROM users WHERE id = :uid");
        $u_stmt->execute([':uid' => $user_id]);
        $user_row = $u_stmt->fetch(PDO::FETCH_ASSOC);
        $partner_id = $user_row['partner_id'];
    }

    // Fallback: Check Orders Table (if user not bound yet or guest checkout glitch)
    if (!$partner_id && !empty($order['order_partner_id'])) {
        $partner_id = $order['order_partner_id'];
        logDebug("User not bound using Order Partner ID: $partner_id");
        
        // AUTO-BIND USER IF MISSING (Heal the data)
        if ($user_id) {
            $bind_stmt = $db->prepare("UPDATE users SET partner_id = :pid WHERE id = :uid");
            $bind_stmt->execute([':pid' => $partner_id, ':uid' => $user_id]);
            logDebug("Self-Healed: Bound user $user_id to partner $partner_id");
        }
    }

    if ($partner_id) {
        logDebug("Partner Identified: $partner_id");

        // B. Partner Commission (15%)
        $comm_partner = $amount * 0.15;
        
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

            $upd_p = $db->prepare("UPDATE partners SET earning = earning + :amnt, total_earnings = total_earnings + :amnt WHERE id = :pid");
            $upd_p->execute([':amnt' => $comm_partner, ':pid' => $partner_id]);
            logDebug("Partner Commission Logic Executed");
        }

        // C. Senior Partner Logic
        $p_stmt = $db->prepare("SELECT senior_partner_id FROM partners WHERE id = :pid");
        $p_stmt->execute([':pid' => $partner_id]);
        $partner_row = $p_stmt->fetch(PDO::FETCH_ASSOC);
        $senior_partner_id = $partner_row['senior_partner_id'] ?? null;

        if ($senior_partner_id) {
            logDebug("Senior Partner Identified: $senior_partner_id");
            
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
                    ':desc' => "Override Commission for Order #$internal_order_id"
                ]);

                $upd_s = $db->prepare("UPDATE senior_partners SET earning = earning + :amnt, total_earnings = total_earnings + :amnt WHERE id = :sid");
                $upd_s->execute([':amnt' => $comm_senior, ':sid' => $senior_partner_id]);
                logDebug("Senior Partner Commission Logic Executed");
            }
        } else {
            logDebug("No Senior Partner linked to Partner $partner_id");
        }
    } else {
        logDebug("No Partner found for User $user_id or Order $internal_order_id");
    }

    // 8. Transaction Log
    $log_stmt = $db->prepare("INSERT INTO razorpay_transactions (order_id, payment_id, amount, status, created_at) VALUES (:oid, :pid, :amnt, 'captured', NOW())");
    $log_stmt->execute([':oid' => $razorpay_order_id, ':pid' => $razorpay_payment_id, ':amnt' => $amount]);

    // 9. Send Email (Buffered)
    // Capture output to avoid JSON corruption
    ob_start();
    try {
        require_once __DIR__ . '/generate-invoice.php';
        
        $smtp_stmt = $db->prepare("SELECT * FROM smtp_settings LIMIT 1");
        $smtp_stmt->execute();
        $smtp = $smtp_stmt->fetch(PDO::FETCH_ASSOC);

        if ($smtp) {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $smtp['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp['username'];
            $mail->Password   = $smtp['password'];
            $mail->SMTPSecure = $smtp['encryption'];
            $mail->Port       = $smtp['port'];

            $mail->setFrom($smtp['from_email'], $smtp['from_name']);
            $customer_stmt = $db->prepare("SELECT customer_email, customer_name FROM orders WHERE id = :id");
            $customer_stmt->execute([':id' => $internal_order_id]);
            $cust = $customer_stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($cust) {
                $mail->addAddress($cust['customer_email'], $cust['customer_name']);
                
                $pdf_content = generateInvoicePDF($internal_order_id, 'S');
                if ($pdf_content) {
                    $mail->addStringAttachment($pdf_content, "Invoice_$internal_order_id.pdf");
                }

                $mail->isHTML(true);
                $mail->Subject = 'Order Confirmation - WaryChary';
                $mail->Body    = "Dear " . htmlspecialchars($cust['customer_name']) . ",<br><br>Thank you for your order! Your payment was successful.<br>Please find your invoice attached.<br><br>Regards,<br>WaryChary Team";
                $mail->send();
                logDebug("Email sent.");
            }
        }
    } catch (Exception $e) {
        logDebug("Email Error: " . $e->getMessage());
    }
    ob_end_clean(); // Discard email output

    // Clean buffer before sending successful JSON
    if (ob_get_length()) ob_clean();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    logDebug("Critical Error: " . $e->getMessage());
    if (ob_get_length()) ob_clean();
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
