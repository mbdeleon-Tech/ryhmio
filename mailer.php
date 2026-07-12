<?php
require("mailer/PHPMailer.php");
require("mailer/SMTP.php");
require("mailer/Exception.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!file_exists(__DIR__ . "/mail_config.php")) {
    throw new Exception("mail_config.php is missing.");
}

require(__DIR__ . "/mail_config.php");

if (!defined("MAIL_HOST") || !defined("MAIL_USERNAME") || !defined("MAIL_PASSWORD") ||
    !defined("MAIL_PORT") || !defined("MAIL_ENCRYPTION") || !defined("MAIL_FROM_EMAIL") ||
    !defined("MAIL_FROM_NAME") || !defined("SITE_URL")) {
    throw new Exception("mail_config.php is incomplete.");
}

function send_confirmation($to_email, $to_name, $token) {
    if (MAIL_USERNAME === "your_email@gmail.com" || MAIL_PASSWORD === "your_gmail_app_password") {
        throw new Exception("mail_config.php still has placeholder Gmail values.");
    }

    $mailer = new PHPMailer(true);

    $mailer->isSMTP();
    $mailer->Host = MAIL_HOST;
    $mailer->SMTPAuth = true;
    $mailer->Username = MAIL_USERNAME;
    $mailer->Password = MAIL_PASSWORD;
    $mailer->SMTPSecure = MAIL_ENCRYPTION;
    $mailer->Port = MAIL_PORT;
    $mailer->CharSet = "UTF-8";
    $mailer->isHTML(true);

    $confirm_link = rtrim(SITE_URL, "/") . "/confirm.php?token=" . urlencode($token);
    $safe_name = htmlspecialchars($to_name, ENT_QUOTES, "UTF-8");
    $safe_link = htmlspecialchars($confirm_link, ENT_QUOTES, "UTF-8");
    $mailer->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
    $mailer->addAddress($to_email, $to_name);
    $mailer->Subject = "Confirm your Rhymio account";
    $mailer->Body = "
        <p>Dear <strong>$safe_name</strong>,</p>
        <p>Thank you for registering at Rhymio Music Store.</p>
        <p><a href='$safe_link' style='padding:10px 18px;background:#0f766e;color:white;text-decoration:none;border-radius:6px'>Confirm Registration</a></p>
        <p>If the button does not work, copy this link:</p>
        <p><a href='$safe_link'>$safe_link</a></p>
    ";

    $mailer->send();
}
?>
