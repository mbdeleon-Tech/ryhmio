<?php
require("functions.php");

$page_title = "Register - Rhymio";
$message = "";
$type = "";
$complete_name = "";
$email = "";
$region_raw = "";
$province_raw = "";
$city_raw = "";
$barangay_raw = "";
$postal_code = "";
$street_raw = "";
$contact_number = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require("db.php");

    $complete_name = clean_input($conn, isset($_POST['complete_name']) ? $_POST['complete_name'] : "");
    $email = clean_input($conn, isset($_POST['email']) ? $_POST['email'] : "");
    $password = isset($_POST['password']) ? trim($_POST['password']) : "";
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : "";
    $region_raw = isset($_POST['region']) ? trim($_POST['region']) : "";
    $province_raw = isset($_POST['province']) ? trim($_POST['province']) : "";
    $city_raw = isset($_POST['city']) ? trim($_POST['city']) : "";
    $barangay_raw = isset($_POST['barangay']) ? trim($_POST['barangay']) : "";
    $postal_code = isset($_POST['postal_code']) ? trim($_POST['postal_code']) : "";
    $street_raw = isset($_POST['street_address']) ? trim($_POST['street_address']) : "";
    $contact_number = clean_input($conn, isset($_POST['contact_number']) ? $_POST['contact_number'] : "");

    if ($complete_name === "" || $email === "" || $password === "" || $confirm_password === "" || $region_raw === "" || $province_raw === "" || $city_raw === "" || $barangay_raw === "" || $postal_code === "" || $street_raw === "" || $contact_number === "") {
        $message = "Please complete all required fields.";
        $type = "danger";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $type = "danger";
    } elseif (!preg_match('/^[0-9]{11}$/', $contact_number)) {
        $message = "Contact number must contain exactly 11 digits.";
        $type = "danger";
    } elseif (!preg_match('/^[0-9]{4}$/', $postal_code)) {
        $message = "Postal code must contain exactly 4 digits.";
        $type = "danger";
    } elseif (!valid_philippine_address_selection($region_raw, $province_raw, $city_raw)) {
        $message = "Please select a valid region, province, and city or municipality.";
        $type = "danger";
    } elseif ($password !== $confirm_password) {
        $message = "The passwords do not match.";
        $type = "danger";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $type = "danger";
    } else {
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
        if (!$check) {
            $message = "Registration is unavailable. Please check the database setup.";
            $type = "danger";
        } elseif (mysqli_num_rows($check) > 0) {
            $message = "Email address is already registered.";
            $type = "danger";
        } else {
            $address_text = format_complete_address($street_raw, $barangay_raw, $city_raw, $province_raw, $region_raw, $postal_code);
            $address = clean_input($conn, $address_text);
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $token = function_exists("random_bytes") ? bin2hex(random_bytes(32)) : md5(uniqid($email, true));
            $sql = "INSERT INTO users (complete_name, email, password, complete_address, contact_number, role, confirm_token)
                    VALUES ('$complete_name', '$email', '$hashed', '$address', '$contact_number', 'buyer', '$token')";
            if (mysqli_query($conn, $sql)) {
                audit_log($conn, "Buyer registration", "$complete_name registered with $email");
                try {
                    require_once("mailer.php");
                    send_confirmation($email, $complete_name, $token);
                    $message = "Registration successful. Please check your email to confirm your account.";
                    $type = "success";
                } catch (Exception $e) {
                    $message = "Registration saved, but email sending failed. Please check mail_config.php before hosting.";
                    $type = "warning";
                }
            } else {
                $message = "Registration could not be completed. Please check the database setup.";
                $type = "danger";
            }
        }
    }
}

include("include/header.php");
?>
<section class="container py-5 form-shell">
    <h1 class="section-title">Create Buyer Account</h1>
    <p class="text-muted">Fill in your details to start buying instruments.</p>
    <?php if ($message): ?><div class="alert alert-<?php echo h($type); ?>"><?php echo h($message); ?></div><?php endif; ?>
    <form method="POST" class="card p-4 shadow-sm">
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Complete name</label>
                <input type="text" name="complete_name" class="form-control" value="<?php echo h($complete_name); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" value="<?php echo h($email); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Confirm password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <fieldset class="col-12">
                <legend class="h6 mb-3">Complete address</legend>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Region</label>
                        <select name="region" class="form-select" data-address-region required>
                            <option value="" disabled<?php echo $region_raw === "" ? " selected" : ""; ?>>Select region</option>
                            <?php foreach (philippine_regions() as $region): ?>
                                <option value="<?php echo h($region); ?>"<?php echo $region_raw === $region ? " selected" : ""; ?>><?php echo h($region); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Province</label>
                        <select name="province" class="form-select" data-address-province data-selected="<?php echo h($province_raw); ?>" required disabled>
                            <option value="" selected disabled>Select province</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">City or municipality</label>
                        <select name="city" class="form-select" data-address-city data-selected="<?php echo h($city_raw); ?>" required disabled>
                            <option value="" selected disabled>Select city or municipality</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Barangay</label>
                        <input type="text" name="barangay" class="form-control" value="<?php echo h($barangay_raw); ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Postal code</label>
                        <input type="text" name="postal_code" class="form-control" value="<?php echo h($postal_code); ?>" inputmode="numeric" pattern="[0-9]{4}" minlength="4" maxlength="4" title="Enter exactly 4 digits." oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,4)" required>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Street name, building, and house number</label>
                        <input type="text" name="street_address" class="form-control" value="<?php echo h($street_raw); ?>" required>
                    </div>
                </div>
            </fieldset>
            <div class="col-md-6">
                <label class="form-label">Contact number</label>
                <input type="tel" name="contact_number" class="form-control" value="<?php echo h($contact_number); ?>" inputmode="numeric" pattern="[0-9]{11}" minlength="11" maxlength="11" title="Enter exactly 11 digits." oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" required>
            </div>
        </div>
        <button class="btn btn-primary mt-4">Register</button>
    </form>
</section>
<script>window.rhymioAddressOptions = <?php echo json_encode(philippine_address_options()); ?>;</script>
<script src="assets/address-selects.js"></script>
<?php include("include/footer.php"); ?>
