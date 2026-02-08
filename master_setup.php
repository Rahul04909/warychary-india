<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once __DIR__ . '/database/db_config.php';

echo "<h1>Master Database Setup</h1>";

try {
    $database = new Database();
    $db = $database->getConnection();

    // 1. Check DB Name
    echo "<h3>1. Database Connection</h3>";
    echo "Connected to: " . $db->query("SELECT DATABASE()")->fetchColumn() . "<br>";

    // 2. Drop Tables (Clean Slate)
    echo "<h3>2. Cleaning Old Tables</h3>";
    $db->exec("DROP TABLE IF EXISTS reviews");
    $db->exec("DROP TABLE IF EXISTS products");
    echo "Dropped 'reviews' and 'products'.<br>";

    // 3. Create Products Table
    echo "<h3>3. Creating Products Table</h3>";
    $sql_prod = "CREATE TABLE products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) UNIQUE NOT NULL,
        featured_image VARCHAR(255),
        gallery_images TEXT, 
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
    $db->exec($sql_prod);
    echo "Table 'products' created.<br>";

    // 4. Seed Product
    echo "<h3>4. Seeding Product</h3>";
    $name = "Premium Wellness Kit";
    $slug = "premium-wellness-kit";
    $desc = "A complete wellness kit.";
    $sql_ins_prod = "INSERT INTO products (name, slug, short_description, description, mrp, sales_price, status) VALUES 
    ('$name', '$slug', '$desc', '$desc', 2999.00, 2499.00, 'active')";
    $db->exec($sql_ins_prod);
    $pid = $db->lastInsertId();
    echo "Inserted Product ID: $pid ($slug)<br>";

    // 5. Create Reviews Table
    echo "<h3>5. Creating Reviews Table</h3>";
    $sql_rev = "CREATE TABLE reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        user_name VARCHAR(100) NOT NULL,
        user_email VARCHAR(100) NOT NULL,
        rating INT NOT NULL,
        review_text TEXT,
        review_images TEXT, 
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'approved',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $db->exec($sql_rev);
    echo "Table 'reviews' created.<br>";

    // 6. Seed Reviews
    echo "<h3>6. Seeding Reviews</h3>";
    $sql_ins_rev = "INSERT INTO reviews (product_id, user_name, user_email, rating, review_text, status) VALUES 
    ($pid, 'Amit Patel', 'amit@example.com', 5, 'Excellent product!', 'approved'),
    ($pid, 'Sneha Reddy', 'sneha@example.com', 4, 'Really good.', 'approved')";
    $db->exec($sql_ins_rev);
    echo "Seeded reviews for Product ID: $pid<br>";

    // 7. Verification list
    echo "<h3>7. Verification</h3>";
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Current Tables: " . implode(", ", $tables);

} catch (PDOException $e) {
    echo "<h2 style='color:red'>Error: " . $e->getMessage() . "</h2>";
}
?>
