<?php
header('Content-Type: application/json');
include_once __DIR__ . '/database/db_config.php';
require __DIR__ . '/vendor/autoload.php';

use Razorpay\Api\Api;

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid Request']);
    exit;
}

// 1. Validate Input
$product_id = $_POST['product_id'] ?? 0;
$qty = $_POST['qty'] ?? 1;
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$mobile = $_POST['mobile'] ?? '';
$address = $_POST['address'] ?? '';
$city = $_POST['city'] ?? '';
$state = $_POST['state'] ?? '';
$pincode = $_POST['pincode'] ?? '';

if (!$product_id || !$name || !$email || !$mobile) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // 2. Fetch Product & Calculate Total
    $stmt = $db->prepare("SELECT * FROM products WHERE id = :id AND status = 'active'");
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        throw new Exception("Product not found or inactive.");
    }

    $amount = $product['sales_price'] * $qty;
    
    // 3. Create/Update User (Guest Logic)
    // Check if user exists by email
    $user_stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
    $user_stmt->bindParam(':email', $email);
    $user_stmt->execute();
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);
    
    $user_id = null;
    if ($user) {
        $user_id = $user['id'];
        // Update details if needed? For now, we keep existing user ID.
    } else {
        // Create new guest user
        $new_user_sql = "INSERT INTO users (name, email, mobile, role, address, city, state, pincode, created_at) VALUES (:name, :email, :mobile, 'guest', :address, :city, :state, :pincode, NOW())";
        $new_stmt = $db->prepare($new_user_sql);
        $new_stmt->bindParam(':name', $name);
        $new_stmt->bindParam(':email', $email);
        $new_stmt->bindParam(':mobile', $mobile);
        $new_stmt->bindParam(':address', $address);
        $new_stmt->bindParam(':city', $city);
        $new_stmt->bindParam(':state', $state);
        $new_stmt->bindParam(':pincode', $pincode);
        $new_stmt->execute();
        $user_id = $db->lastInsertId();
    }

    // 4. Partner Logic (Fetch from Session/Cookie/URL)
    // Check for referral code in Cookie or Session
    $referral_code = $_COOKIE['referral_code'] ?? ($_SESSION['referral_code'] ?? null);
    $partner_id = null;
    $senior_partner_id = null;

    if ($referral_code) {
        $partner_stmt = $db->prepare("SELECT id, senior_partner_id FROM partners WHERE referral_code = :code AND status = 'active'");
        $partner_stmt->bindParam(':code', $referral_code);
        $partner_stmt->execute();
        $partner = $partner_stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($partner) {
            $partner_id = $partner['id'];
            $senior_partner_id = $partner['senior_partner_id'];
        }
    }
    
    // 5. Create Local Order (Pending)
    // Generate a temporary unique order ID to satisfy NOT NULL UNIQUE constraint
    // This will be updated with Razorpay Order ID later
    $temp_order_id = 'ORD_' . time() . '_' . rand(1000, 9999);

    $order_sql = "INSERT INTO orders (order_id, user_id, customer_name, customer_email, customer_mobile, shipping_address, shipping_city, shipping_state, shipping_pincode, total_amount, payment_status, partner_id, senior_partner_id, created_at) VALUES (:oid, :uid, :name, :email, :mobile, :addr, :city, :state, :pin, :total, 'pending', :pid, :sid, NOW())";
    $order_stmt = $db->prepare($order_sql);
    $order_stmt->bindParam(':oid', $temp_order_id);
    $order_stmt->bindParam(':uid', $user_id);
    $order_stmt->bindParam(':name', $name);
    $order_stmt->bindParam(':email', $email);
    $order_stmt->bindParam(':mobile', $mobile);
    $order_stmt->bindParam(':addr', $address);
    $order_stmt->bindParam(':city', $city);
    $order_stmt->bindParam(':state', $state);
    $order_stmt->bindParam(':pin', $pincode);
    $order_stmt->bindParam(':total', $amount);
    $order_stmt->bindParam(':pid', $partner_id);
    $order_stmt->bindParam(':sid', $senior_partner_id);
    $order_stmt->execute();
    $internal_order_id = $db->lastInsertId();
    
    // 6. Create Razorpay Order
    // Fetch Credentials (Active Mode)
    $rzp_stmt = $db->prepare("SELECT key_id, key_secret, mode FROM razorpay_settings ORDER BY id DESC LIMIT 1");
    $rzp_stmt->execute();
    $creds = $rzp_stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$creds) {
        throw new Exception("Payment gateway not configured.");
    }

    $api = new Api($creds['key_id'], $creds['key_secret']);
    
    $rzp_order_data = [
        'receipt'         => 'order_' . $internal_order_id,
        'amount'          => $amount * 100, // Amount in paise
        'currency'        => 'INR',
        'payment_capture' => 1 // Auto capture
    ];
    
    $rzp_order = $api->order->create($rzp_order_data);
    
    // Update local order with Razorpay Order ID
    $update_sql = "UPDATE orders SET order_id = :roid WHERE id = :id";
    $update_stmt = $db->prepare($update_sql);
    $update_stmt->bindParam(':roid', $rzp_order['id']);
    $update_stmt->bindParam(':id', $internal_order_id);
    $update_stmt->execute();

    // 7. Insert Order Items
    $item_sql = "INSERT INTO order_items (order_id, product_id, product_name, quantity, price, total_price, free_gift_name) VALUES (:oid, :pid, :pname, :qty, :price, :total_price, :gift)";
    $item_stmt = $db->prepare($item_sql);
    $item_stmt->bindParam(':oid', $internal_order_id);
    $item_stmt->bindParam(':pid', $product['id']);
    $item_stmt->bindParam(':pname', $product['name']);
    $item_stmt->bindParam(':qty', $qty);
    $item_stmt->bindParam(':price', $product['sales_price']);
    $item_stmt->bindParam(':total_price', $amount);
    
    $gift_name = ($product['is_free_product_active']) ? $product['free_product_name'] : null;
    $item_stmt->bindParam(':gift', $gift_name);
    $item_stmt->execute();

    echo json_encode([
        'success' => true,
        'order' => [
            'id' => $rzp_order['id'],
            'amount' => $amount * 100
        ],
        'internal_order_id' => $internal_order_id
    ]);

} catch (Exception $e) {
    // Log error
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
