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
    }
}
?>
