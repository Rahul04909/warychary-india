<?php
header('Content-Type: application/json');
include_once '../database/db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['code'])) {
    $code = trim($_GET['code']);
    
    // Allow lookup by referral_code OR email
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT id, name, email FROM senior_partners WHERE referral_code = :code OR email = :email LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':code', $code);
    $stmt->bindParam(':email', $code);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['valid' => true, 'name' => $row['name'], 'id' => $row['id']]);
    } else {
        echo json_encode(['valid' => false]);
    }
} else {
    echo json_encode(['valid' => false, 'error' => 'Invalid request']);
}
?>
