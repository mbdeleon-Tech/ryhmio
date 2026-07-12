<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$page_title = isset($page_title) ? $page_title : "Rhymio";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES, "UTF-8"); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo isset($asset_prefix) ? $asset_prefix : ''; ?>assets/style.css?v=20260710-2">
</head>
<body>
<div class="top-strip">
    <div class="container d-flex flex-column flex-md-row justify-content-between gap-1">
        <span>MID-YEAR CASHBACK - A student-built musical instrument store</span>
        <span>Philippines (PHP) - PHPStorm Group</span>
    </div>
</div>
<nav class="navbar navbar-expand-lg navbar-dark site-nav sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-3" href="<?php echo isset($asset_prefix) ? $asset_prefix : ''; ?>index.php">
            <img class="brand-logo-img" src="<?php echo isset($asset_prefix) ? $asset_prefix : ''; ?>assets/rhymio-logo.png" alt="Rhymio logo">
            <span>Rhymio</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <li class="nav-item"><a class="nav-link" href="<?php echo isset($asset_prefix) ? $asset_prefix : ''; ?>store.php">Store</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo isset($asset_prefix) ? $asset_prefix : ''; ?>cart.php">Cart (<?php echo function_exists('cart_count') ? cart_count() : 0; ?>)</a></li>
                <li class="nav-item"><a class="nav-link" href="<?php echo isset($asset_prefix) ? $asset_prefix : ''; ?>about.php">About</a></li>
                <?php if (function_exists('is_admin') && is_admin()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?php echo isset($asset_prefix) ? $asset_prefix : ''; ?>admin/dashboard.php">Admin</a></li>
                <?php endif; ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="btn btn-outline-light btn-sm ms-lg-2" href="<?php echo isset($asset_prefix) ? $asset_prefix : ''; ?>logout.php">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="btn btn-light btn-sm ms-lg-2" href="<?php echo isset($asset_prefix) ? $asset_prefix : ''; ?>login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php if (!empty($show_category_nav)): ?>
    <div class="category-nav" aria-label="Store categories">
        <div class="container d-flex flex-wrap justify-content-center gap-2 gap-md-4">
            <a href="store.php">All Products</a>
            <a href="store.php?category=Guitars">Guitars</a>
            <a href="store.php?category=Keyboards">Pianos &amp; Keyboards</a>
            <a href="store.php?category=Drums">Drums &amp; Percussion</a>
            <a href="store.php?category=Strings">Strings</a>
            <a href="store.php?category=Studio+Gear">Studio Gear</a>
            <a href="store.php?category=Effects+Pedals">Effects Pedals</a>
            <a href="store.php?category=Accessories">Accessories</a>
            <a href="store.php?category=Ukuleles">Ukuleles</a>
            <a href="store.php?category=Brass+%26+Woodwinds">Brass &amp; Woodwinds</a>
        </div>
    </div>
<?php endif; ?>
<main>
