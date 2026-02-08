<?php
$page = 'products';
include_once __DIR__ . '/../../database/db_config.php';
$url_prefix = '../';
include_once __DIR__ . '/../includes/header.php';

$database = new Database();
$db = $database->getConnection();

$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Missing ID.');
$message = "";

// Fetch Current Data
$query = "SELECT * FROM products WHERE id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(':id', $id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    die("Product not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    // Slug logic: update only if needed or keep existing stable
    //$slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    
    $short_desc = $_POST['short_description'];
    $description = $_POST['description']; // Summernote content
    
    // Pricing
    $mrp = $_POST['mrp'];
    $purchase_price = $_POST['purchase_price'];
    $sales_price = $_POST['sales_price'];
    $delivery_cost = $_POST['delivery_cost'];
    $packing_cost = $_POST['packing_cost'];
    $total_cost = $purchase_price + $delivery_cost + $packing_cost; 
    
    $status = $_POST['status'];
    
    // Free Product Logic
    $is_free_active = isset($_POST['is_free_product_active']) ? 1 : 0;
    $free_prod_name = $is_free_active ? $_POST['free_product_name'] : null;
    
    // Handle Images
    $target_dir = "../../uploads/products/";
    
    // Featured Image
    $featured_image_path = $row['featured_image'];
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
        $fileName = time() . '_' . basename($_FILES["featured_image"]["name"]);
        $targetFilePath = $target_dir . $fileName;
        if(move_uploaded_file($_FILES["featured_image"]["tmp_name"], $targetFilePath)){
            $featured_image_path = "uploads/products/" . $fileName;
        }
    }

    // Free Product Image
    $free_prod_image = $row['free_product_image'];
    if ($is_free_active && isset($_FILES['free_product_image']) && $_FILES['free_product_image']['error'] == 0) {
        $fileName = time() . '_free_' . basename($_FILES["free_product_image"]["name"]);
        $targetFilePath = $target_dir . $fileName;
        if(move_uploaded_file($_FILES["free_product_image"]["tmp_name"], $targetFilePath)){
            $free_prod_image = "uploads/products/" . $fileName;
        }
    } else if (!$is_free_active) {
        $free_prod_image = null; // Clear if deactivated
    }

    // Update Query
    $update_query = "UPDATE products SET name=:name, short_description=:short_desc, description=:desc, mrp=:mrp, purchase_price=:pp, sales_price=:sp, delivery_cost=:dc, packing_cost=:pc, total_cost=:tc, is_free_product_active=:ifa, free_product_name=:fpn, free_product_image=:fpi, status=:status, featured_image=:f_img WHERE id=:id";
    
    $up_stmt = $db->prepare($update_query);
    $up_stmt->bindParam(':name', $name);
    $up_stmt->bindParam(':short_desc', $short_desc);
    $up_stmt->bindParam(':desc', $description);
    $up_stmt->bindParam(':mrp', $mrp);
    $up_stmt->bindParam(':pp', $purchase_price);
    $up_stmt->bindParam(':sp', $sales_price);
    $up_stmt->bindParam(':dc', $delivery_cost);
    $up_stmt->bindParam(':pc', $packing_cost);
    $up_stmt->bindParam(':tc', $total_cost);
    $up_stmt->bindParam(':ifa', $is_free_active);
    $up_stmt->bindParam(':fpn', $free_prod_name);
    $up_stmt->bindParam(':fpi', $free_prod_image);
    $up_stmt->bindParam(':status', $status);
    $up_stmt->bindParam(':f_img', $featured_image_path);
    $up_stmt->bindParam(':id', $id);

    if ($up_stmt->execute()) {
        $message = "<div class='alert alert-success'>Product updated successfully!</div>";
        // Refresh row data
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $message = "<div class='alert alert-danger'>Failed to update product.</div>";
    }
}
?>

<!-- Summernote & Scripts -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Product: <?php echo htmlspecialchars($row['name']); ?></h1>
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
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($row['name']); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Short Description</label>
                        <textarea name="short_description" class="form-control" rows="3"><?php echo htmlspecialchars($row['short_description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label font-weight-bold">Full Description <span class="text-danger">*</span></label>
                        <textarea id="summernote" name="description" class="form-control" required><?php echo $row['description']; ?></textarea>
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
                                    <option value="active" <?php echo ($row['status']=='active')?'selected':''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($row['status']=='inactive')?'selected':''; ?>>Inactive</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Update Product</button>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header bg-light font-weight-bold">Files</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Featured Image</label>
                                <?php if($row['featured_image']): ?>
                                    <div class="mb-2"><img src="../../<?php echo htmlspecialchars($row['featured_image']); ?>" width="100"></div>
                                <?php endif; ?>
                                <input type="file" name="featured_image" class="form-control" accept="image/*">
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
                    <input type="number" step="0.01" name="mrp" class="form-control" required value="<?php echo $row['mrp']; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Purchase Price</label>
                    <input type="number" step="0.01" name="purchase_price" id="purchase_price" class="form-control price-input" required value="<?php echo $row['purchase_price']; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Delivery Cost</label>
                    <input type="number" step="0.01" name="delivery_cost" id="delivery_cost" class="form-control price-input" value="<?php echo $row['delivery_cost']; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Packing Cost</label>
                    <input type="number" step="0.01" name="packing_cost" id="packing_cost" class="form-control price-input" value="<?php echo $row['packing_cost']; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-primary font-weight-bold">Total Cost (Calc)</label>
                    <input type="text" id="total_cost_display" class="form-control bg-light" readonly value="<?php echo $row['total_cost']; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label text-success font-weight-bold">Sales Price</label>
                    <input type="number" step="0.01" name="sales_price" class="form-control border-success" required value="<?php echo $row['sales_price']; ?>">
                </div>
            </div>

            <hr>

            <!-- Free Product Section -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="is_free_product_active" id="freeProductCheck" <?php echo ($row['is_free_product_active'])?'checked':''; ?>>
                <label class="form-check-label font-weight-bold" for="freeProductCheck">
                    Include Free Product?
                </label>
            </div>

            <div id="freeProductFields" style="display: <?php echo ($row['is_free_product_active'])?'block':'none'; ?>;" class="p-3 bg-light rounded border">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Free Product Name</label>
                        <input type="text" name="free_product_name" class="form-control" value="<?php echo htmlspecialchars($row['free_product_name']); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Free Product Image</label>
                        <?php if($row['free_product_image']): ?>
                            <div class="mb-2"><img src="../../<?php echo htmlspecialchars($row['free_product_image']); ?>" width="100"></div>
                        <?php endif; ?>
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
