<?php $prefix = isset($asset_prefix) ? $asset_prefix : ""; ?>
</main>
<footer class="site-footer">
    <div class="container py-4">
        <div class="row g-4 align-items-start">
            <div class="col-lg-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <img class="brand-logo-img footer-logo" src="<?php echo $prefix; ?>assets/rhymio-logo.png" alt="Rhymio logo">
                </div>
                <strong>Rhymio</strong>
                <p class="mb-0 mt-2">Group: PHPStorm</p>
            </div>
            <div class="col-md-4 col-lg-2">
                <strong>Shop</strong>
                <a href="<?php echo $prefix; ?>store.php">Products</a>
                <a href="<?php echo $prefix; ?>cart.php">Cart</a>
                <a href="<?php echo $prefix; ?>login.php">Login</a>
            </div>
            <div class="col-md-4 col-lg-3">
                <strong>Categories</strong>
                <a href="<?php echo $prefix; ?>store.php?category=Guitars">Guitars</a>
                <a href="<?php echo $prefix; ?>store.php?category=Keyboards">Pianos &amp; Keyboards</a>
                <a href="<?php echo $prefix; ?>store.php?category=Drums">Drums &amp; Percussion</a>
                <a href="<?php echo $prefix; ?>store.php?category=Strings">Strings</a>
                <a href="<?php echo $prefix; ?>store.php?category=Studio+Gear">Studio Gear</a>
                <a href="<?php echo $prefix; ?>store.php?category=Effects+Pedals">Effects Pedals</a>
                <a href="<?php echo $prefix; ?>store.php?category=Accessories">Accessories</a>
                <a href="<?php echo $prefix; ?>store.php?category=Ukuleles">Ukuleles</a>
                <a href="<?php echo $prefix; ?>store.php?category=Brass+%26+Woodwinds">Brass &amp; Woodwinds</a>
            </div>
            <div class="col-md-4 col-lg-3">
                <strong>Project Notice</strong>
                <p class="mb-0 disclaimer">Disclaimer: This website is for educational purposes only and is a requirement for our final project.</p>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
