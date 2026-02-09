<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit;
}

include_once '../../database/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $user_type = $_POST['user_type']; // 'partner' or 'senior_partner'
    $user_ids_raw = $_POST['user_ids'];
    $payment_mode = $_POST['payment_mode'];
    $transaction_id = $_POST['transaction_id'];
    $admin_note = $_POST['admin_note'];

    $redirect_url = ($user_type === 'partner') ? 'partner-payouts.php' : 'senior-partner-payouts.php';

    if (empty($user_ids_raw)) {
        $_SESSION['error'] = "No users selected for payout.";
        header("Location: $redirect_url");
        exit;
    }

    $user_ids = explode(',', $user_ids_raw);
    $success_count = 0;
    
    // Determine earnings table
    $earnings_table = ($user_type === 'partner') ? 'partner_earnings' : 'senior_partner_earnings';
    $source_col = ($user_type === 'partner') ? 'partner_id' : 'senior_partner_id';

    try {
        $db->beginTransaction();
        
        $insertStmt = $db->prepare("INSERT INTO payout_history (user_id, user_type, amount, payment_mode, transaction_id, status, admin_note) VALUES (:uid, :utype, :amt, :mode, :txn, 'processed', :note)");

        foreach ($user_ids as $uid) {
            $uid = trim($uid);
            if(empty($uid)) continue;

            // 1. Calculate Pending Balance
            // Total Earnings
            $stmt = $db->prepare("SELECT SUM(amount) FROM $earnings_table WHERE $source_col = :id");
            $stmt->execute([':id' => $uid]);
            $total_earnings = $stmt->fetchColumn() ?: 0;

            // Total Paid
            $stmt = $db->prepare("SELECT SUM(amount) FROM payout_history WHERE user_id = :id AND user_type = :utype AND status = 'processed'");
            $stmt->execute([':id' => $uid, ':utype' => $user_type]);
            $total_paid = $stmt->fetchColumn() ?: 0;

            $pending_amount = $total_earnings - $total_paid;

            if ($pending_amount > 0) {
                // 2. Insert Payout Record
                $insertStmt->execute([
                    ':uid' => $uid,
                    ':utype' => $user_type,
                    ':amt' => $pending_amount,
                    ':mode' => $payment_mode,
                    ':txn' => $transaction_id,
                    ':note' => $admin_note
                ]);
                $success_count++;
            }
        }

        $db->commit();
        $_SESSION['success'] = "Payout processed successfully for $success_count user(s).";

    } catch (PDOException $e) {
        $db->rollBack();
        $_SESSION['error'] = "Error processing payout: " . $e->getMessage();
    }

    header("Location: $redirect_url");
    exit;
}
?>
