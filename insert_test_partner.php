<?php
include_once __DIR__ . '/database/db_config.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    try {
        $check = $db->query("SELECT count(*) FROM senior_partners")->fetchColumn();
        if ($check == 0) {
            $sql = "INSERT INTO senior_partners (name, email, referral_code, password, commission, status) 
                    VALUES ('Test Partner', 'partner@example.com', 'TEST01', :password, 2.00, 'active')";
            $stmt = $db->prepare($sql);
            $password = password_hash('123456', PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $password);
            
            if ($stmt->execute()) {
                echo "Test Senior Partner inserted successfully.\n";
                echo "Code: TEST01\n";
                echo "Email: partner@example.com\n";
            } else {
                echo "Failed to insert test partner.\n";
            }
        } else {
            echo "Partners already exist. First one:\n";
            $stmt = $db->query("SELECT name, referral_code, email FROM senior_partners LIMIT 1");
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "Code: " . $row['referral_code'] . "\n";
            echo "Email: " . $row['email'] . "\n";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
