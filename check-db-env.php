<?php
include_once __DIR__ . '/database/db_config.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    echo "<h3>Included Files:</h3><pre>";
    print_r(get_included_files());
    echo "</pre>";

    echo "<h3>Current SELECT DATABASE():</h3>";
    echo $db->query("SELECT DATABASE()")->fetchColumn() . "<br>";

    echo "<h3>Tables in DB:</h3>";
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>" . implode("", array_map(fn($t) => "<li>$t</li>", $tables)) . "</ul>";

    echo "<h3>Checking 'products' table specifically:</h3>";
    try {
        $count = $db->query("SELECT count(*) FROM products")->fetchColumn();
        echo "Products count: $count<br>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage() . "<br>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
