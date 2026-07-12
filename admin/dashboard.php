<?php
$asset_prefix = "../";
require("../db.php");
require("../functions.php");
require_admin();
$page_title = "Admin Dashboard - Rhymio";

$products_count = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM products"))['total'];
$stock_count = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(stock) AS total FROM products"))['total'];
$orders_count = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'];
$admins_count = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='admin'"))['total'];

include("../include/header.php");
?>
<section class="container py-5 admin-shell">
    <div class="row">
        <?php include("sidebar.php"); ?>
        <div class="col-lg-9">
            <h1 class="section-title">System Admin Page</h1>
            <p class="text-muted">Welcome, <?php echo h(current_user_name()); ?>. Manage admin users, stock levels, prices, and reports.</p>
            <div class="row g-3">
                <div class="col-md-3"><div class="card dashboard-tile p-3"><small>Products</small><strong class="h3"><?php echo $products_count; ?></strong></div></div>
                <div class="col-md-3"><div class="card dashboard-tile p-3"><small>Total Stock</small><strong class="h3"><?php echo $stock_count; ?></strong></div></div>
                <div class="col-md-3"><div class="card dashboard-tile p-3"><small>Orders</small><strong class="h3"><?php echo $orders_count; ?></strong></div></div>
                <div class="col-md-3"><div class="card dashboard-tile p-3"><small>Admins</small><strong class="h3"><?php echo $admins_count; ?></strong></div></div>
            </div>
        </div>
    </div>
</section>
<?php include("../include/footer.php"); ?>
