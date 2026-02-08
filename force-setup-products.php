<?php
include_once __DIR__ . '/database/db_config.php';

try {
    $database = new Database();
    $db = $database->getConnection();

    // 1. Create Table
    $sql = "CREATE TABLE IF NOT EXISTS products (
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

    $db->exec($sql);
    echo "Table 'products' created successfully.<br>";

    // 2. Insert Dummy Product if Empty
    $stmt = $db->query("SELECT count(*) FROM products");
    if ($stmt->fetchColumn() == 0) {
        $name = "Premium Wellness Kit";
        $slug = "premium-wellness-kit";
        $desc = "A complete wellness kit for your daily needs.";
        $mrp = 2999.00;
        $sales = 2499.00;
        
        $sql_insert = "INSERT INTO products (name, slug, short_description, description, mrp, sales_price, status) VALUES 
        (:name, :slug, :desc, :desc, :mrp, :sales, 'active')";
        
        $stmt_ins = $db->prepare($sql_insert);
        $stmt_ins->execute([':name'=>$name, ':slug'=>$slug, ':desc'=>$desc, ':mrp'=>$mrp, ':sales'=>$sales]);
        echo "Inserted dummy product: $name ($slug)<br>";
    } else {
        echo "Products already exist.<br>";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
