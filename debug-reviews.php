<?php
include_once __DIR__ . '/database/db_config.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    echo "<h3>Products:</h3>";
    $stmt = $db->query("SELECT id, name, slug FROM products LIMIT 5");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($products) {
        echo "<pre>"; print_r($products); echo "</pre>";
    } else {
        echo "No products found.<br>";
    }
    
    echo "<h3>Reviews:</h3>";
    $stmt = $db->query("SELECT * FROM reviews");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if ($reviews) {
        echo "<pre>"; print_r($reviews); echo "</pre>";
    } else {
        echo "No reviews found.<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
