<?php
require("db.php");
require("functions.php");

if (isset($_SESSION['user_id'])) {
    audit_log($conn, "Logout", current_user_name() . " logged out");
}

// Clear all session values and remove the session cookie before destroying it.
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), "", time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

session_destroy();
header("Location: index.php");
exit;
?>
