<?php
require("db.php");
require("functions.php");
$page_title = "Store - Rhymio";
$show_category_nav = true;

if (isset($_POST['add_to_cart'])) {
    require_buyer_login();
    $product_id = (int)$_POST['product_id'];
    $stock_check = mysqli_query($conn, "SELECT name, stock FROM products WHERE id=$product_id AND status='active'");
    if (mysqli_num_rows($stock_check) === 1) {
        $product = mysqli_fetch_assoc($stock_check);
        if ((int)$product['stock'] > 0) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            $current_quantity = isset($_SESSION['cart'][$product_id]) ? (int)$_SESSION['cart'][$product_id] : 0;
            if ($current_quantity < (int)$product['stock']) {
                $_SESSION['cart'][$product_id] = $current_quantity + 1;
                audit_log($conn, "Add to cart", $product['name'] . " was added to the cart");
            }
        }
    }
    header("Location: cart.php");
    exit;
}

$category_filter = isset($_GET['category']) ? clean_input($conn, $_GET['category']) : "";
$sql = "SELECT products.*, categories.name AS category_name FROM products
        INNER JOIN categories ON products.category_id = categories.id
        WHERE products.status='active'";
if ($category_filter) {
    $sql .= " AND categories.name='$category_filter'";
}
$sql .= " ORDER BY categories.name, products.name";
$products = mysqli_query($conn, $sql);

// Local catalog images keep product photos dependable on the hosted website.
$product_images = [
    "Cedarline Acoustic Guitar" => "assets/products/cedarline-acoustic-guitar.jpg",
    "VoltEdge Electric Guitar" => "assets/products/voltedge-electric-guitar.jpg",
    "StageLite 61-Key Keyboard" => "assets/products/stagelite-keyboard.jpg",
    "PulseKick Drum Set" => "assets/products/pulsekick-drum-set.jpg",
    "Arco Student Violin" => "assets/products/arco-student-violin.jpg",
    "ClearTone USB Microphone" => "assets/products/cleartone-usb-microphone.jpg",
    "BlueWave Chorus Pedal" => "assets/products/bluewave-chorus-pedal.jpg",
    "DriveBox Overdrive Pedal" => "assets/products/drivebox-overdrive-pedal.jpg",
    "Rhymio Clip-On Tuner" => "assets/products/rhymio-clip-on-tuner.jpg",
    "Padded Guitar Gig Bag" => "assets/products/padded-guitar-gig-bag.jpg",
    "IslandTone Concert Ukulele" => "assets/products/islandtone-concert-ukulele.jpg",
    "Mahogany Soprano Ukulele" => "assets/products/mahogany-soprano-ukulele.jpg",
    "Student Alto Saxophone" => "assets/products/student-alto-saxophone.jpg",
    "Brassline Trumpet" => "assets/products/brassline-trumpet.jpg"
];

// Correct older catalog copy without overriding descriptions edited by an admin.
$description_corrections = [
    "Five-piece beginner drum kit with cymbals, throne, and kick pedal." => "Five-piece beginner drum kit with cymbals, a throne, and a kick pedal.",
    "Full-size violin with bow, case, rosin, and starter shoulder rest." => "Full-size violin with a bow, case, rosin, and starter shoulder rest.",
    "Compact chorus pedal for shimmering clean tones and wide modulation sounds." => "Compact chorus pedal for shimmering clean tones and wide modulation effects.",
    "Lightweight padded gig bag with shoulder straps and accessory pocket." => "Lightweight padded gig bag with shoulder straps and an accessory pocket.",
    "Concert ukulele with bright tone, smooth fretboard, and starter strings." => "Concert ukulele with a bright tone, smooth fretboard, and starter strings.",
    "Entry-level alto saxophone with case, neck strap, mouthpiece, and reeds." => "Entry-level alto saxophone with a case, neck strap, mouthpiece, and reeds.",
    "Beginner trumpet with lacquer finish, case, mouthpiece, and cleaning cloth." => "Beginner trumpet with a lacquer finish, case, mouthpiece, and cleaning cloth."
];
include("include/header.php");
?>
<section class="container py-5">
    <div class="mb-4">
        <h1 class="section-title">Instrument Store</h1>
        <p class="text-muted mb-0">Browse instruments by category, check availability, and log in to place an order.</p>
    </div>
    <div class="row g-4">
        <?php while ($row = mysqli_fetch_assoc($products)): ?>
            <?php
            $image_path = $row['image_url'];
            if (strpos($image_path, "images.unsplash.com") !== false && isset($product_images[$row['name']])) {
                $image_path = $product_images[$row['name']];
            }
            $description = isset($description_corrections[$row['description']])
                ? $description_corrections[$row['description']]
                : $row['description'];
            ?>
            <div class="col-sm-6 col-lg-4">
                <div class="card h-100 product-card">
                    <img src="<?php echo h($image_path); ?>" class="product-img" alt="<?php echo h($row['name']); ?>" loading="lazy" onerror="this.onerror=null;this.src='assets/rhymio-logo.png';">
                    <div class="card-body d-flex flex-column">
                        <span class="badge category-pill align-self-start mb-2"><?php echo h($row['category_name']); ?></span>
                        <h2 class="h5"><?php echo h($row['name']); ?></h2>
                        <p class="text-muted flex-grow-1"><?php echo h($description); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <strong class="price-text"><?php echo h(peso($row['price'])); ?></strong>
                            <small class="stock-text"><?php echo (int)$row['stock']; ?> in stock</small>
                        </div>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "buyer"): ?>
                            <form method="POST" class="mt-3">
                                <input type="hidden" name="product_id" value="<?php echo (int)$row['id']; ?>">
                                <button name="add_to_cart" class="btn btn-primary w-100" <?php echo (int)$row['stock'] <= 0 ? 'disabled' : ''; ?>>Add to Cart</button>
                            </form>
                        <?php else: ?>
                            <form method="POST" class="mt-3">
                                <input type="hidden" name="product_id" value="<?php echo (int)$row['id']; ?>">
                                <button name="add_to_cart" class="btn btn-primary w-100" <?php echo (int)$row['stock'] <= 0 ? 'disabled' : ''; ?>>Add to Cart</button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</section>
<?php include("include/footer.php"); ?>
