<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/database/db_config.php';
$database = new Database();
$db = $database->getConnection();

echo "<h2>System Diagnostics</h2>";

// 1. Check Write Permissions
$logFile = __DIR__ . '/permissions_test.log';
if (file_put_contents($logFile, "Test Log\n")) {
    echo "<p style='color:green'>[PASS] File Write Permission OK</p>";
    unlink($logFile);
} else {
    echo "<p style='color:red'>[FAIL] Cannot write to directory!</p>";
}

// 2. Check Database Columns
function checkColumn($db, $table, $col) {
    try {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$col'");
        if ($stmt->rowCount() > 0) {
            echo "<p style='color:green'>[PASS] Column `$table`.`$col` exists.</p>";
        } else {
            echo "<p style='color:red'>[FAIL] Column `$table`.`$col` MISSING!</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color:red'>[ERROR] " . $e->getMessage() . "</p>";
    }
}

checkColumn($db, 'partners', 'earning');
checkColumn($db, 'partners', 'total_earnings');
checkColumn($db, 'senior_partners', 'earning');
checkColumn($db, 'senior_partners', 'total_earnings');

// 3. Check Vendor Autoload
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "<p style='color:green'>[PASS] vendor/autoload.php found.</p>";
} else {
    echo "<p style='color:red'>[FAIL] vendor/autoload.php MISSING!</p>";
}
?>
