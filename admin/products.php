<?php
$asset_prefix = "../";
require("../db.php");
require("../functions.php");
require_admin();
$page_title = "Inventory and Prices - Rhymio";
$message = "";
$message_type = "success";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $name_raw = isset($_POST['name']) ? trim($_POST['name']) : "";
    $description_raw = isset($_POST['description']) ? trim($_POST['description']) : "";
    $price_raw = isset($_POST['price']) ? trim($_POST['price']) : "";
    $stock_raw = isset($_POST['stock']) ? trim($_POST['stock']) : "";
    $image_url_raw = isset($_POST['image_url']) ? trim($_POST['image_url']) : "";
    $status_raw = isset($_POST['status']) ? trim($_POST['status']) : "active";

    if ($category_id <= 0 || $name_raw === "" || $description_raw === "" || $price_raw === "" || $stock_raw === "" || $image_url_raw === "") {
        $message = "Please complete all required fields.";
        $message_type = "danger";
    } else {
        $name = clean_input($conn, $name_raw);
        $description = clean_input($conn, $description_raw);
        $price = max(0, (float)$price_raw);
        $stock = max(0, (int)$stock_raw);
        $image_url = clean_input($conn, $image_url_raw);
        $status = $status_raw === "inactive" ? "inactive" : "active";

        if ($id > 0) {
            mysqli_query($conn, "UPDATE products SET category_id=$category_id, name='$name', description='$description', price=$price, stock=$stock, image_url='$image_url', status='$status' WHERE id=$id");
            audit_log($conn, "Modify product", "Updated the price or stock level for $name");
            $message = "Product updated.";
        } else {
            mysqli_query($conn, "INSERT INTO products (category_id, name, description, price, stock, image_url, status)
                                 VALUES ($category_id, '$name', '$description', $price, $stock, '$image_url', '$status')");
            audit_log($conn, "Add product", "Added $name to the product catalog");
            $message = "Product added.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_category'])) {
    $category_name_raw = trim($_POST['category_name']);
    if ($category_name_raw === "") {
        $message = "Please enter a category name.";
        $message_type = "danger";
    } else {
        $category_name = clean_input($conn, $category_name_raw);
        mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$category_name')");
        audit_log($conn, "Add category", "Added category $category_name");
        $message = "Category added.";
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$edit_id"));
}
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
$products = mysqli_query($conn, "SELECT products.*, categories.name AS category_name FROM products INNER JOIN categories ON products.category_id=categories.id ORDER BY products.name");
include("../include/header.php");
?>
<section class="container py-5 admin-shell">
    <div class="row">
        <?php include("sidebar.php"); ?>
        <div class="col-lg-9">
            <h1 class="section-title">Add or Modify Products</h1>
            <?php if ($message): ?><div class="alert alert-<?php echo h($message_type); ?>"><?php echo h($message); ?></div><?php endif; ?>
            <form method="POST" class="card p-4 mb-4">
                <label class="form-label">Add Category</label>
                <div class="input-group">
                    <input class="form-control" name="category_name" placeholder="Example: Amplifiers">
                    <button name="save_category" class="btn btn-outline-secondary">Add Category</button>
                </div>
            </form>
            <form method="POST" class="card p-4 mb-4">
                <input type="hidden" name="id" value="<?php echo $edit ? (int)$edit['id'] : 0; ?>">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Product name</label><input class="form-control" name="name" value="<?php echo $edit ? h($edit['name']) : ''; ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Category</label><select name="category_id" class="form-select"><?php while ($cat = mysqli_fetch_assoc($categories)): ?><option value="<?php echo (int)$cat['id']; ?>" <?php echo $edit && $edit['category_id'] == $cat['id'] ? 'selected' : ''; ?>><?php echo h($cat['name']); ?></option><?php endwhile; ?></select></div>
                    <div class="col-md-4"><label class="form-label">Price</label><input type="number" step="0.01" class="form-control" name="price" value="<?php echo $edit ? h($edit['price']) : ''; ?>" required></div>
                    <div class="col-md-4"><label class="form-label">Stock</label><input type="number" class="form-control" name="stock" value="<?php echo $edit ? h($edit['stock']) : ''; ?>" required></div>
                    <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="inactive" <?php echo $edit && $edit['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option></select></div>
                    <div class="col-12"><label class="form-label">Image URL or Local Path</label><input class="form-control" name="image_url" placeholder="Example: assets/products/guitar.png" value="<?php echo $edit ? h($edit['image_url']) : ''; ?>" required></div>
                    <div class="col-12"><label class="form-label">Description</label><textarea class="form-control" name="description" required><?php echo $edit ? h($edit['description']) : ''; ?></textarea></div>
                </div>
                <button name="save_product" class="btn btn-primary mt-3"><?php echo $edit ? 'Update Product' : 'Add Product'; ?></button>
            </form>
            <div class="table-responsive card">
                <table class="table mb-0">
                    <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($products)): ?>
                            <tr><td><?php echo h($row['name']); ?></td><td><?php echo h($row['category_name']); ?></td><td><?php echo h(peso($row['price'])); ?></td><td><?php echo (int)$row['stock']; ?></td><td><?php echo h($row['status']); ?></td><td><a class="btn btn-sm btn-outline-secondary" href="products.php?edit=<?php echo (int)$row['id']; ?>">Modify</a></td></tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php include("../include/footer.php"); ?>
