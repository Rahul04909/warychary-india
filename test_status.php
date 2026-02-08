<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/database/db_config.php';
$database = new Database();
$db = $database->getConnection();

echo "System Diagnostics\n";
echo "==================\n";

// 1. Check Write Permissions
$logFile = __DIR__ . '/permissions_test.log';
if (file_put_contents($logFile, "Test Log\n")) {
    echo "[PASS] File Write Permission OK\n";
    unlink($logFile);
} else {
    echo "[FAIL] Cannot write to directory!\n";
}

// 2. Check Database Columns
function checkColumn($db, $table, $col) {
    try {
        $stmt = $db->query("SHOW COLUMNS FROM `$table` LIKE '$col'");
        if ($stmt->rowCount() > 0) {
            echo "[PASS] Column `$table`.`$col` exists.\n";
        } else {
            echo "[FAIL] Column `$table`.`$col` MISSING!\n";
        }
    } catch (Exception $e) {
        echo "[ERROR] " . $e->getMessage() . "\n";
    }
}

checkColumn($db, 'partners', 'earning');
checkColumn($db, 'partners', 'total_earnings');
checkColumn($db, 'senior_partners', 'earning');
checkColumn($db, 'senior_partners', 'total_earnings');

// 3. Check Vendor Autoload
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "[PASS] vendor/autoload.php found.\n";
} else {
    echo "[FAIL] vendor/autoload.php MISSING!\n";
}
?>
