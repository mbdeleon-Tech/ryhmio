<?php
$asset_prefix = "../";
require("../db.php");
require("../functions.php");
require_admin();
$page_title = "Admin Users - Rhymio";
$message = "";
$message_type = "success";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $complete_name_raw = isset($_POST['complete_name']) ? trim($_POST['complete_name']) : "";
    $email_raw = isset($_POST['email']) ? trim($_POST['email']) : "";
    $contact_raw = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : "";
    $address_raw = isset($_POST['complete_address']) ? trim($_POST['complete_address']) : "";
    $password = isset($_POST['password']) ? trim($_POST['password']) : "";

    if ($complete_name_raw === "" || $email_raw === "" || $contact_raw === "" || $address_raw === "" || ($id === 0 && $password === "")) {
        $message = "Please complete all required fields.";
        $message_type = "danger";
    } elseif (!filter_var($email_raw, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $message_type = "danger";
    } elseif (!preg_match('/^[0-9]{11}$/', $contact_raw)) {
        $message = "Contact number must contain exactly 11 digits.";
        $message_type = "danger";
    } else {
        $complete_name = clean_input($conn, $complete_name_raw);
        $email = clean_input($conn, $email_raw);
        $contact = clean_input($conn, $contact_raw);
        $address = clean_input($conn, $address_raw);

        if ($id > 0) {
            $password_sql = "";
            if ($password !== "") {
                $hashed = clean_input($conn, password_hash($password, PASSWORD_DEFAULT));
                $password_sql = ", password='$hashed'";
            }
            mysqli_query($conn, "UPDATE users SET complete_name='$complete_name', email='$email', contact_number='$contact', complete_address='$address', role='admin', is_confirmed=1 $password_sql WHERE id=$id");
            audit_log($conn, "Modify admin user", "Updated admin user $complete_name");
            $message = "Admin user updated.";
        } else {
            $hashed = clean_input($conn, password_hash($password, PASSWORD_DEFAULT));
            mysqli_query($conn, "INSERT INTO users (complete_name, email, password, complete_address, contact_number, role, is_confirmed)
                                 VALUES ('$complete_name', '$email', '$hashed', '$address', '$contact', 'admin', 1)");
            audit_log($conn, "Add admin user", "Added admin user $complete_name");
            $message = "Admin user added.";
        }
    }
}

$edit = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$edit_id AND role='admin'"));
}
$admins = mysqli_query($conn, "SELECT * FROM users WHERE role='admin' ORDER BY complete_name");
include("../include/header.php");
?>
<section class="container py-5 admin-shell">
    <div class="row">
        <?php include("sidebar.php"); ?>
        <div class="col-lg-9">
            <h1 class="section-title">Add or Modify Admin Users</h1>
            <?php if ($message): ?><div class="alert alert-<?php echo h($message_type); ?>"><?php echo h($message); ?></div><?php endif; ?>
            <form method="POST" class="card p-4 mb-4">
                <input type="hidden" name="id" value="<?php echo $edit ? (int)$edit['id'] : 0; ?>">
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Complete name</label><input class="form-control" name="complete_name" value="<?php echo $edit ? h($edit['complete_name']) : ''; ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?php echo $edit ? h($edit['email']) : ''; ?>" required></div>
                    <div class="col-md-6"><label class="form-label">Password</label><input type="password" class="form-control" name="password" <?php echo $edit ? '' : 'required'; ?>></div>
                    <div class="col-md-6"><label class="form-label">Contact number</label><input type="tel" class="form-control" name="contact_number" inputmode="numeric" pattern="[0-9]{11}" minlength="11" maxlength="11" title="Enter exactly 11 digits." oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" value="<?php echo $edit ? h($edit['contact_number']) : ''; ?>" required></div>
                    <div class="col-12"><label class="form-label">Address</label><textarea class="form-control" name="complete_address" required><?php echo $edit ? h($edit['complete_address']) : ''; ?></textarea></div>
                </div>
                <button class="btn btn-primary mt-3"><?php echo $edit ? 'Update Admin' : 'Add Admin'; ?></button>
            </form>
            <div class="table-responsive card">
                <table class="table mb-0">
                    <thead><tr><th>Name</th><th>Email</th><th>Contact</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($admins)): ?>
                            <tr><td><?php echo h($row['complete_name']); ?></td><td><?php echo h($row['email']); ?></td><td><?php echo h($row['contact_number']); ?></td><td><a class="btn btn-sm btn-outline-secondary" href="users.php?edit=<?php echo (int)$row['id']; ?>">Modify</a></td></tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
<?php include("../include/footer.php"); ?>
