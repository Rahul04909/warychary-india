<?php
include_once __DIR__ . '/database/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_review'])) {
    
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $slug = isset($_POST['slug']) ? $_POST['slug'] : '';
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $rating = isset($_POST['rating']) ? intval($_POST['rating']) : 5;
    $review_text = isset($_POST['review_text']) ? trim($_POST['review_text']) : '';
    
    // Validation
    if (empty($name) || empty($email) || empty($review_text) || $product_id == 0) {
        die("Please fill all required fields.");
    }
    
    // Image Upload
    $review_images = [];
    if (isset($_FILES['review_images']) && !empty($_FILES['review_images']['name'][0])) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $upload_dir = 'uploads/reviews/';
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        foreach ($_FILES['review_images']['name'] as $key => $filename) {
            $tmp_name = $_FILES['review_images']['tmp_name'][$key];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            
            if (in_array($ext, $allowed)) {
                $new_filename = uniqid() . '.' . $ext;
                $target_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($tmp_name, $target_path)) {
                    $review_images[] = $target_path;
                }
            }
        }
    }
    
    $review_images_json = json_encode($review_images);
    
    // Insert Logic
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "INSERT INTO reviews (product_id, user_name, user_email, rating, review_text, review_images, status) VALUES (:product_id, :name, :email, :rating, :review_text, :review_images, 'approved')";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':rating', $rating);
    $stmt->bindParam(':review_text', $review_text);
    $stmt->bindParam(':review_images', $review_images_json);
    
    if ($stmt->execute()) {
        header("Location: product-details.php?slug=" . $slug . "&review=success");
        exit;
    } else {
        echo "Error submitting review.";
    }
}
?>
