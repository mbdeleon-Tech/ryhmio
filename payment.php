<?php
require("db.php");
require("functions.php");
require_buyer_login();
$page_title = "Payment - Rhymio";
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['checkout'], $_SESSION['cart'])) {
    $checkout = $_SESSION['checkout'];
    $user_id = current_user_id();
    $user_sql = $user_id ? $user_id : "NULL";
    $total = (float)$checkout['total'];
    $payment_method_raw = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : "";
    $allowed_payment_methods = ["Cash on Delivery", "Bank Transfer", "Over-the-counter"];
    if (!in_array($payment_method_raw, $allowed_payment_methods, true)) {
        $payment_method_raw = "Cash on Delivery";
    }
    $payment_method = clean_input($conn, $payment_method_raw);
    mysqli_query($conn, "INSERT INTO orders (user_id, customer_name, customer_email, delivery_address, contact_number, total_amount, payment_method, order_status)
                         VALUES ($user_sql, '{$checkout['name']}', '{$checkout['email']}', '{$checkout['address']}', '{$checkout['contact']}', $total, '$payment_method', 'Pending')");
    $order_id = mysqli_insert_id($conn);
    foreach ($_SESSION['cart'] as $product_id => $qty) {
        $product_id = (int)$product_id;
        $qty = (int)$qty;
        $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT price, name FROM products WHERE id=$product_id"));
        $price = (float)$product['price'];
        mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES ($order_id, $product_id, $qty, $price)");
        mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id=$product_id");
    }
    audit_log($conn, "Payment page", "Order #$order_id was placed using $payment_method");
    unset($_SESSION['cart'], $_SESSION['checkout']);
    $message = "Order #$order_id has been recorded. This educational demo does not process a live payment.";
}

include("include/header.php");
?>
<section class="container py-5 form-shell">
    <h1 class="section-title">Payment</h1>
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo h($message); ?></div>
        <a href="store.php" class="btn btn-primary">Back to Store</a>
    <?php elseif (!isset($_SESSION['checkout'])): ?>
        <div class="alert alert-info">Please complete checkout first.</div>
    <?php else: ?>
        <div class="card p-4 shadow-sm">
            <p>Total amount: <strong><?php echo h(peso($_SESSION['checkout']['total'])); ?></strong></p>
            <form method="POST">
                <label class="form-label">Payment method</label>
                <select name="payment_method" class="form-select mb-3" required>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                    <option value="Over-the-counter">Over-the-counter</option>
                </select>
                <button class="btn btn-primary">Place Order</button>
            </form>
        </div>
    <?php endif; ?>
</section>
<?php include("include/footer.php"); ?>
