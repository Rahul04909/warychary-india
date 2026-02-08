<?php
session_start();
include_once __DIR__ . '/../../database/db_config.php';

if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();
    
    $id = $_GET['id'];
    
    // Optional: Get image paths to unlink them
    $query = "SELECT featured_image, gallery_images, free_product_image FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Delete Record
    $del_query = "DELETE FROM products WHERE id = :id";
    $del_stmt = $db->prepare($del_query);
    $del_stmt->bindParam(':id', $id);
    
    if ($del_stmt->execute()) {
        // Unlink files if needed (optional logic)
        // if($row['featured_image'] && file_exists("../../".$row['featured_image'])) unlink("../../".$row['featured_image']);
        // ... similar for gallery and free product image
        
        $_SESSION['success_message'] = "Product deleted successfully.";
    } else {
        $_SESSION['error_message'] = "Failed to delete product.";
    }
}

header("Location: index.php");
exit;
?>
