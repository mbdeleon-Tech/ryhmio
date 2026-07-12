<?php
require("db.php");
require("functions.php");
$page_title = "Confirm Account - Rhymio";
$message = "Invalid confirmation link.";
$type = "danger";

if (isset($_GET['token'])) {
    $token = clean_input($conn, $_GET['token']);
    $result = mysqli_query($conn, "SELECT id, complete_name FROM users WHERE confirm_token='$token' AND is_confirmed=0");
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        mysqli_query($conn, "UPDATE users SET is_confirmed=1, confirm_token=NULL WHERE id=" . (int)$user['id']);
        audit_log($conn, "Account confirmation", $user['complete_name'] . " confirmed an account");
        $message = "Your account has been confirmed. You may now log in.";
        $type = "success";
    }
}

include("include/header.php");
?>
<section class="container py-5">
    <div class="alert alert-<?php echo h($type); ?>"><?php echo h($message); ?></div>
    <a href="login.php" class="btn btn-primary">Go to Login</a>
</section>
<?php include("include/footer.php"); ?>
