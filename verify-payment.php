<?php
header('Content-Type: application/json');
include_once __DIR__ . '/database/db_config.php';
require __DIR__ . '/vendor/autoload.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$database = new Database();
$db = $database->getConnection();

$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, true);

if (!isset($input['razorpay_payment_id']) || !isset($input['razorpay_order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid Data']);
    exit;
}

try {
    // 1. Fetch Credentials
    $rzp_stmt = $db->prepare("SELECT key_id, key_secret FROM razorpay_settings WHERE mode = 'test' LIMIT 1");
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
        
        // 2% Commission for Senior Partner (if applicable)
        // Senior partner ID is already linked in the order during creation in process-checkout (we need to ensure process-checkout saves it)
        // Let's rely on orders table having senior_partner_id
        $senior_partner_id = $order_res['senior_partner_id'];

        if ($senior_partner_id) {
            $commission_senior = $amount * 0.02;
            $comm_senior_sql = "INSERT INTO partner_earnings (partner_id, partner_type, order_id, amount, percentage, description, created_at) VALUES (:pid, 'senior', :oid, :amnt, 2.00, :desc, NOW())";
            $comm_senior_stmt = $db->prepare($comm_senior_sql);
            $desc_senior = "Override Commission for Order #$order_id (Ref: Partner $partner_id)";
            $comm_senior_stmt->bindParam(':pid', $senior_partner_id);
            $comm_senior_stmt->bindParam(':oid', $order_id);
            $comm_senior_stmt->bindParam(':amnt', $commission_senior);
            $comm_senior_stmt->bindParam(':desc', $desc_senior);
            $comm_senior_stmt->execute();
        }
    }
    
    // 5. Save Transaction Log
    $log_sql = "INSERT INTO razorpay_transactions (order_id, payment_id, amount, status, created_at) VALUES (:oid, :pid, :amnt, 'captured', NOW())";
    $log_stmt = $db->prepare($log_sql);
    $log_stmt->execute([':oid' => $input['razorpay_order_id'], ':pid' => $payment_id, ':amnt' => $amount]);

    echo json_encode(['success' => true]);

} catch (SignatureVerificationError $e) {
    echo json_encode(['success' => false, 'message' => 'Signature Verification Failed']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
