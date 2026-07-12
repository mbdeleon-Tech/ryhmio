<?php
require("db.php");
require("functions.php");
require_buyer_login();
$page_title = "Cart - Rhymio";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'], $_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $product_id => $qty) {
        $product_id = (int)$product_id;
        $qty = max(0, (int)$qty);
        $stock_result = mysqli_query($conn, "SELECT stock FROM products WHERE id=$product_id AND status='active'");
        $stock_row = $stock_result ? mysqli_fetch_assoc($stock_result) : null;
        $available_stock = $stock_row ? (int)$stock_row['stock'] : 0;
        $qty = min($qty, $available_stock);
        if ($qty === 0) {
            unset($_SESSION['cart'][$product_id]);
        } else {
            $_SESSION['cart'][$product_id] = $qty;
        }
    }
    audit_log($conn, "Cart update", "Cart quantities were updated");
}

$items = [];
$total = 0;
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    $ids = implode(",", array_map("intval", array_keys($_SESSION['cart'])));
    $result = mysqli_query($conn, "SELECT * FROM products WHERE id IN ($ids)");
    while ($row = mysqli_fetch_assoc($result)) {
        $row['quantity'] = $_SESSION['cart'][$row['id']];
        $row['subtotal'] = $row['quantity'] * $row['price'];
        $total += $row['subtotal'];
        $items[] = $row;
    }
}

include("include/header.php");
?>
<section class="container py-5">
    <h1 class="section-title">Cart</h1>
    <?php if (count($items) === 0): ?>
        <div class="alert alert-info">Your cart is empty.</div>
        <a href="store.php" class="btn btn-primary">Continue Shopping</a>
    <?php else: ?>
        <form method="POST">
            <div class="table-responsive card shadow-sm">
                <table class="table align-middle mb-0">
                    <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th></tr></thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo h($item['name']); ?></td>
                                <td><?php echo h(peso($item['price'])); ?></td>
                                <td><input type="number" class="form-control" name="qty[<?php echo (int)$item['id']; ?>]" value="<?php echo (int)$item['quantity']; ?>" min="0" max="<?php echo (int)$item['stock']; ?>"></td>
                                <td><?php echo h(peso($item['subtotal'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot><tr><th colspan="3">Total</th><th><?php echo h(peso($total)); ?></th></tr></tfoot>
                </table>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button name="update_cart" class="btn btn-outline-secondary">Update Cart</button>
                <a href="checkout.php" class="btn btn-primary">Checkout</a>
            </div>
        </form>
    <?php endif; ?>
</section>
<?php include("include/footer.php"); ?>
