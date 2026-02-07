<?php
// Mock GET request
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET['code'] = 'FVYEXR'; // Using the code found in the DB

// Define path to API file
$api_file = __DIR__ . '/api/validate-referral.php';

echo "Testing API at: $api_file\n";

if (file_exists($api_file)) {
    // Capture output
    ob_start();
    include $api_file;
    $output = ob_get_clean();
    
    echo "Raw Output:\n";
    var_dump($output);
    
    echo "\nDecoded JSON:\n";
    $json = json_decode($output, true);
    print_r($json);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "\nJSON Error: " . json_last_error_msg();
    }
} else {
    echo "API file not found.";
}
?>
