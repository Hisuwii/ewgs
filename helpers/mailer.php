<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

/**
 * Send teacher portal credentials via email using PHPMailer + Gmail SMTP.
 *
 * Configure SMTP credentials in config/mail.php.
 */
function sendTeacherCredentials($email, $fname, $lname, $password, $isReset = false) {
    $name   = htmlspecialchars($fname . ' ' . $lname);
    $portal = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
            . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/ewgs/';

    $subject    = $isReset ? 'Your EWGS Password Has Been Reset' : 'Welcome to EWGS — Your Account is Ready';
    $headline   = $isReset ? 'Password Reset' : 'Account Created';
    $intro      = $isReset
        ? "Your password for the <strong>Elementary Web Grading System (EWGS)</strong> has been reset by your administrator. Use the temporary password below to log in."
        : "Your teacher account has been created in the <strong>Elementary Web Grading System (EWGS)</strong>. Use the credentials below to log in for the first time.";

    $body = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background:#eef2ee;font-family:'Segoe UI',Arial,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#eef2ee;padding:40px 0;">
    <tr><td align="center">
      <table width="580" cellpadding="0" cellspacing="0"
             style="background:#ffffff;border-radius:10px;overflow:hidden;box-shadow:0 4px 16px rgba(0,0,0,0.10);max-width:580px;">

        <!-- Header -->
        <tr>
          <td style="background:linear-gradient(135deg,#3a5a3a 0%,#4b6b4b 100%);padding:32px 40px;">
            <p style="margin:0 0 4px;color:rgba(255,255,255,0.7);font-size:12px;letter-spacing:2px;text-transform:uppercase;">Elementary Web Grading System</p>
            <h1 style="margin:0;color:#ffffff;font-size:24px;font-weight:700;letter-spacing:-0.3px;">{$headline}</h1>
          </td>
        </tr>

        <!-- Body -->
        <tr>
          <td style="padding:36px 40px 28px;color:#2d2d2d;font-size:15px;line-height:1.7;">
            <p style="margin:0 0 16px;">Dear <strong style="color:#3a5a3a;">{$name}</strong>,</p>
            <p style="margin:0 0 24px;">{$intro}</p>

            <!-- Credentials Box -->
            <table cellpadding="0" cellspacing="0" width="100%"
                   style="background:#f5f8f5;border:1px solid #c8d8c8;border-radius:8px;margin:0 0 28px;">
              <tr>
                <td style="padding:20px 24px;">
                  <p style="margin:0 0 14px;font-size:12px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#5a7a5a;">Your Login Credentials</p>
                  <table cellpadding="0" cellspacing="0" width="100%">
                    <tr>
                      <td style="padding:8px 0;border-bottom:1px solid #dde8dd;font-size:13px;color:#6b8a6b;font-weight:600;width:110px;">Email</td>
                      <td style="padding:8px 0;border-bottom:1px solid #dde8dd;font-size:15px;color:#1a1a1a;">{$email}</td>
                    </tr>
                    <tr>
                      <td style="padding:12px 0 0;font-size:13px;color:#6b8a6b;font-weight:600;vertical-align:top;">Password</td>
                      <td style="padding:12px 0 0;">
                        <span style="display:inline-block;background:#ffffff;border:2px solid #4b6b4b;border-radius:6px;
                                     padding:8px 18px;font-size:20px;font-weight:700;letter-spacing:3px;
                                     color:#2e4e2e;font-family:'Courier New',Courier,monospace;">{$password}</span>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>

            <!-- CTA Button -->
            <table cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
              <tr>
                <td style="border-radius:6px;background:#4b6b4b;">
                  <a href="{$portal}"
                     style="display:inline-block;padding:13px 32px;font-size:15px;font-weight:700;
                            color:#ffffff;text-decoration:none;letter-spacing:0.2px;">
                    Log In to EWGS &rarr;
                  </a>
                </td>
              </tr>
            </table>

            <!-- Warning Banner -->
            <table cellpadding="0" cellspacing="0" width="100%"
                   style="background:#fff8e1;border-left:4px solid #f59e0b;border-radius:0 6px 6px 0;margin:0 0 8px;">
              <tr>
                <td style="padding:14px 18px;">
                  <p style="margin:0 0 6px;font-size:13px;font-weight:700;color:#92610a;text-transform:uppercase;letter-spacing:0.5px;">&#9888; Important</p>
                  <p style="margin:0;font-size:13px;color:#7a5200;line-height:1.6;">
                    This is a <strong>temporary, system-generated password</strong>. You will be
                    <strong>required to change it immediately</strong> upon your first login —
                    you will not be able to use the system until you do.<br>
                    If you did not expect this email, contact your school administrator right away.
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>

        <!-- Footer -->
        <tr>
          <td style="background:#f5f8f5;padding:18px 40px;text-align:center;border-top:1px solid #e0e8e0;">
            <p style="margin:0;font-size:12px;color:#999;">&copy; EWGS Administration &mdash; This is an automated message, please do not reply.</p>
          </td>
        </tr>

      </table>
    </td></tr>
  </table>
</body>
</html>
HTML;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;

        $mail->setFrom(MAIL_FROM_EMAIL, MAIL_FROM_NAME);
        $mail->addAddress($email, $fname . ' ' . $lname);

        $mail->CharSet  = PHPMailer::CHARSET_UTF8;
        $mail->Encoding = PHPMailer::ENCODING_BASE64;

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        $detail = $mail->ErrorInfo;
        error_log('Mailer error: ' . $detail);
        return $detail; // return error string so callers can surface it
    }
}
