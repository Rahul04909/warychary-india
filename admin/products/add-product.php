<?php
$page = 'products';
include_once __DIR__ . '/../../database/db_config.php';
$url_prefix = '../';
include_once __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    $short_desc = $_POST['short_description'];
    $description = $_POST['description']; // Summernote content
    
    // Pricing
    $mrp = $_POST['mrp'];
    $purchase_price = $_POST['purchase_price'];
    $sales_price = $_POST['sales_price'];
    $delivery_cost = $_POST['delivery_cost'];
    $packing_cost = $_POST['packing_cost'];
    $total_cost = $purchase_price + $delivery_cost + $packing_cost; // Calculated locally but stored for reference
    
    $status = $_POST['status'];
    
    // Free Product Logic
    $is_free_active = isset($_POST['is_free_product_active']) ? 1 : 0;
    $free_prod_name = $is_free_active ? $_POST['free_product_name'] : null;
    $free_prod_image = null;

    // Image Upload Handler
    $target_dir = "../../uploads/products/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // Featured Image
    $featured_image_path = "";
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
        $fileName = time() . '_' . basename($_FILES["featured_image"]["name"]);
        $targetFilePath = $target_dir . $fileName;
        if(move_uploaded_file($_FILES["featured_image"]["tmp_name"], $targetFilePath)){
            $featured_image_path = "uploads/products/" . $fileName;
        }
    }

    // Free Product Image
    if ($is_free_active && isset($_FILES['free_product_image']) && $_FILES['free_product_image']['error'] == 0) {
        $fileName = time() . '_free_' . basename($_FILES["free_product_image"]["name"]);
        $targetFilePath = $target_dir . $fileName;
        if(move_uploaded_file($_FILES["free_product_image"]["tmp_name"], $targetFilePath)){
            $free_prod_image = "uploads/products/" . $fileName;
        }
    }

    // Gallery Images
    $gallery_paths = [];
    if(isset($_FILES['gallery_images'])){
        $countfiles = count($_FILES['gallery_images']['name']);
        for($i=0; $i<$countfiles; $i++){
            if($_FILES['gallery_images']['error'][$i] == 0){
                $fileName = time() . '_' . $i . '_' . basename($_FILES['gallery_images']['name'][$i]);
                $targetFilePath = $target_dir . $fileName;
                if(move_uploaded_file($_FILES['gallery_images']['tmp_name'][$i], $targetFilePath)){
                    $gallery_paths[] = "uploads/products/" . $fileName;
                }
            }
        }
    }
    $gallery_json = !empty($gallery_paths) ? json_encode($gallery_paths) : null;

    // Logic to ensure unique slug
    $check_slug = "SELECT id FROM products WHERE slug = :slug";
    $stmt_slug = $db->prepare($check_slug);
    $stmt_slug->bindParam(':slug', $slug);
    $stmt_slug->execute();
    if($stmt_slug->rowCount() > 0){
        $slug = $slug . '-' . time();
    }

    $query = "INSERT INTO products (name, slug, featured_image, gallery_images, short_description, description, mrp, purchase_price, sales_price, delivery_cost, packing_cost, total_cost, is_free_product_active, free_product_name, free_product_image, status) VALUES (:name, :slug, :featured_image, :gallery_images, :short_description, :description, :mrp, :purchase_price, :sales_price, :delivery_cost, :packing_cost, :total_cost, :is_free_active, :free_name, :free_image, :status)";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':slug', $slug);
    $stmt->bindParam(':featured_image', $featured_image_path);
    $stmt->bindParam(':gallery_images', $gallery_json);
    $stmt->bindParam(':short_description', $short_desc);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':mrp', $mrp);
    $stmt->bindParam(':purchase_price', $purchase_price);
    $stmt->bindParam(':sales_price', $sales_price);
    $stmt->bindParam(':delivery_cost', $delivery_cost);
    $stmt->bindParam(':packing_cost', $packing_cost);
    $stmt->bindParam(':total_cost', $total_cost);
    $stmt->bindParam(':is_free_active', $is_free_active);
    $stmt->bindParam(':free_name', $free_prod_name);
    $stmt->bindParam(':free_image', $free_prod_image);
    $stmt->bindParam(':status', $status);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Product added successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Failed to add product.</div>";
    }
}
?>

<!-- Summernote & Scripts -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Add New Product</h1>
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php echo $message; ?>
        
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <!-- Basic Info -->
                <div class="col-md-8">
                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Product Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter product name">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="3" placeholder="Brief summary..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Full Description <span class="text-danger">*</span></label>
                        <textarea id="summernote" name="description" class="form-control" required></textarea>
                    </div>
                </div>

                <!-- Sidebar / Images / Status -->
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-header bg-light font-weight-bold">Publish</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Save Product</button>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light font-weight-bold">Files</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Featured Image</label>
                                <input type="file" name="featured_image" class="form-control" required accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gallery Images</label>
                                <input type="file" name="gallery_images[]" class="form-control" multiple accept="image/*">
                                <small class="text-muted">Select multiple files</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr>

            <!-- Pricing Section -->
            <h5 class="font-weight-bold mb-3">Pricing & Costs</h5>
            <div class="row">
                <div class="col-md-2">
                    <label class="form-label">MRP</label>
                    <input type="number" step="0.01" name="mrp" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Purchase Price</label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control price-input" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Delivery Cost</label>
                    <input type="number" step="0.01" name="delivery_cost" id="delivery_cost" class="form-control price-input" value="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Packing Cost</label>
                    <input type="number" step="0.01" name="packing_cost" id="packing_cost" class="form-control price-input" value="0">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-primary font-weight-bold">Total Cost (Calc)</label>
                    <input type="text" id="total_cost_display" class="form-control bg-light" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label text-success font-weight-bold">Sales Price</label>
                    <input type="number" step="0.01" name="sales_price" class="form-control border-success" required>
                </div>
            </div>

            <hr>

            <!-- Free Product Section -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_free_product_active" id="freeProductCheck">
                <label class="form-check-label font-weight-bold" for="freeProductCheck">
                    Include Free Product?
                </label>
            </div>

            <div id="freeProductFields" style="display: none;" class="p-3 bg-light rounded border">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Free Product Name</label>
                        <input type="text" name="free_product_name" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Free Product Image</label>
                        <input type="file" name="free_product_image" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            placeholder: 'Enter product description here...',
            tabsize: 2,
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });

        // Price Calculation
        $('.price-input').on('input', function() {
            let purchase = parseFloat($('#purchase_price').val()) || 0;
            let delivery = parseFloat($('#delivery_cost').val()) || 0;
            let packing = parseFloat($('#packing_cost').val()) || 0;
            let total = purchase + delivery + packing;
            $('#total_cost_display').val(total.toFixed(2));
        });

        // Toggle Free Product
        $('#freeProductCheck').change(function() {
            if(this.checked) {
                $('#freeProductFields').slideDown();
            } else {
                $('#freeProductFields').slideUp();
            }
        });
    });
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
