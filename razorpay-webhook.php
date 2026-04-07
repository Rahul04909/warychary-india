<?php
header('Content-Type: application/json');

include_once __DIR__ . '/database/db_config.php';
require_once __DIR__ . '/includes/order_helper.php';
require_once __DIR__ . '/vendor/autoload.php';

use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

// 1. Webhook Secret (Matches what you set in Razorpay Dashboard)
$webhook_secret = 'warychary_webhook_secret_2024'; 

$logFile = __DIR__ . '/payment_debug.log';
function logWebhook($msg) {
    global $logFile;
    file_put_contents($logFile, date('[Y-m-d H:i:s] ') . "[Webhook] " . $msg . PHP_EOL, FILE_APPEND);
}

try {
    $database = new Database();
    $db = $database->getConnection();

    // 2. Capture Raw Body and Signature Header
    $post_data = file_get_contents('php://input');
    $sig_header = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

    if (empty($post_data) || empty($sig_header)) {
        logWebhook("Warning: Empty data or missing signature header.");
        http_response_code(400);
        exit;
    }

    // 3. Verify Webhook Signature
    $rzp_stmt = $db->prepare("SELECT key_id, key_secret FROM razorpay_settings ORDER BY id DESC LIMIT 1");
    $rzp_stmt->execute();
    $creds = $rzp_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$creds) {
        logWebhook("Error: Payment gateway settings missing.");
        http_response_code(500);
        exit;
    }

    $api = new Api($creds['key_id'], $creds['key_secret']);

    try {
        $api->utility->verifyWebhookSignature($post_data, $sig_header, $webhook_secret);
        logWebhook("Signature verification successful.");
    } catch (SignatureVerificationError $e) {
        logWebhook("Error: Signature verification failed - " . $e->getMessage());
        http_response_code(200); // Always 200 for Razorpay unless retry is wanted, but sig fail doesn't need retry.
        exit;
    }

    // 4. Process Webhook Event
    $data = json_decode($post_data, true);
    $event = $data['event'] ?? '';

    logWebhook("Received Event: $event");

    if ($event === 'order.paid') {
        $razorpay_order_id = $data['payload']['order']['entity']['id'];
        
        // Fetch payment details (usually the first successful payment linked to this order)
        // Note: order.paid usually has order payload, but may not have payment ID directly there?
        // Actually, in order.paid, the payments are listed.
        $payments = $data['payload']['payment']['entity'] ?? [];
        $razorpay_payment_id = $payments['id'] ?? 'WEBHOOK_PAYMENT_N/A';

        logWebhook("Processing Order Paid: $razorpay_order_id | Payment: $razorpay_payment_id");

        // CALL SHARED ORDER SERVICE
        $result = completeOrder($db, $razorpay_order_id, $razorpay_payment_id);
        
        if ($result['success']) {
            logWebhook("Order successfully processed via Webhook.");
        } else {
            logWebhook("Order processing failed: " . ($result['message'] ?? 'Unknown Error'));
        }
    }

    // Always respond with 200 to acknowledgereceipt
    http_response_code(200);
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    logWebhook("Critical Error: " . $e->getMessage());
    http_response_code(200); // Avoid retries if it's a code error?
}
?>
