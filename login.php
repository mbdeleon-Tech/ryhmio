<?php
require("db.php");
require("functions.php");
$page_title = "Login - Rhymio";
$message = "";
$notice = isset($_SESSION['login_notice']) ? $_SESSION['login_notice'] : "";
unset($_SESSION['login_notice']);

// Logged-in admins should go straight to the dashboard unless they log out first.
if (is_admin()) {
    header("Location: admin/dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_raw = isset($_POST['email']) ? trim($_POST['email']) : "";
    $password = isset($_POST['password']) ? trim($_POST['password']) : "";

    if ($email_raw === "" || $password === "") {
        $message = "Invalid username or password";
    } else {
        $email = clean_input($conn, $email_raw);
        $result = mysqli_query($conn, "SELECT id, complete_name, email, password, role, is_confirmed FROM users WHERE email='$email' LIMIT 1");

        if ($result && mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            if (password_verify($password, $user['password']) && (int)$user['is_confirmed'] === 1) {
                session_regenerate_id(true);

                // Store only the user details needed by the website. Never store the password.
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['complete_name'] = $user['complete_name'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === "admin") {
                    $_SESSION['admin_id'] = (int)$user['id'];
                    $_SESSION['admin_name'] = $user['complete_name'];
                    $_SESSION['admin_username'] = $user['email'];
                    $_SESSION['admin_role'] = $user['role'];
                }

                audit_log($conn, "Login", $user['complete_name'] . " logged in");
                header($user['role'] === "admin" ? "Location: admin/dashboard.php" : "Location: store.php");
                exit;
            }
        }

        if (!$message) {
            $message = "Invalid username or password";
        }
    }
}

include("include/header.php");
?>
<section class="container py-5 form-shell login-shell">
    <div class="w-100">
        <p class="text-uppercase text-primary fw-bold mb-1">Account access</p>
        <h1 class="section-title">Login</h1>
        <p class="text-muted">Sign in to order instruments.</p>
        <?php if ($notice): ?><div class="alert alert-info"><?php echo h($notice); ?></div><?php endif; ?>
        <?php if ($message): ?><div class="alert alert-danger"><?php echo h($message); ?></div><?php endif; ?>
        <form method="POST" class="card p-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control mb-3" required>
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control mb-3" required>
            <button class="btn btn-primary">Login</button>
            <a href="registration.php" class="mt-3 d-inline-block">Create a buyer account</a>
        </form>
    </div>
</section>
<?php include("include/footer.php"); ?>
