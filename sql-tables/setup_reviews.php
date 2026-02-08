<?php
include_once __DIR__ . '/../database/db_config.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        user_name VARCHAR(100) NOT NULL,
        user_email VARCHAR(100) NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        review_text TEXT,
        review_images TEXT, -- JSON array of image paths
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved', -- Auto-approve for demo
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $db->exec($sql);
    echo "Reviews table created successfully.";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
