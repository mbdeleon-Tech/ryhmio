<?php
$asset_prefix = "../";
require("../db.php");
require("../functions.php");
require_admin();
$page_title = "Reports - Rhymio";
$inventory = mysqli_query($conn, "SELECT products.*, categories.name AS category_name FROM products INNER JOIN categories ON products.category_id=categories.id ORDER BY products.stock ASC");
$logs = mysqli_query($conn, "SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 100");
include("../include/header.php");
?>
<section class="container py-5 admin-shell">
    <div class="row">
        <?php include("sidebar.php"); ?>
        <div class="col-lg-9">
            <h1 class="section-title">Reports</h1>
            <h2 class="h4 mt-4">Remaining Inventory</h2>
            <div class="table-responsive card mb-4">
                <table class="table mb-0">
                    <thead><tr><th>Product</th><th>Category</th><th>Remaining Stock</th><th>Price</th></tr></thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($inventory)): ?>
                            <tr><td><?php echo h($row['name']); ?></td><td><?php echo h($row['category_name']); ?></td><td><?php echo (int)$row['stock']; ?></td><td><?php echo h(peso($row['price'])); ?></td></tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <h2 class="h4">Audit Log Report</h2>
            <div class="table-responsive card">
                <table class="table mb-0">
                    <thead><tr><th>Date and Time</th><th>User</th><th>Activity</th><th>Details</th></tr></thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($logs)): ?>
                            <tr><td><?php echo h($row['created_at']); ?></td><td><?php echo h($row['actor_name']); ?></td><td><?php echo h($row['action']); ?></td><td><?php echo h($row['details']); ?></td></tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php include("../include/footer.php"); ?>
