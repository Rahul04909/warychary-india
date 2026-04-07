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

    // 5. CALL CENTRAL ORDER SERVICE (Heals data, Awards Commissions, Sends Emails)
    require_once __DIR__ . '/includes/order_helper.php';
    $result = completeOrder($db, $razorpay_order_id, $razorpay_payment_id);

    if (!$result['success']) {
        throw new Exception("Order Completion Failed: " . ($result['message'] ?? 'Unknown Error'));
    }

    logDebug("Order " . ($result['order_id'] ?? 'N/A') . " processed successfully via redirect.");

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

