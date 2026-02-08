<?php
include_once __DIR__ . '/database/db_config.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // Create Table
    $sql = "CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        user_name VARCHAR(100) NOT NULL,
        user_email VARCHAR(100) NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        review_text TEXT,
        review_images TEXT, 
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->exec($sql);
    echo "Table 'reviews' checked/created.<br>";

    // Check if any review exists
    $stmt = $db->query("SELECT count(*) FROM reviews");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Get a valid product ID
        $stmt_prod = $db->query("SELECT id FROM products LIMIT 1");
        $prod = $stmt_prod->fetch(PDO::FETCH_ASSOC);
        
        if ($prod) {
            $pid = $prod['id'];
            // Seed Reviews
            $sql_insert = "INSERT INTO reviews (product_id, user_name, user_email, rating, review_text, status) VALUES 
            ($pid, 'Amit Patel', 'amit@example.com', 5, 'Excellent quality! Really satisfied with the purchase.', 'approved'),
            ($pid, 'Sneha Reddy', 'sneha@example.com', 4, 'Descent product, delivery was quick.', 'approved')";
            
            $db->exec($sql_insert);
            echo "Seeded 2 reviews for Product ID: $pid<br>";
        } else {
            echo "No products found to seed reviews against.<br>";
        }
    } else {
        echo "Reviews already exist ($count).<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
