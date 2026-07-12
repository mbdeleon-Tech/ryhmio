<?php
require("functions.php");
$page_title = "Rhymio";
include("include/header.php");
?>
<section class="hero">
    <div class="container">
        <div class="col-lg-7">
            <p class="text-uppercase fw-bold text-warning">PHPStorm</p>
            <h1>Gear up for every stage.</h1>
            <p class="lead mt-3">Shop guitars, keyboards, percussion, strings, and pro audio essentials from Rhymio's student-built music store.</p>
            <div class="d-flex gap-2 mt-4">
                <a href="store.php" class="btn btn-primary btn-lg">Shop Products</a>
                <a href="registration.php" class="btn btn-outline-light btn-lg">Create Account</a>
            </div>
        </div>
    </div>
</section>
<section class="retail-band py-4">
    <div class="container">
        <div class="row g-3 text-center text-md-start">
            <div class="col-md-4"><strong>Guitars and Strings</strong><br><span class="text-muted">Acoustic guitars, electric guitars, ukuleles, violins, and accessories.</span></div>
            <div class="col-md-4"><strong>Keys and Percussion</strong><br><span class="text-muted">Portable keyboards, drum kits, sticks, and essentials.</span></div>
            <div class="col-md-4"><strong>Pro Audio Gear</strong><br><span class="text-muted">Microphones and recording tools for home setups.</span></div>
        </div>
    </div>
</section>
<section class="container py-5">
    <div class="d-flex justify-content-between align-items-end gap-3 mb-4">
        <div>
            <p class="text-uppercase text-primary fw-bold mb-1">Why shop Rhymio</p>
            <h2 class="section-title mb-0">A complete musical instrument catalog.</h2>
        </div>
        <a href="store.php" class="btn btn-primary d-none d-md-inline-flex">Browse Store</a>
    </div>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="feature-tile">
                <h3>Curated Categories</h3>
                <p class="mb-0 text-muted">Browse by product family so buyers can quickly find the right gear.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-tile">
                <h3>Managed Inventory</h3>
                <p class="mb-0 text-muted">Admins can update stock levels and prices, while reports show the remaining quantities.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-tile">
                <h3>Account Confirmation</h3>
                <p class="mb-0 text-muted">New buyers receive an email confirmation link after registration.</p>
            </div>
        </div>
    </div>
</section>
<?php include("include/footer.php"); ?>
