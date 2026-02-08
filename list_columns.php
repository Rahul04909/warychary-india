<?php
require 'database/db_config.php';
$db = (new Database())->getConnection();

function listCols($db, $table) {
    file_put_contents('cols_utf8.log', "TABLE: $table\n", FILE_APPEND);
    try {
        $stmt = $db->query("DESCRIBE $table");
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
            file_put_contents('cols_utf8.log', $col['Field'] . "\n", FILE_APPEND);
        }
    } catch (Exception $e) {
        file_put_contents('cols_utf8.log', "Error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
    file_put_contents('cols_utf8.log', "\n", FILE_APPEND);
}

if (file_exists('cols_utf8.log')) unlink('cols_utf8.log');
listCols($db, 'senior_partners');
listCols($db, 'partners');
?>
