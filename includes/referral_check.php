<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check for 'ref' parameter in URL
if (isset($_GET['ref']) && !empty($_GET['ref'])) {
    $referral_code = trim($_GET['ref']);
    
    // Validate code format (optional, alphanumeric only)
    if (preg_match('/^[a-zA-Z0-9]+$/', $referral_code)) {
        // Set Session
        $_SESSION['referral_code'] = $referral_code;
        
        // Set Cookie (expires in 30 days)
        setcookie('referral_code', $referral_code, time() + (86400 * 30), "/");

        // LIFETIME REFERRAL LOGIC:
        // Immediately fetch Partner ID and store in 'bound_partner_id'
        // This will be used when the user Registers or Checks out for the first time
        if (!isset($_SESSION['bound_partner_id'])) {
            // Need DB connection. Check if $db exists, else create it.
            if (!isset($db)) {
                include_once __DIR__ . '/../database/db_config.php';
                $database = new Database();
                $db = $database->getConnection();
            }

            $stmt = $db->prepare("SELECT id FROM partners WHERE referral_code = :code AND status = 'active'");
            $stmt->execute([':code' => $referral_code]);
            $partner = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($partner) {
                $_SESSION['bound_partner_id'] = $partner['id'];
            }
        }
    }
}
?>
