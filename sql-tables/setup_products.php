<?php
require_once "../database/db_config.php";

try {
    $database = new Database();
    $db = $database->getConnection();

    $sql = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        featured_image VARCHAR(255),
        gallery_images TEXT, -- JSON Array of image paths
        short_description TEXT,
        description LONGTEXT,
        mrp DECIMAL(10, 2) DEFAULT 0.00,
        purchase_price DECIMAL(10, 2) DEFAULT 0.00,
        sales_price DECIMAL(10, 2) DEFAULT 0.00,
        delivery_cost DECIMAL(10, 2) DEFAULT 0.00,
        packing_cost DECIMAL(10, 2) DEFAULT 0.00,
        total_cost DECIMAL(10, 2) DEFAULT 0.00,
        is_free_product_active TINYINT(1) DEFAULT 0,
        free_product_name VARCHAR(255),
        free_product_image VARCHAR(255),
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

    $db->exec($sql);
    echo "Table 'products' created successfully.";

} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
