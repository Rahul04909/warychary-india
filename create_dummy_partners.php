<?php
require 'database/db_config.php';
$db = (new Database())->getConnection();

// 1. Create Senior Partner
$senior_email = "senior@test.com";
$stmt = $db->prepare("SELECT id FROM senior_partners WHERE email = ?");
$stmt->execute([$senior_email]);
$senior = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$senior) {
    $sql = "INSERT INTO senior_partners (name, email, password, status, created_at) VALUES ('Test Senior', ?, ?, 'active', NOW())";
    $stmt = $db->prepare($sql);
    $stmt->execute([$senior_email, password_hash('password', PASSWORD_DEFAULT)]);
    $senior_id = $db->lastInsertId();
    echo "Created Senior Partner ID: $senior_id\n";
} else {
    $senior_id = $senior['id'];
    echo "Senior Partner exists: ID $senior_id\n";
}

// 2. Create Junior Partner linked to Senior
$partner_email = "partner@test.com";
$stmt = $db->prepare("SELECT id FROM partners WHERE email = ?");
$stmt->execute([$partner_email]);
$partner = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$partner) {
    $referral_code = "TESTREF";
    $sql = "INSERT INTO partners (senior_partner_id, name, email, mobile, password, referral_code, status, created_at) VALUES (?, 'Test Partner', ?, '9999999991', ?, ?, 'active', NOW())";
    $stmt = $db->prepare($sql);
    $stmt->execute([$senior_id, $partner_email, password_hash('password', PASSWORD_DEFAULT), $referral_code]);
    $partner_id = $db->lastInsertId();
    echo "Created Partner ID: $partner_id with Referral Code: $referral_code\n";
} else {
    echo "Partner exists: ID " . $partner['id'] . "\n";
}
?>
