<?php
if (!file_exists(__DIR__ . "/db_config.php")) {
    die("
        <div style='font-family:Arial,sans-serif;max-width:720px;margin:40px auto;padding:24px;border:1px solid #ddd;border-radius:8px'>
            <h2>Database configuration is missing</h2>
            <p>Create <strong>db_config.php</strong> in the main htdocs folder using the database details from the InfinityFree control panel.</p>
        </div>
    ");
}

require_once(__DIR__ . "/db_config.php");

if (!defined("DB_HOST") || !defined("DB_USER") || !defined("DB_PASSWORD") || !defined("DB_NAME")) {
    die("
        <div style='font-family:Arial,sans-serif;max-width:720px;margin:40px auto;padding:24px;border:1px solid #ddd;border-radius:8px'>
            <h2>Database configuration is incomplete</h2>
            <p>Check that <strong>db_config.php</strong> defines DB_HOST, DB_USER, DB_PASSWORD, and DB_NAME.</p>
        </div>
    ");
}

mysqli_report(MYSQLI_REPORT_OFF);
$conn = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if (!$conn) {
    die("
        <div style='font-family:Arial,sans-serif;max-width:720px;margin:40px auto;padding:24px;border:1px solid #ddd;border-radius:8px'>
            <h2>Database connection failed</h2>
            <p>Please check the database settings in <strong>db_config.php</strong> and make sure the SQL file was imported.</p>
        </div>
    ");
}

mysqli_set_charset($conn, "utf8mb4");
?>
