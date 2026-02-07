<?php
include_once __DIR__ . '/database/db_config.php';

echo "Testing DB Connection...\n";

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "Connected successfully.\n";
    try {
        $stmt = $db->query("SELECT referral_code, email FROM senior_partners LIMIT 1");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($row) {
            echo "Found a Senior Partner:\n";
            print_r($row);
            
            // Now test API with this code
            $code = $row['referral_code'];
            echo "\nTesting API with code: $code\n";
            
            // Set up environment for API script
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_GET['code'] = $code;
            
            // Capture output
            ob_start();
            include __DIR__ . '/api/validate-referral.php';
            $output = ob_get_clean();
            
            echo "API Output:\n$output\n";
        } else {
            echo "No senior partners found in DB.\n";
        }
    } catch (PDOException $e) {
        echo "Query Error: " . $e->getMessage() . "\n";
    }
} else {
    echo "Failed to connect to DB.\n";
}
?>
