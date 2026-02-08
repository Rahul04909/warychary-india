<?php
header('Content-Type: application/json');
include_once __DIR__ . '/database/db_config.php';
require __DIR__ . '/vendor/autoload.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$database = new Database();
$db = $database->getConnection();

// Debug Logging
$logFile = __DIR__ . '/payment_debug.log';
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Input: " . $inputJSON . PHP_EOL, FILE_APPEND);

if (!isset($input['razorpay_payment_id']) || !isset($input['razorpay_order_id'])) {
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Error: Invalid Data" . PHP_EOL, FILE_APPEND);
    echo json_encode(['success' => false, 'message' => 'Invalid Data']);
    exit;
}

try {
    // 1. Fetch Credentials
    $rzp_stmt = $db->prepare("SELECT key_id, key_secret, mode FROM razorpay_settings ORDER BY id DESC LIMIT 1");
    $rzp_stmt->execute();
    $creds = $rzp_stmt->fetch(PDO::FETCH_ASSOC);
    
    $api = new Api($creds['key_id'], $creds['key_secret']);
    
    // 2. Verify Signature
    $attributes = [
        'razorpay_order_id' => $input['razorpay_order_id'],
        'razorpay_payment_id' => $input['razorpay_payment_id'],
        'razorpay_signature' => $input['razorpay_signature']
    ];
    
    $api->utility->verifyPaymentSignature($attributes);
    
    // 3. Update Order Status
    $order_id = $input['internal_order_id'];
    $payment_id = $input['razorpay_payment_id'];
    
    $update_sql = "UPDATE orders SET payment_status = 'paid', payment_id = :pid, updated_at = NOW() WHERE id = :oid";
    $update_stmt = $db->prepare($update_sql);
    $update_stmt->bindParam(':pid', $payment_id);
    $update_stmt->bindParam(':oid', $order_id);
    $update_stmt->execute();
    
    // 4. Partner Commission Logic
    // Fetch Order to get total amount and partner_id
    $query = "SELECT total_amount, partner_id, senior_partner_id FROM orders WHERE id = :oid";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':oid', $order_id);
    $stmt->execute();
    $order_res = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $amount = ($order_res) ? $order_res['total_amount'] : 0;
    $partner_id = ($order_res) ? $order_res['partner_id'] : null;

    if ($partner_id) {
        // 15% Commission for Marketing Partner
        $commission_partner = $amount * 0.15;
        
        $comm_sql = "INSERT INTO partner_earnings (partner_id, partner_type, order_id, amount, percentage, description, created_at) VALUES (:pid, 'marketing', :oid, :amnt, 15.00, :desc, NOW())";
        $comm_stmt = $db->prepare($comm_sql);
        $desc = "Commission for Order #$order_id";
        $comm_stmt->bindParam(':pid', $partner_id);
        $comm_stmt->bindParam(':oid', $order_id);
        $comm_stmt->bindParam(':amnt', $commission_partner);
        $comm_stmt->bindParam(':desc', $desc);
        $comm_stmt->execute();

        // UPDATE PARTNER BALANCE
        $update_partner_sql = "UPDATE partners SET earning = earning + :amnt, total_earnings = total_earnings + :amnt WHERE id = :pid";
        $update_partner_stmt = $db->prepare($update_partner_sql);
        $update_partner_stmt->bindParam(':amnt', $commission_partner);
        $update_partner_stmt->bindParam(':pid', $partner_id);
        $update_partner_stmt->execute();
        
        // 2% Commission for Senior Partner (if applicable)
        // Senior partner ID is already linked in the order during creation in process-checkout (we need to ensure process-checkout saves it)
        // Let's rely on orders table having senior_partner_id
        $senior_partner_id = $order_res['senior_partner_id'];

        if ($senior_partner_id) {
            $commission_senior = $amount * 0.02;
            $comm_senior_sql = "INSERT INTO senior_partner_earnings (senior_partner_id, source_partner_id, order_id, amount, percentage, description, status, created_at) VALUES (:sid, :pid, :oid, :amnt, 2.00, :desc, 'pending', NOW())";
            $comm_senior_stmt = $db->prepare($comm_senior_sql);
            $desc_senior = "Override Commission for Order #$order_id";
            $comm_senior_stmt->bindParam(':sid', $senior_partner_id);
            $comm_senior_stmt->bindParam(':pid', $partner_id);
            $comm_senior_stmt->bindParam(':oid', $order_id);
            $comm_senior_stmt->bindParam(':amnt', $commission_senior);
            $comm_senior_stmt->bindParam(':desc', $desc_senior);
            $comm_senior_stmt->execute();

            // UPDATE SENIOR PARTNER BALANCE
            $update_senior_sql = "UPDATE senior_partners SET earning = earning + :amnt, total_earnings = total_earnings + :amnt WHERE id = :sid";
            $update_senior_stmt = $db->prepare($update_senior_sql);
            $update_senior_stmt->bindParam(':amnt', $commission_senior);
            $update_senior_stmt->bindParam(':sid', $senior_partner_id);
            $update_senior_stmt->execute();
        }
    }
    
    // 5. Save Transaction Log
    $log_sql = "INSERT INTO razorpay_transactions (order_id, payment_id, amount, status, created_at) VALUES (:oid, :pid, :amnt, 'captured', NOW())";
    $log_stmt = $db->prepare($log_sql);
    $log_stmt->execute([':oid' => $input['razorpay_order_id'], ':pid' => $payment_id, ':amnt' => $amount]);

    // 6. Send Invoice Email
    // Use output buffering to prevent any mailer output from breaking JSON
    ob_start();
    try {
        require_once __DIR__ . '/generate-invoice.php';
        
        // Fetch SMTP Settings
        $smtp_stmt = $db->prepare("SELECT * FROM smtp_settings LIMIT 1");
        $smtp_stmt->execute();
        $smtp = $smtp_stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($smtp) {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $smtp['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $smtp['username'];
            $mail->Password   = $smtp['password'];
            $mail->SMTPSecure = $smtp['encryption'];
            $mail->Port       = $smtp['port'];

            // Recipients
            $mail->setFrom($smtp['from_email'], $smtp['from_name']);
            $order_query = $db->query("SELECT customer_email, customer_name FROM orders WHERE id = $order_id")->fetch(PDO::FETCH_ASSOC);
            $mail->addAddress($order_query['customer_email'], $order_query['customer_name']);

            // Attachments
            $pdf_content = generateInvoicePDF($order_id, 'S'); // S = String return
            if ($pdf_content) {
                $mail->addStringAttachment($pdf_content, "Invoice_$order_id.pdf");
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Order Confirmation - WaryChary';
            $mail->Body    = "Dear " . htmlspecialchars($order_query['customer_name']) . ",<br><br>Thank you for your order! Your payment was successful.<br>Please find your invoice attached.<br><br>Regards,<br>WaryChary Team";

            $mail->send();
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Email sent to " . $order_query['customer_email'] . PHP_EOL, FILE_APPEND);
        } else {
            file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "SMTP Settings not found." . PHP_EOL, FILE_APPEND);
        }
    } catch (Exception $e) {
        file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Email Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    }
    // Clean buffer so no email logs/errors break JSON
    ob_end_clean();

    echo json_encode(['success' => true]);


} catch (SignatureVerificationError $e) {
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Signature Error: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    if(ob_get_length()) ob_clean();
    echo json_encode(['success' => false, 'message' => 'Signature Verification Failed']);
} catch (Exception $e) {
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "Exception: " . $e->getMessage() . PHP_EOL, FILE_APPEND);
    if(ob_get_length()) ob_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
