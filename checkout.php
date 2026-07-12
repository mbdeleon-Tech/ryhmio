<?php
require("db.php");
require("functions.php");
require_buyer_login();
$page_title = "Checkout - Rhymio";
$message = "";
$region = "";
$province = "";
$city = "";
$barangay = "";
$postal_code = "";
$street_address = "";
$contact = "";

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && count($items) > 0) {
    $name = isset($_POST['name']) ? trim($_POST['name']) : "";
    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $region = isset($_POST['region']) ? trim($_POST['region']) : "";
    $province = isset($_POST['province']) ? trim($_POST['province']) : "";
    $city = isset($_POST['city']) ? trim($_POST['city']) : "";
    $barangay = isset($_POST['barangay']) ? trim($_POST['barangay']) : "";
    $postal_code = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : "";
    $street_address = isset($_POST['street_address']) ? trim($_POST['street_address']) : "";
    $contact = isset($_POST['contact']) ? trim($_POST['contact']) : "";

    if ($name === "" || $email === "" || $region === "" || $province === "" || $city === "" || $barangay === "" || $postal_code === "" || $street_address === "" || $contact === "") {
        $message = "Please complete all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
    } elseif (!preg_match('/^[0-9]{11}$/', $contact)) {
        $message = "Contact number must contain exactly 11 digits.";
    } elseif (!preg_match('/^[0-9]{4}$/', $postal_code)) {
        $message = "Postal code must contain exactly 4 digits.";
    } elseif (!valid_philippine_address_selection($region, $province, $city)) {
        $message = "Please select a valid region, province, and city or municipality.";
    } else {
        $address = format_complete_address($street_address, $barangay, $city, $province, $region, $postal_code);
        $_SESSION['checkout'] = [
            "name" => clean_input($conn, $name),
            "email" => clean_input($conn, $email),
            "address" => clean_input($conn, $address),
            "contact" => clean_input($conn, $contact),
            "total" => $total
        ];
        audit_log($conn, "Checkout", "Checkout details were entered");
        header("Location: payment.php");
        exit;
    }
}

include("include/header.php");
?>
<section class="container py-5 form-shell">
    <h1 class="section-title">Checkout</h1>
    <?php if (count($items) === 0): ?>
        <div class="alert alert-info">Your cart is empty.</div>
    <?php else: ?>
        <?php if ($message): ?><div class="alert alert-danger"><?php echo h($message); ?></div><?php endif; ?>
        <div class="card p-4 mb-4">
            <?php foreach ($items as $item): ?>
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span><?php echo h($item['name']); ?> x <?php echo (int)$item['quantity']; ?></span>
                    <strong><?php echo h(peso($item['subtotal'])); ?></strong>
                </div>
            <?php endforeach; ?>
            <div class="d-flex justify-content-between pt-3"><strong>Total</strong><strong><?php echo h(peso($total)); ?></strong></div>
        </div>
        <form method="POST" class="card p-4 shadow-sm">
            <label class="form-label">Complete name</label>
            <input type="text" name="name" class="form-control mb-3" value="<?php echo isset($_SESSION['complete_name']) ? h($_SESSION['complete_name']) : ''; ?>" required>
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control mb-3" required>
            <fieldset class="mb-3">
                <legend class="h6 mb-3">Delivery address</legend>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Region</label>
                        <select name="region" class="form-select" data-address-region required>
                            <option value="" disabled<?php echo $region === "" ? " selected" : ""; ?>>Select region</option>
                            <?php foreach (philippine_regions() as $region_option): ?>
                                <option value="<?php echo h($region_option); ?>"<?php echo $region === $region_option ? " selected" : ""; ?>><?php echo h($region_option); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Province</label>
                        <select name="province" class="form-select" data-address-province data-selected="<?php echo h($province); ?>" required disabled>
                            <option value="" selected disabled>Select province</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City or municipality</label>
                        <select name="city" class="form-select" data-address-city data-selected="<?php echo h($city); ?>" required disabled>
                            <option value="" selected disabled>Select city or municipality</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Barangay</label>
                        <input type="text" name="barangay" class="form-control" value="<?php echo h($barangay); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Postal code</label>
                        <input type="text" name="postal_code" class="form-control" value="<?php echo h($postal_code); ?>" inputmode="numeric" pattern="[0-9]{4}" minlength="4" maxlength="4" title="Enter exactly 4 digits." oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,4)" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Street name, building, and house number</label>
                        <input type="text" name="street_address" class="form-control" value="<?php echo h($street_address); ?>" required>
                    </div>
                </div>
            </fieldset>
            <label class="form-label">Contact number</label>
            <input type="tel" name="contact" class="form-control mb-3" value="<?php echo h($contact); ?>" inputmode="numeric" pattern="[0-9]{11}" minlength="11" maxlength="11" title="Enter exactly 11 digits." oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" required>
            <button class="btn btn-primary">Proceed to Payment</button>
        </form>
    <?php endif; ?>
</section>
<script>window.rhymioAddressOptions = <?php echo json_encode(philippine_address_options()); ?>;</script>
<script src="assets/address-selects.js"></script>
<?php include("include/footer.php"); ?>
