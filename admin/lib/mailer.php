<?php
/**
 * ADEPTIO – Thin wrapper around PHPMailer for sending an email via Gmail SMTP.
 *
 * Returns [success(bool), error(string|null)]. Never throws, so the caller can
 * log + flash cleanly.
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../../lib/PHPMailer/SMTP.php';

/**
 * @return array{0: bool, 1: ?string}
 */
function send_mail(string $toEmail, string $toName, string $subject, string $body): array
{
    $cfg = require __DIR__ . '/../config/mail.php';

    // Guard against the unconfigured placeholder values.
    if (
        $cfg['username'] === 'YOUR_ADDRESS@gmail.com'
        || $cfg['password'] === 'YOUR_APP_PASSWORD'
        || $cfg['username'] === ''
        || $cfg['password'] === ''
    ) {
        return [false, "Configuration email manquante : renseignez admin/config/mail.php (adresse Gmail + App Password)."];
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = $cfg['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $cfg['username'];
        $mail->Password   = str_replace(' ', '', $cfg['password']); // App Passwords are shown with spaces
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = (int) $cfg['port'];
        $mail->CharSet    = 'UTF-8';

        // Gmail forces the From to the authenticated account; set it to match.
        $mail->setFrom($cfg['username'], $cfg['from_name']);
        $mail->addReplyTo($cfg['username'], $cfg['from_name']);
        $mail->addAddress($toEmail, $toName !== '' ? $toName : $toEmail);

        $mail->Subject = $subject;
        $mail->Body    = $body;          // plain text
        $mail->isHTML(false);

        $mail->send();

        return [true, null];
    } catch (Exception $e) {
        // PHPMailer puts the useful message in ErrorInfo.
        return [false, $mail->ErrorInfo ?: $e->getMessage()];
    }
}
